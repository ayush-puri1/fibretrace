<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\Bid;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    /**
     * Public (authenticated) auction floor — all active lots with filters.
     */
    public function index(Request $request)
    {
        $query = Lot::where('status', 'active')
            ->with(['seller', 'highestBid', 'images'])
            ->where('auction_ends_at', '>', now());

        // Filter by fiber type
        if ($request->filled('fiber')) {
            $query->where('fiber_type', 'like', '%' . $request->fiber . '%');
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by min/max weight
        if ($request->filled('min_weight')) {
            $query->where('weight_kg', '>=', $request->min_weight);
        }
        if ($request->filled('max_weight')) {
            $query->where('weight_kg', '<=', $request->max_weight);
        }

        // Sort
        $sort = $request->get('sort', 'ending_soon');
        match($sort) {
            'price_asc'    => $query->orderBy('base_price', 'asc'),
            'price_desc'   => $query->orderBy('base_price', 'desc'),
            'weight_desc'  => $query->orderBy('weight_kg', 'desc'),
            default        => $query->orderBy('auction_ends_at', 'asc'), // ending soonest
        };

        $lots = $query->paginate(12)->withQueryString();

        // Buyer's current active bids — to highlight "WINNING" cards
        $myActiveBidLotIds = [];
        if (auth()->user()->isBuyer()) {
            $myActiveBidLotIds = Bid::where('buyer_id', auth()->id())
                ->where('status', 'active')
                ->pluck('lot_id')
                ->toArray();
        }

        return view('auctions', compact('lots', 'myActiveBidLotIds', 'sort'));
    }
}
