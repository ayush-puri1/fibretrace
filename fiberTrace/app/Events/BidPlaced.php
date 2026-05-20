<?php

namespace App\Events;

use App\Models\Bid;
use App\Models\Lot;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * BidPlaced Event
 *
 * Broadcast over the public Reverb channel `lot.{lotId}` whenever
 * a new bid is placed. The buyer's identity is NEVER revealed to
 * other clients (blind bidding) — only the bid amount is sent.
 *
 * Frontend listens with:
 *   Echo.channel('lot.' + lotId).listen('BidPlaced', (e) => { ... })
 */
class BidPlaced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int    $lotId;
    public string $lotNumber;
    public float  $amount;
    public int    $totalBids;
    public string $timeRemaining; // ISO 8601 timestamp of auction_ends_at

    public function __construct(Bid $bid, Lot $lot)
    {
        $this->lotId         = $lot->id;
        $this->lotNumber     = $lot->lot_number;
        $this->amount        = (float) $bid->amount;
        $this->totalBids     = $lot->bids()->count();
        $this->timeRemaining = $lot->auction_ends_at->toIso8601String();
        // NOTE: buyer_id is intentionally NOT broadcast — blind bidding guarantee
    }

    /**
     * Broadcast on a public channel named by lot ID.
     * Public channel so both buyers and sellers can see live updates.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('lot.' . $this->lotId);
    }

    /**
     * Custom event name for the frontend Echo listener.
     */
    public function broadcastAs(): string
    {
        return 'BidPlaced';
    }
}
