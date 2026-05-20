<?php

namespace App\Mail;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Sent to BUYER when payment is confirmed — tells them to await dispatch.
 * Sent to SELLER when payment is confirmed — tells them to dispatch goods.
 */
class PaymentConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Transaction $transaction;
    public string      $recipientRole; // 'buyer' or 'seller'

    public function __construct(Transaction $transaction, string $recipientRole)
    {
        $this->transaction   = $transaction;
        $this->recipientRole = $recipientRole;
    }

    public function build(): self
    {
        $subject = $this->recipientRole === 'buyer'
            ? "✅ Payment Confirmed — Awaiting Dispatch"
            : "💰 Payment Received — Please Dispatch Goods";

        return $this
            ->subject($subject)
            ->view('emails.payment-confirmed')
            ->with([
                'transaction'   => $this->transaction,
                'recipientRole' => $this->recipientRole,
                'lotNumber'     => $this->transaction->lot->lot_number ?? 'N/A',
                'amount'        => '₹' . number_format($this->transaction->total_amount, 2),
            ]);
    }
}
