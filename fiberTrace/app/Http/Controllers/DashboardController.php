<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\Bid;
use App\Models\Transaction;
use App\Models\MarketPrice;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Main client dashboard — shown to both sellers and buyers after login.
     * Passes stats, recent lots/bids, and market prices to the view.
     */
    public function index()
    {
        $user = auth()->user();

        // --- Stats section differs by role ---
        if ($user->isSeller()) {
            $activeLots   = Lot::where('seller_id', $user->id)->where('status', 'active')->count();
            $pendingLots  = Lot::where('seller_id', $user->id)->where('status', 'pending_review')->count();
            $settledLots  = Lot::where('seller_id', $user->id)->where('status', 'settled')->count();
            $totalRevenue = Transaction::where('seller_id', $user->id)
                ->where('payment_status', 'released')
                ->sum('total_amount');
            $recentLots   = Lot::where('seller_id', $user->id)
                ->with('highestBid')
                ->orderByDesc('created_at')
                ->take(5)
                ->get();
            $stats = compact('activeLots', 'pendingLots', 'settledLots', 'totalRevenue');
        } else {
            // Buyer stats
            $activeBids   = Bid::where('buyer_id', $user->id)->where('status', 'active')->count();
            $wonBids      = Bid::where('buyer_id', $user->id)->where('status', 'won')->count();
            $totalSpend   = Transaction::where('buyer_id', $user->id)
                ->whereIn('payment_status', ['paid', 'released'])
                ->sum('total_amount');
            $recentLots   = collect(); // buyers don't see "their lots"
            $stats = compact('activeBids', 'wonBids', 'totalSpend');
        }

        // Latest market prices for the live ticker
        $marketPrices = MarketPrice::orderByDesc('week_start')
            ->take(6)
            ->get();

        // Recent activity: latest transactions touching this user
        $recentTransactions = Transaction::where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)
                  ->orWhere('seller_id', $user->id);
            })
            ->with(['lot', 'buyer', 'seller'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('dashboard', compact('user', 'stats', 'marketPrices', 'recentTransactions', 'recentLots'));
    }
}
