@extends('layouts.dashboard')

@section('title', 'Lot #' . $lot->lot_number . ' - FibreTrace')

@section('page-title', 'Lot Details')

@section('dashboard-content')
    <!-- Breadcrumb & Header -->
    <div class="mb-lg">
        <div class="flex items-center gap-2 text-on-surface-variant font-label-sm mb-2">
            <a href="{{ url('/dashboard') }}" class="hover:text-primary transition-colors">Overview</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('seller.ledger') }}" class="hover:text-primary transition-colors">My Listings</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-bold">Lot {{ $lot->lot_number }}</span>
        </div>
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-md">
            <div>
                <h2 class="font-headline-md text-primary font-bold flex items-center gap-3">
                    {{ $lot->primary_fiber }}
                    @if($lot->status === 'active')
                        <span class="bg-secondary-container/30 text-secondary border border-secondary/20 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Active</span>
                    @elseif($lot->status === 'awarded')
                        <span class="bg-primary/10 text-primary border border-primary/20 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Awarded</span>
                    @else
                        <span class="bg-outline-variant/30 text-outline border border-outline-variant/50 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">{{ $lot->status }}</span>
                    @endif
                </h2>
                <p class="font-body-sm text-on-surface-variant">Listed on {{ $lot->created_at->format('M d, Y') }} 
                    @if($lot->auction_ends_at && $lot->status === 'active')
                        • Closes in <span class="text-error font-bold font-mono">{{ $lot->auction_ends_at->diffForHumans(null, true) }}</span>
                    @endif
                </p>
            </div>
            
            @if($lot->status === 'active')
            <form action="{{ route('seller.lot.cancel', $lot) }}" method="POST">
                @csrf
                <button type="submit" class="btn-magnetic bg-surface-container-lowest text-error border border-error/30 font-label-sm px-4 py-2.5 rounded-xl hover:bg-error/10 shadow-sm flex items-center gap-2 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">cancel</span> Cancel Auction
                </button>
            </form>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="bg-secondary/10 border border-secondary/30 text-secondary font-label-sm px-4 py-3 rounded-xl mb-lg flex items-center gap-3">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-lg">
        <!-- Left Column: Specs & Photos (2/3 width) -->
        <div class="lg:col-span-2 flex flex-col gap-lg">
            
            <!-- Photo Gallery -->
            <div class="glass-panel p-2 rounded-[2rem] bg-white/70 border-white/90 shadow-[0_8px_32px_rgba(0,53,39,0.03)] flex gap-2 overflow-x-auto custom-scrollbar snap-x">
                @if($lot->images->isNotEmpty())
                    @foreach($lot->images as $img)
                    <div class="min-w-[300px] h-[250px] rounded-3xl snap-start shrink-0 relative overflow-hidden border border-outline-variant/30">
                        <img src="{{ asset('storage/' . $img->file_path) }}" class="w-full h-full object-cover"
                             alt="{{ $lot->fiber_type }}"
                             onerror="this.parentElement.innerHTML='<div class=\'w-full h-full bg-surface-container-low flex items-center justify-center\'><span class=\'material-symbols-outlined text-[48px] text-outline-variant/50\'>image</span></div>'">
                        <div class="absolute bottom-3 right-3 bg-black/50 backdrop-blur-md text-white text-[10px] px-2.5 py-1 rounded-full font-semibold">
                            {{ $loop->iteration }} / {{ $lot->images->count() }}
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="min-w-[300px] h-[250px] bg-surface-container-low rounded-3xl flex items-center justify-center border border-outline-variant/30 snap-start shrink-0 relative overflow-hidden">
                    <span class="material-symbols-outlined text-[48px] text-outline-variant/50">image</span>
                    <div class="absolute bottom-3 right-3 bg-black/50 backdrop-blur-md text-white text-[10px] px-2.5 py-1 rounded-full font-semibold font-sans">No Photos</div>
                </div>
                @endif
            </div>

            <!-- Technical Specifications -->
            <div class="glass-panel p-lg rounded-[2rem] bg-white/70 border-white/90 shadow-[0_8px_32px_rgba(0,53,39,0.03)]">
                <h3 class="font-label-lg text-primary font-bold mb-md">Technical Specifications</h3>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-md">
                    <div class="bg-surface-container-lowest border border-outline-variant/30 p-3 rounded-xl">
                        <div class="text-[11px] text-outline uppercase font-bold tracking-wider mb-1">Total Weight</div>
                        <div class="font-label-lg text-primary font-bold">{{ number_format($lot->weight_kg) }} kg</div>
                    </div>
                    <div class="bg-surface-container-lowest border border-outline-variant/30 p-3 rounded-xl">
                        <div class="text-[11px] text-outline uppercase font-bold tracking-wider mb-1">Primary Fiber</div>
                        <div class="font-label-lg text-primary font-bold">{{ $lot->primary_fiber }}</div>
                    </div>
                    <div class="bg-surface-container-lowest border border-outline-variant/30 p-3 rounded-xl">
                        <div class="text-[11px] text-outline uppercase font-bold tracking-wider mb-1">Color Sort</div>
                        <div class="font-label-lg text-primary font-bold">{{ $lot->is_color_sorted ? 'Color Sorted' : 'Mixed Colors' }}</div>
                    </div>
                    <div class="bg-surface-container-lowest border border-outline-variant/30 p-3 rounded-xl">
                        <div class="text-[11px] text-outline uppercase font-bold tracking-wider mb-1">Location</div>
                        <div class="font-label-lg text-primary font-bold">{{ $lot->seller->city ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Live Bidding Room (1/3 width) -->
        <div class="lg:col-span-1 flex flex-col gap-lg">
            
            <div class="glass-panel rounded-[2rem] bg-white/70 border-white/90 shadow-[0_12px_40px_rgba(0,53,39,0.08)] flex flex-col h-[600px] overflow-hidden relative">
                <!-- Header -->
                <div class="p-lg border-b border-outline-variant/30 bg-surface-container-lowest/80 backdrop-blur-md relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-label-lg text-primary font-bold flex items-center gap-2">
                            @if($lot->status === 'active')
                                <span class="w-2.5 h-2.5 rounded-full bg-secondary animate-pulse shadow-[0_0_8px_rgba(0,108,73,0.6)]"></span>
                                Live Bidding Room
                            @else
                                Auction Closed
                            @endif
                        </h3>
                        <span class="bg-surface-container-low text-on-surface-variant text-[10px] font-bold px-2 py-0.5 rounded-full border border-outline-variant/50">{{ $lot->bids->count() }} Bids Total</span>
                    </div>

                    <!-- Current Highest -->
                    <div class="bg-secondary-container/20 border border-secondary/30 rounded-xl p-4 shadow-inner relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-16 h-16 bg-secondary/10 rounded-full blur-xl"></div>
                        <div class="text-[11px] text-secondary font-bold uppercase tracking-wider mb-1">Current Highest Bid</div>
                        <div class="flex items-end gap-2">
                            @if($lot->highestBid)
                                <span class="font-headline-lg text-[36px] text-secondary font-bold leading-none tracking-tight">₹{{ number_format($lot->highestBid->amount, 2) }}</span>
                                <span class="font-body-sm text-on-surface-variant mb-1">/ kg</span>
                            @else
                                <span class="font-headline-lg text-[24px] text-outline font-bold leading-none tracking-tight">No Bids Yet</span>
                            @endif
                        </div>
                        @if($lot->highestBid)
                            <div class="text-[10px] text-outline font-semibold mt-1 flex justify-between">
                                <span>Placed by: Buyer #{{ $lot->highestBid->buyer_id }}</span>
                                <span>{{ $lot->highestBid->created_at->diffForHumans() }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Bid Feed -->
                <div class="flex-1 overflow-y-auto custom-scrollbar p-lg flex flex-col gap-3 relative z-0">
                    <div class="absolute top-0 inset-x-0 h-4 bg-gradient-to-b from-surface-container-lowest/80 to-transparent pointer-events-none z-10"></div>
                    
                    @forelse($lot->bids->sortByDesc('amount') as $bid)
                    <div class="bg-surface-container-lowest border border-outline-variant/30 p-3 rounded-xl flex justify-between items-center {{ $loop->first ? 'opacity-100' : 'opacity-60' }}">
                        <div>
                            <div class="font-label-md text-on-surface-variant font-bold">₹{{ number_format($bid->amount, 2) }} / kg</div>
                            <div class="text-[10px] text-outline">Buyer #{{ $bid->buyer_id }}</div>
                        </div>
                        <span class="text-[10px] text-outline font-medium">{{ $bid->created_at->diffForHumans(null, true, true) }}</span>
                    </div>
                    @empty
                    <div class="flex-1 flex flex-col items-center justify-center text-center py-8">
                        <span class="material-symbols-outlined text-[32px] text-outline-variant mb-2">gavel</span>
                        <div class="font-label-md text-on-surface-variant">Waiting for bids...</div>
                    </div>
                    @endforelse
                </div>

                <!-- Action Footer -->
                @if($lot->status === 'active' && $lot->highestBid)
                <div class="p-lg border-t border-outline-variant/30 bg-surface-container-lowest/80 backdrop-blur-md relative z-10">
                    <form action="{{ route('seller.lot.accept', $lot) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-magnetic bg-primary text-white font-label-lg w-full py-4 rounded-xl hover:bg-secondary transition-all shadow-[0_8px_20px_rgba(0,53,39,0.2)] flex justify-center items-center gap-2 relative overflow-hidden group">
                            <span class="relative z-10">Accept Highest Bid (₹{{ number_format($lot->highestBid->amount, 2) }})</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1s_infinite]"></div>
                        </button>
                    </form>
                    <p class="text-center text-[10px] text-outline mt-3">Accepting will immediately close the auction and move it to Settlement.</p>
                </div>
                @endif
            </div>

        </div>
    </div>
@endsection
