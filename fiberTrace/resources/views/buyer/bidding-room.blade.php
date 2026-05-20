@extends('layouts.dashboard')

@section('title', 'Bidding Room #{{ $lot->lot_number }} - FibreTrace')

@section('page-title', 'Active Bidding Room')

@section('dashboard-content')
    <!-- Breadcrumb & Header -->
    <div class="mb-lg">
        <div class="flex items-center gap-2 text-on-surface-variant font-label-sm mb-2">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Overview</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('auctions') }}" class="hover:text-primary transition-colors">Live Auctions</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-bold">Lot #{{ $lot->lot_number }}</span>
        </div>
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-md">
            <div>
                <h2 class="font-headline-md text-primary font-bold flex items-center gap-3">
                    {{ $lot->fiber_type }}
                    @if($lot->auction_ends_at && $lot->auction_ends_at->diffInHours(now()) < 1)
                    <span class="bg-error/10 text-error border border-error/20 text-[10px] font-bold px-2.5 py-1 rounded-full flex items-center gap-1">
                        <span class="material-symbols-filled text-[12px]">local_fire_department</span> ENDING SOON
                    </span>
                    @endif
                </h2>
                <div class="flex items-center gap-3 mt-2 flex-wrap">
                    <span class="bg-surface-container-low text-on-surface-variant text-[11px] px-2.5 py-1 rounded-md font-semibold border border-outline-variant/30">{{ number_format($lot->weight_kg) }} kg</span>
                    <span class="bg-surface-container-low text-on-surface-variant text-[11px] px-2.5 py-1 rounded-md font-semibold border border-outline-variant/30">{{ $lot->seller->city ?? 'N/A' }}</span>
                    <span class="bg-surface-container-low text-on-surface-variant text-[11px] px-2.5 py-1 rounded-md font-semibold border border-outline-variant/30">{{ $lot->color_description }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="bg-secondary/10 border border-secondary/30 text-secondary font-label-sm px-4 py-3 rounded-xl mb-lg flex items-center gap-3">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-error/10 border border-error/30 text-error font-label-sm px-4 py-3 rounded-xl mb-lg flex items-center gap-3">
            <span class="material-symbols-outlined text-[20px]">error</span>
            {{ session('error') }}
        </div>
    @endif
    
    {{-- Live Outbid Banner (Hidden by default, triggered via WebSocket) --}}
    <div id="outbid-banner" class="hidden bg-error/10 border border-error/30 text-error font-label-sm px-4 py-3 rounded-xl mb-lg flex items-center justify-between gap-3 shadow-sm animate-pulse">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-[20px]">warning</span>
            <span class="font-bold">Alert: You have just been outbid!</span> Another buyer placed a higher bid.
        </div>
        <a href="#bid-amount-input" class="bg-error text-white px-3 py-1.5 rounded-lg text-[12px] font-bold hover:bg-error/90 transition-colors">Bid Again</a>
    </div>

    <!-- Full Screen Auction Closed Overlay -->
    <div id="auction-closed-overlay" class="hidden fixed inset-0 z-50 bg-surface-container-lowest/90 backdrop-blur-md flex flex-col items-center justify-center transition-all duration-500">
        <div class="bg-white p-xl rounded-[2rem] shadow-[0_24px_64px_rgba(0,108,73,0.1)] border border-primary/20 flex flex-col items-center max-w-md text-center transform scale-110 animate-bounce-in">
            <div class="w-20 h-20 bg-secondary/10 text-secondary rounded-full flex items-center justify-center mb-6">
                <span class="material-symbols-filled text-[40px]">gavel</span>
            </div>
            <h2 class="font-headline-lg text-primary font-bold mb-2">Auction Closed!</h2>
            <p class="font-body-md text-on-surface-variant mb-6">The seller has accepted a bid. Calculating final results and redirecting you...</p>
            <div class="flex gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-secondary animate-bounce"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-secondary animate-bounce" style="animation-delay: 0.1s"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-secondary animate-bounce" style="animation-delay: 0.2s"></span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-lg">
        <!-- Left Column: Specs & Photos -->
        <div class="flex flex-col gap-lg">
            
            <!-- Photo Gallery -->
            <div class="glass-panel p-2 rounded-[2rem] bg-white/70 border-white/90 shadow-[0_8px_32px_rgba(0,53,39,0.03)] flex gap-2 overflow-x-auto custom-scrollbar snap-x">
                @if($lot->images->isNotEmpty())
                    @foreach($lot->images as $img)
                    <div class="min-w-[100%] h-[360px] rounded-[1.5rem] snap-start shrink-0 relative overflow-hidden">
                        <img src="{{ asset('storage/' . $img->file_path) }}" class="w-full h-full object-cover"
                             alt="{{ $lot->fiber_type }}"
                             onerror="this.parentElement.innerHTML='<div class=\'w-full h-full bg-surface-container-low flex items-center justify-center\'><span class=\'material-symbols-outlined text-[64px] text-outline-variant/50\'>image</span></div>'">
                        <div class="absolute bottom-4 right-4 bg-black/50 backdrop-blur-md text-white text-[11px] px-3 py-1.5 rounded-full font-semibold">
                            {{ $loop->iteration }} / {{ $lot->images->count() }}
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="min-w-[100%] h-[360px] bg-surface-container-low rounded-[1.5rem] flex items-center justify-center border border-outline-variant/30 snap-start shrink-0 relative group cursor-pointer overflow-hidden">
                    <span class="material-symbols-outlined text-[64px] text-outline-variant/50">image</span>
                    <div class="absolute bottom-4 right-4 bg-black/50 backdrop-blur-md text-white text-[11px] px-3 py-1.5 rounded-full font-semibold">No Photos</div>
                </div>
                @endif
            </div>

            <!-- Technical Specifications -->
            <div class="glass-panel p-lg rounded-[2rem] bg-white/70 border-white/90 shadow-[0_8px_32px_rgba(0,53,39,0.03)]">
                <h3 class="font-label-lg text-primary font-bold mb-md">Lot Specifications</h3>
                
                <div class="grid grid-cols-2 gap-y-4 gap-x-8">
                    <div>
                        <div class="text-[11px] text-outline uppercase font-bold tracking-wider mb-1">Total Weight</div>
                        <div class="font-label-lg text-primary font-bold">{{ number_format($lot->weight_kg) }} kg</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-outline uppercase font-bold tracking-wider mb-1">Base Price</div>
                        <div class="font-label-lg text-on-surface-variant font-bold">₹{{ number_format($lot->base_price, 2) }} / kg</div>
                    </div>
                    <div class="col-span-2 w-full h-px bg-outline-variant/30"></div>
                    <div>
                        <div class="text-[11px] text-outline uppercase font-bold tracking-wider mb-1">Primary Fiber</div>
                        <div class="font-label-lg text-primary font-bold">{{ $lot->fiber_type }} ({{ $lot->fiber_purity_pct }}%)</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-outline uppercase font-bold tracking-wider mb-1">Color Sort</div>
                        <div class="font-label-lg text-primary font-bold">{{ $lot->color_sorted ? 'Sorted' : 'Mixed' }} · {{ $lot->color_description }}</div>
                    </div>
                    <div class="col-span-2 w-full h-px bg-outline-variant/30"></div>
                    <div>
                        <div class="text-[11px] text-outline uppercase font-bold tracking-wider mb-1">Seller Identity</div>
                        <div class="font-label-lg text-primary font-bold flex items-center gap-1">
                            <span class="material-symbols-filled text-secondary text-[16px]">verified</span> Verified Factory
                        </div>
                    </div>
                    <div>
                        <div class="text-[11px] text-outline uppercase font-bold tracking-wider mb-1">Location</div>
                        <div class="font-label-lg text-primary font-bold">{{ $lot->seller->city ?? 'N/A' }}, {{ $lot->seller->state ?? '' }}</div>
                    </div>
                    @if($lot->auction_ends_at)
                    <div class="col-span-2 w-full h-px bg-outline-variant/30"></div>
                    <div class="col-span-2">
                        <div class="text-[11px] text-outline uppercase font-bold tracking-wider mb-1">Auction Ends</div>
                        <div class="font-label-lg text-primary font-bold">{{ $lot->auction_ends_at->format('d M Y, h:i A') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Live Bidding Room -->
        <div class="flex flex-col gap-lg">
            
            <div class="glass-panel rounded-[2rem] bg-white/70 border-white/90 shadow-[0_12px_40px_rgba(0,53,39,0.08)] flex flex-col h-[750px] overflow-hidden relative">
                
                @php $endsImminent = $lot->auction_ends_at && $lot->auction_ends_at->diffInHours(now()) < 1; @endphp
                <div class="absolute top-0 inset-x-0 h-1.5 {{ $endsImminent ? 'bg-gradient-to-r from-error to-error-container' : 'bg-gradient-to-r from-secondary to-primary' }} z-20"></div>

                <!-- Live Header -->
                <div class="p-lg border-b border-outline-variant/30 bg-surface-container-lowest/90 backdrop-blur-md relative z-10">
                    <div class="flex justify-between items-center mb-6">
                        <span class="font-label-sm text-outline font-semibold uppercase tracking-wide">Time Remaining</span>
                        @if($lot->auction_ends_at)
                        <span class="font-headline-sm {{ $endsImminent ? 'text-error animate-pulse' : 'text-primary' }} font-bold flex items-center gap-1">
                            <span class="material-symbols-outlined text-[20px]">timer</span>
                            {{ $lot->auction_ends_at->diffForHumans() }}
                        </span>
                        @endif
                    </div>

                    <!-- Current Highest Bid -->
                    <div class="bg-surface-container-lowest border border-{{ $myBid && $lot->highestBid?->buyer_id === auth()->id() ? 'secondary/30' : 'error/30' }} rounded-[1.5rem] p-6 shadow-[0_8px_24px_rgba(186,26,26,0.05)] relative overflow-hidden group">
                        <div class="absolute -right-8 -top-8 w-32 h-32 bg-error/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="text-[11px] text-outline font-bold uppercase tracking-wider mb-2 relative z-10 flex justify-between items-center">
                            <span>Current Highest Bid</span>
                            <span id="live-bid-count" class="bg-outline-variant/20 px-2 py-0.5 rounded text-[10px]">{{ $lot->bids->count() }} bid(s)</span>
                        </div>
                        <div class="flex items-end gap-3 relative z-10">
                            @if($lot->highestBid)
                                <span id="live-highest-bid" class="font-headline-lg text-[56px] text-primary font-bold leading-none tracking-tight transition-all duration-300">₹{{ number_format($lot->highestBid->amount, 2) }}</span>
                            @else
                                <span id="live-highest-bid" class="font-headline-lg text-[40px] text-outline font-bold leading-none tracking-tight transition-all duration-300">No bids yet</span>
                            @endif
                            @if($lot->highestBid)
                            <span class="font-body-md text-on-surface-variant mb-2">/ kg</span>
                            @endif
                        </div>
                        @if($myBid)
                            @if($myBid->status === 'active')
                                <div class="text-[12px] text-secondary font-bold mt-2 relative z-10 flex items-center gap-1">
                                    <span class="material-symbols-filled text-[14px]">stars</span> You are currently winning!
                                </div>
                            @else
                                <div class="text-[12px] text-error font-bold mt-2 relative z-10 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">warning</span> You have been outbid!
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Scrolling Bid Feed -->
                <div class="flex-1 overflow-y-auto custom-scrollbar p-lg flex flex-col gap-3 relative z-0 bg-surface-container-low/30">
                    <div class="absolute top-0 inset-x-0 h-8 bg-gradient-to-b from-surface-container-lowest/90 to-transparent pointer-events-none z-10"></div>
                    
                    @forelse($lot->bids->take(20) as $bid)
                    @php $isMe = $bid->buyer_id === auth()->id(); $isHighest = $loop->first && $lot->highestBid?->id === $bid->id; @endphp
                    <div class="{{ $isMe ? 'bg-secondary-container/20 border-secondary/30' : 'bg-surface-container-lowest border-outline-variant/30' }} {{ !$isHighest ? 'opacity-' . max(40, 100 - $loop->index * 15) : '' }} border p-4 rounded-xl flex justify-between items-center shadow-sm relative overflow-hidden">
                        <div class="absolute left-0 inset-y-0 w-1 {{ $isMe ? 'bg-secondary' : ($isHighest ? 'bg-primary' : 'bg-outline-variant') }}"></div>
                        <div class="pl-2">
                            <div class="font-label-lg {{ $isMe ? 'text-secondary' : 'text-primary' }} font-bold">₹{{ number_format($bid->amount, 2) }} / kg</div>
                            <div class="text-[11px] {{ $isMe ? 'text-secondary font-bold' : 'text-outline' }} font-semibold mt-0.5">
                                {{ $isMe ? 'You' : 'Buyer #' . $bid->buyer_id }}
                            </div>
                        </div>
                        <span class="text-[11px] text-on-surface-variant font-bold bg-surface-container-low px-2 py-1 rounded">{{ $bid->created_at->diffForHumans(['short' => true]) }}</span>
                    </div>
                    @empty
                    <div class="flex-1 flex flex-col items-center justify-center text-center py-8">
                        <span class="material-symbols-outlined text-[48px] text-outline-variant mb-2">gavel</span>
                        <div class="font-label-md text-on-surface-variant">No bids yet. Be the first!</div>
                    </div>
                    @endforelse
                </div>

                <!-- Quick Bid Footer -->
                <div class="p-lg border-t border-outline-variant/30 bg-surface-container-lowest/95 backdrop-blur-md relative z-20">
                    @php $nextMin = $lot->highestBid ? round($lot->highestBid->amount + 0.50, 2) : $lot->base_price; @endphp
                    <div class="flex gap-3 mb-4">
                        @foreach([0.50, 1.00, 5.00] as $inc)
                        <button type="button" onclick="addIncrement({{ $inc }})"
                                class="flex-1 py-2 rounded-lg bg-surface-container-low border border-outline-variant/50 hover:border-primary text-primary font-label-sm font-bold transition-colors">
                            + ₹{{ number_format($inc, 2) }}
                        </button>
                        @endforeach
                    </div>

                    <form method="POST" action="{{ route('buyer.bid.place', $lot) }}">
                        @csrf
                        <div class="flex gap-3">
                            <div class="relative flex-1 group">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant font-bold">₹</span>
                                <input type="number" id="bid-amount-input" name="amount" step="0.10" min="{{ $minimumBid }}"
                                       placeholder="Min. {{ $minimumBid }}"
                                       class="w-full bg-white pl-8 pr-4 py-4 rounded-xl border border-outline-variant/50 focus:border-secondary focus:ring-1 focus:ring-secondary outline-none transition-all font-headline-sm font-bold text-primary shadow-inner"
                                       required>
                            </div>
                            <button type="submit" class="btn-magnetic bg-secondary text-white font-label-lg px-8 rounded-xl hover:bg-primary transition-all shadow-[0_8px_20px_rgba(0,108,73,0.2)] flex items-center justify-center whitespace-nowrap">
                                Place Bid
                            </button>
                        </div>
                        <div class="text-center text-[10px] text-outline mt-3">
                            @if($lot->highestBid)
                            Est. commitment: <span class="font-bold">₹{{ number_format($minimumBid * $lot->weight_kg, 0) }}</span> for {{ number_format($lot->weight_kg) }}kg (excl. logistics)
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function addIncrement(amount) {
            const input = document.getElementById('bid-amount-input');
            const currentVal = parseFloat(input.value) || {{ $minimumBid }};
            input.value = (currentVal + amount).toFixed(2);
        }
    </script>

    {{-- Real-Time WebSockets Listener --}}
    @include('components.reverb-bid-listener', ['lot' => $lot])
@endsection
