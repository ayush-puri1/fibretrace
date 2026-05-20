<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lot;
use App\Models\Bid;
use App\Models\Transaction;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /** Admin dashboard with platform-wide stats and queues */
    public function index()
    {
        $stats = [
            'pending_verifications' => User::where('status', 'pending')->count(),
            'pending_lots'          => Lot::where('status', 'pending_review')->count(),
            'flagged_lots'          => Lot::where('flagged', true)->count(),
            'active_auctions'       => Lot::where('status', 'active')->count(),
            'total_users'           => User::whereIn('role', ['seller', 'buyer'])->count(),
            'verified_users'        => User::where('status', 'verified')->count(),
            'pending_settlements'   => Transaction::where('payment_status', 'pending')->count(),
            'live_bids'             => Bid::where('status', 'active')->count(),
            'escrow_volume'         => Transaction::whereIn('payment_status', ['pending', 'paid'])->sum('subtotal'),
        ];

        // Recent pending users (for the quick action panel)
        $pendingUsers = User::where('status', 'pending')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // Recent flagged lots
        $flaggedLots = Lot::where('flagged', true)
            ->with('seller')
            ->orderByDesc('flag_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingUsers', 'flaggedLots'));
    }
}
