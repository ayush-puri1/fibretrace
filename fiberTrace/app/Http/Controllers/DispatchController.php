<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class DispatchController extends Controller
{
    /** Show logistics tracking for a transaction */
    public function show(Transaction $transaction)
    {
        $user = auth()->user();
        abort_if(
            $transaction->buyer_id !== $user->id && $transaction->seller_id !== $user->id,
            403
        );

        $transaction->load(['lot.images', 'buyer', 'seller']);

        return view('dispatch', compact('transaction'));
    }

    /**
     * Update logistics status.
     * Allowed states: awaiting_dispatch → dispatched → in_transit → delivered
     * On 'delivered': starts the 48-hour escrow release countdown.
     */
    public function updateStatus(Request $request, Transaction $transaction)
    {
        // Only the seller can update dispatch status
        abort_if($transaction->seller_id !== auth()->id(), 403);

        $request->validate([
            'status' => ['required', 'in:dispatched,in_transit,delivered'],
        ]);

        $updates = ['logistics_status' => $request->status];

        if ($request->status === 'delivered') {
            // Start 48-hour escrow release window
            $updates['escrow_released_at'] = now()->addHours(48);
        }

        $transaction->update($updates);

        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'logistics_updated',
            'subject_type' => Transaction::class,
            'subject_id'   => $transaction->id,
            'description'  => "Logistics updated to '{$request->status}' for {$transaction->transaction_number}",
            'ip_address'   => $request->ip(),
        ]);

        return redirect()->route('dispatch.show', $transaction)
            ->with('success', "Logistics status updated to: " . ucfirst(str_replace('_', ' ', $request->status)));
    }
}
