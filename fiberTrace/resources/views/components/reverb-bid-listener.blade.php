{{--
    Reverb Real-Time Bid Listener
    Include this at the bottom of buyer/bidding-room.blade.php.

    Expects: $lot->id (passed from BidController::room())

    Events listened to:
    - BidPlaced       → updates the live bid price and counter
    - AuctionClosed   → redirects all viewers to the appropriate page
--}}
<script>
(function () {
    const lotId   = {{ $lot->id }};
    const isOwner = {{ auth()->id() === $lot->seller_id ? 'true' : 'false' }};

    /**
     * Listen on the public lot channel.
     * No auth required — all verified users can watch any auction.
     */
    if (window.Echo) {
        window.Echo.channel('lot.' + lotId)

            // --- New bid placed by another user ---
            .listen('.BidPlaced', (event) => {
                console.log('[Reverb] BidPlaced:', event);

                // Update the displayed highest bid price
                const bidEl = document.getElementById('live-highest-bid');
                if (bidEl) {
                    bidEl.textContent = '₹' + parseFloat(event.amount).toFixed(2);
                    bidEl.classList.add('bid-flash');
                    setTimeout(() => bidEl.classList.remove('bid-flash'), 1000);
                }

                // Update total bids counter
                const countEl = document.getElementById('live-bid-count');
                if (countEl) {
                    countEl.textContent = event.totalBids + ' bid(s)';
                }

                // Update minimum bid input
                const inputEl = document.getElementById('bid-amount-input');
                if (inputEl) {
                    const newMin = (parseFloat(event.amount) + 0.50).toFixed(2);
                    inputEl.min   = newMin;
                    inputEl.placeholder = 'Min ₹' + newMin;
                }

                // Show "you've been outbid" banner if this buyer had the highest bid
                const outbidBanner = document.getElementById('outbid-banner');
                if (outbidBanner && !isOwner) {
                    outbidBanner.classList.remove('hidden');
                }
            })

            // --- Auction closed by seller ---
            .listen('.AuctionClosed', (event) => {
                console.log('[Reverb] AuctionClosed:', event);
                // Show full-screen overlay before redirect
                const overlay = document.getElementById('auction-closed-overlay');
                if (overlay) {
                    overlay.classList.remove('hidden');
                }
                // Redirect after 3 seconds — buyer goes to their bids, seller goes to settlement
                setTimeout(() => {
                    if (isOwner) {
                        window.location.href = '/settlement/' + event.transactionId;
                    } else {
                        window.location.href = '/buyer/bids';
                    }
                }, 3000);
            });
    }
})();
</script>

{{-- Flash animation for bid updates --}}
<style>
    @keyframes bid-flash {
        0%   { color: inherit; }
        30%  { color: #006C49; transform: scale(1.08); }
        100% { color: inherit; transform: scale(1); }
    }
    .bid-flash { animation: bid-flash 0.8s ease-out; }
</style>
