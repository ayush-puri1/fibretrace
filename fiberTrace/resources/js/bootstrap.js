import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Laravel Echo + Pusher (pointed at Laravel Reverb server)
 *
 * How it works:
 *   - Reverb runs on localhost:8080 in dev (start with: php artisan reverb:start)
 *   - On bid placement, the server broadcasts `BidPlaced` on channel `lot.{id}`
 *   - The buyer's bidding room JS listens and updates the UI without a page refresh
 *   - On auction close, `AuctionClosed` triggers a redirect to settlement
 *
 * Start the queue worker for emails:
 *   php artisan queue:work
 *
 * Start Reverb WebSocket server:
 *   php artisan reverb:start
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key:         import.meta.env.VITE_REVERB_APP_KEY,
    wsHost:      import.meta.env.VITE_REVERB_HOST    ?? 'localhost',
    wsPort:      import.meta.env.VITE_REVERB_PORT    ?? 8080,
    wssPort:     import.meta.env.VITE_REVERB_PORT    ?? 443,
    scheme:      import.meta.env.VITE_REVERB_SCHEME  ?? 'http',
    forceTLS:   (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
});
