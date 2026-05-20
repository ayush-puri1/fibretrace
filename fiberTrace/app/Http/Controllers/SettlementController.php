<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\ActivityLog;
use App\Mail\PaymentConfirmedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SettlementController extends Controller
{
    /** Show the escrow invoice + payment screen */
    public function show(Transaction $transaction)
    {
        // Only buyer or seller involved can see the settlement
        $user = auth()->user();
        abort_if(
            $transaction->buyer_id !== $user->id && $transaction->seller_id !== $user->id,
            403
        );

        $transaction->load(['lot.images', 'bid', 'buyer', 'seller']);

        return view('settlement', compact('transaction'));
    }

    /**
     * Simulate payment — marks the transaction as paid (escrow received).
     * In production this would be a Razorpay callback. For MVP: always succeeds.
     */
    public function simulatePayment(Request $request, Transaction $transaction)
    {
        abort_if($transaction->buyer_id !== auth()->id(), 403);
        abort_if($transaction->payment_status !== 'pending', 422, 'Payment already processed.');

        // Simulate: payment always succeeds
        $transaction->update([
            'payment_status'   => 'paid',
            'logistics_status' => 'awaiting_dispatch',
        ]);

        // Update the lot status
        $transaction->lot->update(['status' => 'settled']);

        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'payment_simulated',
            'subject_type' => Transaction::class,
            'subject_id'   => $transaction->id,
            'description'  => "Payment simulated for transaction {$transaction->transaction_number}. Amount: ₹{$transaction->total_amount}",
            'ip_address'   => $request->ip(),
        ]);

        // Queue payment confirmation emails to both buyer and seller
        $transaction->load(['buyer', 'seller', 'lot']);
        Mail::to($transaction->buyer->email)->queue(new PaymentConfirmedMail($transaction, 'buyer'));
        Mail::to($transaction->seller->email)->queue(new PaymentConfirmedMail($transaction, 'seller'));

        return redirect()->route('settlement.show', $transaction)
            ->with('success', 'Payment successful! Escrow funds received. Awaiting dispatch.');
    }
}
