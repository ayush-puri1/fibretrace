<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Escrow wallet — shows all transactions for this user with payment status.
     */
    public function index()
    {
        $user = auth()->user();

        $transactions = Transaction::where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)
                  ->orWhere('seller_id', $user->id);
            })
            ->with(['lot', 'buyer', 'seller'])
            ->orderByDesc('created_at')
            ->paginate(10);

        // Summary stats for the wallet header
        $totalEscrow   = Transaction::where('seller_id', $user->id)->where('payment_status', 'paid')->sum('total_amount');
        $totalReleased = Transaction::where('seller_id', $user->id)->where('payment_status', 'released')->sum('total_amount');
        $totalSpent    = Transaction::where('buyer_id', $user->id)->whereIn('payment_status', ['paid', 'released'])->sum('total_amount');

        return view('wallet', compact('transactions', 'totalEscrow', 'totalReleased', 'totalSpent'));
    }
}
