<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\Bid;
use App\Models\Transaction;
use App\Models\ActivityLog;
use App\Models\SystemSetting;
use App\Services\PriceSuggestionService;
use App\Events\AuctionClosed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LotController extends Controller
{
    protected PriceSuggestionService $pricer;

    public function __construct(PriceSuggestionService $pricer)
    {
        // Injected automatically by Laravel's service container
        $this->pricer = $pricer;
    }

    /** Show the create listing form — passes a price suggestion if query params provided */
    public function create(Request $request)
    {
        $suggestion = null;
        if ($request->filled('fiber_type')) {
            $suggestion = $this->pricer->breakdown(
                $request->fiber_type,
                (int) $request->get('purity', 85),
                (bool) $request->get('color_sorted', false)
            );
        }
        return view('seller.create-lot', compact('suggestion'));
    }

    /**
     * AJAX endpoint: returns price suggestion as JSON.
     * Called by the create-lot form as the seller fills in fiber type/purity.
     */
    public function suggestPrice(Request $request)
    {
        // Support both naming schemes (purity and fiber_purity_pct) gracefully
        if (!$request->has('purity') && $request->has('fiber_purity_pct')) {
            $request->merge(['purity' => $request->fiber_purity_pct]);
        }

        $request->validate([
            'fiber_type'   => ['required', 'string'],
            'purity'       => ['required', 'integer', 'min:1', 'max:100'],
            'color_sorted' => ['boolean'],
        ]);

        $breakdown = $this->pricer->breakdown(
            $request->fiber_type,
            (int) $request->purity,
            (bool) $request->get('color_sorted', false)
        );

        return response()->json($breakdown);
    }


    /** Store a newly created lot */
    public function store(Request $request)
    {
        $request->validate([
            'category'        => ['required', 'in:cutting_scraps,yarn_ends,rejected_batches,selvedge'],
            'fiber_type'      => ['required', 'string', 'max:100'],
            'fiber_purity_pct'=> ['required', 'integer', 'min:1', 'max:100'],
            'color_sorted'    => ['boolean'],
            'color_description' => ['nullable', 'string', 'max:255'],
            'weight_kg'       => ['required', 'numeric', 'min:100', 'max:25000'],
            'base_price'      => ['required', 'numeric', 'min:1'],
            'auction_ends_at' => ['required', 'date', 'after:now'],
            'images'          => ['nullable', 'array', 'max:5'],
            'images.*'        => ['image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ]);

        $lot = Lot::create([
            'seller_id'        => auth()->id(),
            'category'         => $request->category,
            'fiber_type'       => $request->fiber_type,
            'fiber_purity_pct' => $request->fiber_purity_pct,
            'color_sorted'     => $request->boolean('color_sorted'),
            'color_description'=> $request->color_description,
            'weight_kg'        => $request->weight_kg,
            'base_price'       => $request->base_price,
            'auction_ends_at'  => $request->auction_ends_at,
            'status'           => 'pending_review', // Admin must verify before going live
        ]);

        // Handle image uploads (stored locally in storage/app/public/lots)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store("lots/{$lot->id}", 'public');
                $lot->images()->create([
                    'file_path'  => $path,
                    'file_name'  => $image->getClientOriginalName(),
                    'file_size'  => $image->getSize(),
                    'sort_order' => $index,
                ]);
            }
        }

        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'lot_created',
            'subject_type' => Lot::class,
            'subject_id'   => $lot->id,
            'description'  => "Created lot {$lot->lot_number}",
            'ip_address'   => $request->ip(),
        ]);

        return redirect()->route('seller.ledger')
            ->with('success', "Lot {$lot->lot_number} submitted for verification.");
    }

    /** Seller's inventory ledger — all their lots */
    public function ledger(Request $request)
    {
        $user = auth()->user();
        $status = $request->get('status', 'active');

        $lots = Lot::where('seller_id', $user->id)
            ->with(['highestBid', 'bids'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(20);

        // Tab counts
        $counts = [
            'active'         => Lot::where('seller_id', $user->id)->where('status', 'active')->count(),
            'pending_review' => Lot::where('seller_id', $user->id)->where('status', 'pending_review')->count(),
            'settled'        => Lot::where('seller_id', $user->id)->where('status', 'settled')->count(),
        ];

        return view('seller.ledger', compact('lots', 'counts', 'status'));
    }

    /** Seller view of a single lot with all its bids */
    public function show(Lot $lot)
    {
        // Gate: only the seller who owns this lot can see it
        abort_if($lot->seller_id !== auth()->id(), 403);

        $lot->load(['bids.buyer', 'images', 'highestBid.buyer']);

        return view('seller.lot-details', compact('lot'));
    }

    /** Seller accepts the highest bid — converts to Transaction */
    public function acceptBid(Request $request, Lot $lot)
    {
        abort_if($lot->seller_id !== auth()->id(), 403);
        abort_if($lot->status !== 'active', 422, 'Lot is not active.');
        abort_if(!$lot->highest_bid_id, 422, 'No bids on this lot yet.');

        $bid = Bid::findOrFail($lot->highest_bid_id);
        $commission = SystemSetting::get('platform_commission', 1.50);

        $subtotal = $bid->amount * $lot->weight_kg;
        $commissionAmount = $commission * $lot->weight_kg;

        $transaction = Transaction::create([
            'lot_id'            => $lot->id,
            'bid_id'            => $bid->id,
            'buyer_id'          => $bid->buyer_id,
            'seller_id'         => $lot->seller_id,
            'agreed_price'      => $bid->amount,
            'actual_weight_kg'  => $lot->weight_kg,
            'subtotal'          => $subtotal,
            'commission_amount' => $commissionAmount,
            'total_amount'      => $subtotal + $commissionAmount,
            'payment_status'    => 'pending',
            'logistics_status'  => 'awaiting_payment',
        ]);

        // Update lot status
        $lot->update(['status' => 'awarded']);
        $bid->update(['status' => 'won']);

        // Mark all other bids as lost
        Bid::where('lot_id', $lot->id)->where('id', '!=', $bid->id)->update(['status' => 'lost']);

        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'bid_accepted',
            'subject_type' => Transaction::class,
            'subject_id'   => $transaction->id,
            'description'  => "Accepted bid of ₹{$bid->amount}/kg on lot {$lot->lot_number}",
            'ip_address'   => $request->ip(),
        ]);

        // Notify all clients watching the bidding room that the auction is closed
        broadcast(new AuctionClosed($lot, $transaction));

        return redirect()->route('settlement.show', $transaction)
            ->with('success', 'Bid accepted. Settlement invoice created.');
    }

    /** Seller cancels a lot that hasn't been settled yet */
    public function cancel(Request $request, Lot $lot)
    {
        abort_if($lot->seller_id !== auth()->id(), 403);
        abort_if(in_array($lot->status, ['settled', 'awarded']), 422, 'Cannot cancel a settled or awarded lot.');

        $lot->update(['status' => 'cancelled']);

        // Release all bids
        Bid::where('lot_id', $lot->id)->update(['status' => 'cancelled']);

        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'lot_cancelled',
            'subject_type' => Lot::class,
            'subject_id'   => $lot->id,
            'description'  => "Cancelled lot {$lot->lot_number}",
            'ip_address'   => $request->ip(),
        ]);

        return redirect()->route('seller.ledger')
            ->with('success', "Lot {$lot->lot_number} has been cancelled.");
    }
}
