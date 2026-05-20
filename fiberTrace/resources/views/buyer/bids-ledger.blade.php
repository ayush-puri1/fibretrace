@extends('layouts.dashboard')

@section('title', 'My Bids - FibreTrace')

@section('page-title', 'My Bids Ledger')

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

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-md mb-lg">
        <div>
            <h2 class="font-headline-md text-primary font-bold">Active Bids Tracker</h2>
            <p class="font-body-sm text-on-surface-variant">Monitor all lots where you have placed a bid.</p>
        </div>
        <a href="{{ route('auctions') }}" class="btn-magnetic bg-surface-container-lowest text-primary border border-outline-variant/50 font-label-sm px-5 py-2.5 rounded-xl hover:border-primary shadow-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">gavel</span> Browse More Lots
        </a>
    </div>

    @if($bids->isNotEmpty())
    <!-- Stats summary -->
    @php
        $winning = $bids->where('status', 'active')->count();
        $outbid  = $bids->where('status', 'outbid')->count();
        $won     = $bids->where('status', 'won')->count();
    @endphp
    <div class="flex flex-wrap gap-3 mb-lg">
        @if($winning > 0)
        <div class="bg-secondary-container/20 border border-secondary/30 rounded-xl px-4 py-2 flex items-center gap-3">
            <span class="w-3 h-3 rounded-full bg-secondary animate-pulse"></span>
            <span class="font-label-sm text-secondary font-bold uppercase tracking-wider">{{ $winning }} Winning</span>
        </div>
        @endif
        @if($outbid > 0)
        <div class="bg-error/5 border border-error/20 rounded-xl px-4 py-2 flex items-center gap-3">
            <span class="w-3 h-3 rounded-full bg-error"></span>
            <span class="font-label-sm text-error font-bold uppercase tracking-wider">{{ $outbid }} Outbid</span>
        </div>
        @endif
        @if($won > 0)
        <div class="bg-primary/10 border border-primary/20 rounded-xl px-4 py-2 flex items-center gap-3">
            <span class="material-symbols-filled text-[14px] text-primary">emoji_events</span>
            <span class="font-label-sm text-primary font-bold uppercase tracking-wider">{{ $won }} Won</span>
        </div>
        @endif
    </div>

    <!-- Bids Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-lg">
        @foreach($bids as $bid)
        @php
            $lot = $bid->lot;
            $isWinning = $bid->status === 'active';
            $isOutbid  = $bid->status === 'outbid';
            $isWon     = $bid->status === 'won';
        @endphp
        <div class="glass-panel bg-white/70 rounded-[1.5rem] border {{ $isWinning ? 'border-secondary/30' : ($isOutbid ? 'border-error/30' : 'border-white') }} p-5 flex flex-col relative overflow-hidden group hover:shadow-[0_12px_32px_rgba(0,108,73,0.1)] transition-all duration-300">

            @if($isWinning)
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-secondary to-primary"></div>
            @elseif($isOutbid)
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-error to-error-container"></div>
            @elseif($isWon)
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-primary to-tertiary"></div>
            @endif

            <div class="flex justify-between items-start mb-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        @if($isWinning)
                            <span class="bg-secondary/10 text-secondary text-[10px] font-bold px-2 py-0.5 rounded-full border border-secondary/20 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-secondary animate-ping"></span> WINNING
                            </span>
                        @elseif($isOutbid)
                            <span class="bg-error/10 text-error text-[10px] font-bold px-2 py-0.5 rounded-full border border-error/20 flex items-center gap-1">
                                <span class="material-symbols-filled text-[10px]">warning</span> OUTBID
                            </span>
                        @elseif($isWon)
                            <span class="bg-primary/10 text-primary text-[10px] font-bold px-2 py-0.5 rounded-full border border-primary/20 flex items-center gap-1">
                                <span class="material-symbols-filled text-[10px]">emoji_events</span> WON
                            </span>
                        @else
                            <span class="bg-outline/10 text-outline text-[10px] font-bold px-2 py-0.5 rounded-full border border-outline/20">{{ strtoupper($bid->status) }}</span>
                        @endif
                        <span class="text-[12px] font-bold text-on-surface-variant">#{{ $lot->lot_number ?? 'N/A' }}</span>
                    </div>
                    <h3 class="font-headline-sm text-primary font-bold">{{ $lot->fiber_type ?? 'Unknown Lot' }}</h3>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 mb-5">
                @if($lot)
                <span class="bg-surface-container-low text-on-surface-variant text-[11px] px-2.5 py-1 rounded-md font-semibold border border-outline-variant/30">{{ number_format($lot->weight_kg) }} kg</span>
                <span class="bg-surface-container-low text-on-surface-variant text-[11px] px-2.5 py-1 rounded-md font-semibold border border-outline-variant/30">{{ $lot->seller->city ?? 'N/A' }}</span>
                @endif
            </div>

            <div class="{{ $isWinning ? 'bg-secondary-container/10 border-secondary/20' : ($isOutbid ? 'bg-surface-container-lowest border-error/30' : 'bg-surface-container-lowest border-outline-variant/30') }} border rounded-xl p-4 flex flex-col gap-1 mb-5 relative overflow-hidden">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-label-sm {{ $isWinning ? 'text-secondary' : ($isOutbid ? 'text-error' : 'text-outline') }} font-semibold uppercase tracking-wide">
                        {{ $isWinning ? 'Your Bid (Highest)' : ($isOutbid ? 'Current Highest' : 'Your Bid') }}
                    </span>
                    @if($lot && $lot->auction_ends_at && $lot->auction_ends_at->isFuture())
                    <span class="font-label-sm {{ $isOutbid ? 'text-error animate-pulse' : 'text-on-surface-variant' }} font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">timer</span>
                        {{ $lot->auction_ends_at->diffForHumans(['short' => true]) }}
                    </span>
                    @endif
                </div>
                <div class="flex items-end gap-2">
                    @if($isOutbid && $lot->highestBid)
                        <span class="font-headline-lg text-[28px] text-primary font-bold leading-none tracking-tight">₹{{ number_format($lot->highestBid->amount, 2) }}</span>
                    @else
                        <span class="font-headline-lg text-[28px] {{ $isWinning ? 'text-secondary' : 'text-primary' }} font-bold leading-none tracking-tight">₹{{ number_format($bid->amount, 2) }}</span>
                    @endif
                    <span class="font-body-sm text-on-surface-variant mb-1">/ kg</span>
                </div>
                @if($isOutbid)
                <div class="text-[11px] text-outline font-medium mt-1">Your bid was ₹{{ number_format($bid->amount, 2) }}</div>
                @endif
            </div>

            <div class="mt-auto flex gap-2">
                @if($lot && $lot->status === 'active' && $lot->auction_ends_at && $lot->auction_ends_at->isFuture())
                    @if($isOutbid)
                    <a href="{{ route('buyer.room', $lot) }}" class="btn-magnetic bg-secondary text-white font-label-md flex-1 py-3 rounded-xl hover:bg-primary transition-all shadow-sm flex justify-center items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">gavel</span> Increase Bid
                    </a>
                    @else
                    <a href="{{ route('buyer.room', $lot) }}" class="bg-white border border-outline-variant/50 text-on-surface-variant font-label-md flex-1 py-3 rounded-xl hover:bg-surface-container-low transition-colors flex justify-center items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">visibility</span> View Room
                    </a>
                    @endif
                @elseif($isWon && $lot)
                    <a href="{{ route('settlement.show', $lot->transaction) }}" class="btn-magnetic bg-primary text-white font-label-md flex-1 py-3 rounded-xl hover:bg-secondary transition-all shadow-sm flex justify-center items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">receipt_long</span> View Settlement
                    </a>
                @else
                    <div class="bg-surface-container-low text-outline font-label-md flex-1 py-3 rounded-xl flex justify-center items-center gap-2">
                        Auction Ended
                    </div>
                @endif
                @if($isWinning)
                <form method="POST" action="{{ route('buyer.bid.cancel', $bid) }}" onsubmit="return confirm('Cancel your bid on this lot?')">
                    @csrf
                    <button type="submit" class="p-3 text-error hover:bg-error/10 rounded-xl transition-colors border border-error/20" title="Cancel Bid">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($bids->hasPages())
    <div class="mt-xl flex justify-center">{{ $bids->withQueryString()->links() }}</div>
    @endif

    @else
    <div class="bg-white/70 glass-panel rounded-[2rem] p-xl text-center flex flex-col items-center gap-4">
        <span class="material-symbols-outlined text-[64px] text-outline-variant">gavel</span>
        <div class="font-headline-sm text-primary font-bold">No Bids Yet</div>
        <p class="font-body-sm text-on-surface-variant">You haven't placed any bids. Browse the live auction floor to find materials.</p>
        <a href="{{ route('auctions') }}" class="btn-magnetic bg-secondary text-white font-label-lg px-6 py-3 rounded-xl hover:bg-primary transition-all shadow-md mt-2 inline-flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">gavel</span> Browse Auctions
        </a>
    </div>
    @endif
@endsection
