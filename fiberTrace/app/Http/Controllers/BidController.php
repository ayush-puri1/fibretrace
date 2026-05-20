<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\Bid;
use App\Models\ActivityLog;
use App\Models\SystemSetting;
use App\Events\BidPlaced;
use Illuminate\Http\Request;

class BidController extends Controller
{
    /** Show the bidding room for a specific lot */
    public function room(Lot $lot)
    {
        abort_if($lot->status !== 'active', 404);
        abort_if($lot->auction_ends_at < now(), 410, 'This auction has ended.');

        $lot->load(['seller', 'images', 'bids' => function ($q) {
            // Only show bid count, not WHO bid (blind bidding — PII protection)
            $q->orderByDesc('amount');
        }, 'highestBid']);

        // Did the current buyer already place a bid?
        $myBid = Bid::where('lot_id', $lot->id)->where('buyer_id', auth()->id())->first();

        // Minimum next bid = highest bid + ₹0.50, or base_price if no bids
        $minimumBid = $lot->highestBid
            ? round($lot->highestBid->amount + 0.50, 2)
            : $lot->base_price;

        return view('buyer.bidding-room', compact('lot', 'myBid', 'minimumBid'));
    }

    /** Place or update a bid */
    public function place(Request $request, Lot $lot)
    {
        abort_if($lot->status !== 'active', 422, 'Lot is no longer active.');
        abort_if($lot->auction_ends_at < now(), 422, 'Auction has ended.');

        // Sellers cannot bid on their own lots
        abort_if($lot->seller_id === auth()->id(), 403, 'You cannot bid on your own listing.');

        $request->validate([
            'amount' => ['required', 'numeric', 'min:' . ($lot->highestBid ? $lot->highestBid->amount + 0.01 : $lot->base_price)],
        ]);

        // Deactivate previous bid by this buyer on this lot (they're updating their bid)
        Bid::where('lot_id', $lot->id)
            ->where('buyer_id', auth()->id())
            ->update(['status' => 'outbid']);

        // Create the new bid
        $bid = Bid::create([
            'lot_id'   => $lot->id,
            'buyer_id' => auth()->id(),
            'amount'   => $request->amount,
            'status'   => 'active',
        ]);

        // Update the lot's highest_bid_id
        $lot->update(['highest_bid_id' => $bid->id]);

        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'bid_placed',
            'subject_type' => Bid::class,
            'subject_id'   => $bid->id,
            'description'  => "Placed bid of ₹{$bid->amount}/kg on lot {$lot->lot_number}",
            'ip_address'   => $request->ip(),
        ]);

        // Broadcast real-time update to all clients watching this lot's room
        broadcast(new BidPlaced($bid, $lot->fresh()))->toOthers();

        return redirect()->route('buyer.room', $lot)
            ->with('success', "Bid of ₹{$bid->amount}/kg placed successfully.");
    }

    /** Buyer's bids ledger — all their bids across lots */
    public function ledger()
    {
        $bids = Bid::where('buyer_id', auth()->id())
            ->with(['lot' => fn($q) => $q->with('highestBid', 'seller')])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('buyer.bids-ledger', compact('bids'));
    }

    /** Buyer cancels an active bid */
    public function cancelBid(Request $request, Bid $bid)
    {
        abort_if($bid->buyer_id !== auth()->id(), 403);
        abort_if($bid->status !== 'active', 422, 'Only active bids can be cancelled.');

        // Cannot cancel if this is the highest bid and the lot is still active
        $lot = $bid->lot;
        if ($lot->highest_bid_id === $bid->id && $lot->status === 'active') {
            return back()->with('error', 'You cannot cancel the current highest bid. Outbid it or let the auction close.');
        }

        $bid->update(['status' => 'cancelled']);

        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'bid_cancelled',
            'subject_type' => Bid::class,
            'subject_id'   => $bid->id,
            'description'  => "Cancelled bid #{$bid->id} on lot {$lot->lot_number}",
            'ip_address'   => $request->ip(),
        ]);

        return redirect()->route('buyer.bids')
            ->with('success', 'Bid cancelled successfully.');
    }
}
