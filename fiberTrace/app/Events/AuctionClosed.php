<?php

namespace App\Events;

use App\Models\Lot;
use App\Models\Transaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * AuctionClosed Event
 *
 * Broadcast when a seller accepts the highest bid, closing the auction.
 * All browser windows watching this lot's bidding room should redirect
 * to their respective settlement or bid-cancelled page.
 *
 * Frontend listens with:
 *   Echo.channel('lot.' + lotId).listen('AuctionClosed', (e) => {
 *       window.location.href = e.redirectUrl;
 *   })
 */
class AuctionClosed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int    $lotId;
    public string $lotNumber;
    public float  $finalPrice;
    public int    $transactionId;

    public function __construct(Lot $lot, Transaction $transaction)
    {
        $this->lotId         = $lot->id;
        $this->lotNumber     = $lot->lot_number;
        $this->finalPrice    = (float) $transaction->agreed_price;
        $this->transactionId = $transaction->id;
        // NOTE: winner buyer identity not revealed in the broadcast — they'll know on their settlement page
    }

    public function broadcastOn(): Channel
    {
        return new Channel('lot.' . $this->lotId);
    }

    public function broadcastAs(): string
    {
        return 'AuctionClosed';
    }
}
