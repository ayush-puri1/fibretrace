@extends('layouts.dashboard')

@section('title', 'Live Auctions - FibreTrace')

@section('page-title', 'Live Auctions')

@section('dashboard-content')

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

    <!-- Controls Bar -->
    <form method="GET" action="{{ route('auctions') }}" class="flex flex-col sm:flex-row justify-between items-center gap-md mb-xl">
        <div class="flex items-center gap-3 w-full sm:w-auto flex-wrap">
            <span class="font-label-md text-on-surface-variant">Sort by:</span>
            <select name="sort" onchange="this.form.submit()" class="bg-white border border-outline-variant/50 text-label-sm font-semibold rounded-lg px-3 py-2 outline-none focus:border-primary shadow-sm">
                <option value="ending_soon" {{ $sort === 'ending_soon' ? 'selected' : '' }}>Ending Soonest</option>
                <option value="price_asc"   {{ $sort === 'price_asc'   ? 'selected' : '' }}>Price (Low to High)</option>
                <option value="price_desc"  {{ $sort === 'price_desc'  ? 'selected' : '' }}>Price (High to Low)</option>
                <option value="weight_desc" {{ $sort === 'weight_desc' ? 'selected' : '' }}>Weight (Largest First)</option>
            </select>
            <select name="category" onchange="this.form.submit()" class="bg-white border border-outline-variant/50 text-label-sm font-semibold rounded-lg px-3 py-2 outline-none focus:border-primary shadow-sm">
                <option value="">All Categories</option>
                <option value="cutting_scraps"   {{ request('category') === 'cutting_scraps'   ? 'selected' : '' }}>Cutting Scraps</option>
                <option value="yarn_ends"        {{ request('category') === 'yarn_ends'        ? 'selected' : '' }}>Yarn Ends</option>
                <option value="rejected_batches" {{ request('category') === 'rejected_batches' ? 'selected' : '' }}>Rejected Batches</option>
                <option value="selvedge"         {{ request('category') === 'selvedge'         ? 'selected' : '' }}>Selvedge</option>
            </select>
        </div>
    </form>

    <!-- Auction Grid -->
    @if($lots->isNotEmpty())
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-lg">
        @foreach($lots as $lot)
        @php
            $isWinning = in_array($lot->id, $myActiveBidLotIds);
            $endingImminently = $lot->auction_ends_at && $lot->auction_ends_at->diffInHours(now()) < 1;
        @endphp

        <div class="glass-panel bg-white/70 rounded-[1.5rem] border {{ $isWinning ? 'border-primary/40' : 'border-white' }} p-5 flex flex-col relative overflow-hidden group hover:shadow-[0_12px_32px_rgba(0,108,73,0.1)] transition-all duration-300">

            {{-- Top accent bar --}}
            @if($isWinning)
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-secondary to-primary"></div>
            @elseif($endingImminently)
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-error to-error-container"></div>
            @endif

            <div class="flex justify-between items-start mb-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        @if($isWinning)
                            <span class="bg-secondary/10 text-secondary text-[10px] font-bold px-2 py-0.5 rounded-full border border-secondary/20 flex items-center gap-1">
                                <span class="material-symbols-filled text-[10px]">stars</span> WINNING
                            </span>
                        @elseif($endingImminently)
                            <span class="bg-error/10 text-error text-[10px] font-bold px-2 py-0.5 rounded-full border border-error/20 flex items-center gap-1">
                                <span class="material-symbols-filled text-[10px]">local_fire_department</span> HOT
                            </span>
                        @else
                            <span class="bg-secondary-container/30 text-secondary border border-secondary/20 text-[10px] font-bold px-2 py-0.5 rounded-full">ACTIVE</span>
                        @endif
                        <span class="text-[12px] font-bold text-on-surface-variant">#{{ $lot->lot_number }}</span>
                    </div>
                    <h3 class="font-headline-sm text-primary font-bold">{{ $lot->fiber_type }}</h3>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 mb-4">
                <span class="bg-surface-container-low text-on-surface-variant text-[11px] px-2.5 py-1 rounded-md font-semibold border border-outline-variant/30">{{ number_format($lot->weight_kg) }} kg</span>
                <span class="bg-surface-container-low text-on-surface-variant text-[11px] px-2.5 py-1 rounded-md font-semibold border border-outline-variant/30">{{ $lot->seller->city ?? 'N/A' }}</span>
                <span class="bg-surface-container-low text-on-surface-variant text-[11px] px-2.5 py-1 rounded-md font-semibold border border-outline-variant/30">{{ $lot->color_description }}</span>
            </div>

            <div class="{{ $isWinning ? 'bg-secondary-container/20 border-secondary/30' : 'bg-surface-container-lowest border-outline-variant/30' }} border rounded-xl p-4 flex flex-col gap-3 mb-5 mt-auto shadow-inner">
                <div class="flex justify-between items-center">
                    <span class="font-label-sm {{ $isWinning ? 'text-secondary' : 'text-outline' }} font-semibold uppercase tracking-wide">
                        {{ $isWinning ? 'Your Bid (Highest)' : 'Current Highest Bid' }}
                    </span>
                    @if($lot->auction_ends_at)
                    <span class="font-label-sm {{ $endingImminently ? 'text-error animate-pulse' : 'text-on-surface-variant' }} font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">timer</span>
                        {{ $lot->auction_ends_at->diffForHumans(['short' => true]) }}
                    </span>
                    @endif
                </div>
                <div class="flex items-end gap-2">
                    @if($lot->highestBid)
                        <span class="font-headline-lg text-[32px] {{ $isWinning ? 'text-secondary' : 'text-primary' }} font-bold leading-none tracking-tight">₹{{ number_format($lot->highestBid->amount, 2) }}</span>
                    @else
                        <span class="font-headline-lg text-[32px] text-outline font-bold leading-none tracking-tight">₹{{ number_format($lot->base_price, 2) }}</span>
                    @endif
                    <span class="font-body-sm text-on-surface-variant mb-1">/ kg</span>
                </div>
            </div>

            @if(auth()->user()->isBuyer())
            <a href="{{ route('buyer.room', $lot) }}"
               class="btn-magnetic {{ $isWinning ? 'bg-white border border-outline-variant/50 text-on-surface-variant hover:border-primary hover:text-primary' : 'bg-secondary text-white hover:bg-primary' }} font-label-lg w-full py-3.5 rounded-xl transition-all shadow-md flex justify-center items-center gap-2 relative overflow-hidden">
                <span class="relative z-10">{{ $isWinning ? 'Update Bid' : 'Place Bid' }}</span>
                <span class="material-symbols-outlined text-[20px] relative z-10">gavel</span>
                @if(!$isWinning)
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full animate-[shimmer_2s_infinite]"></div>
                @endif
            </a>
            @elseif(auth()->user()->isSeller())
            <div class="btn-magnetic bg-surface-container text-outline font-label-lg w-full py-3.5 rounded-xl flex justify-center items-center gap-2">
                <span class="material-symbols-outlined text-[16px]">visibility</span> Viewing as Seller
            </div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($lots->hasPages())
    <div class="mt-xl flex justify-center">{{ $lots->withQueryString()->links() }}</div>
    @endif

    @else
    <div class="bg-white/70 glass-panel rounded-[2rem] p-xl text-center flex flex-col items-center gap-4">
        <span class="material-symbols-outlined text-[64px] text-outline-variant">gavel</span>
        <div class="font-headline-sm text-primary font-bold">No Active Auctions</div>
        <p class="font-body-sm text-on-surface-variant">There are no active lots matching your filters right now. Check back soon.</p>
        @if(auth()->user()->isSeller())
        <a href="{{ route('seller.create') }}" class="btn-magnetic bg-secondary text-white font-label-lg px-6 py-3 rounded-xl hover:bg-primary transition-all shadow-md mt-2 inline-flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">add</span> Create Your First Listing
        </a>
        @endif
    </div>
    @endif
@endsection
