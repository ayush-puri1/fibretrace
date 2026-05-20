@extends('layouts.dashboard')

@section('title', 'Dashboard - FibreTrace')

@section('page-title', 'Overview')

@section('dashboard-content')

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="bg-secondary/10 border border-secondary/30 text-secondary font-label-sm px-4 py-3 rounded-xl mb-lg flex items-center gap-3">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Greeting --}}
    <div class="mb-lg">
        <h2 class="font-headline-md text-primary font-bold">
            Welcome back, {{ $user->name }} 👋
        </h2>
        <p class="font-body-sm text-on-surface-variant">{{ $user->company_name }} · {{ ucfirst($user->role) }} Account</p>
    </div>

    {{-- ── SELLER Stats ──────────────────────────────────────────── --}}
    @if($user->isSeller())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-md mb-xl">
        <div class="bg-white rounded-2xl p-lg border border-outline-variant/30 shadow-[0_4px_24px_rgba(0,0,0,0.02)]">
            <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[24px]">inventory_2</span>
            </div>
            <div class="font-label-sm text-outline font-bold uppercase tracking-wider mb-1">Active Listings</div>
            <div class="font-headline-lg text-[#111] font-bold">{{ $stats['activeLots'] }}</div>
            <div class="font-body-sm text-on-surface-variant mt-1 text-[12px]">Currently on market floor</div>
        </div>
        <div class="bg-white rounded-2xl p-lg border border-outline-variant/30 shadow-[0_4px_24px_rgba(0,0,0,0.02)]">
            <div class="w-12 h-12 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[24px]">pending</span>
            </div>
            <div class="font-label-sm text-outline font-bold uppercase tracking-wider mb-1">Under Review</div>
            <div class="font-headline-lg text-[#111] font-bold">{{ $stats['pendingLots'] }}</div>
            <div class="font-body-sm text-on-surface-variant mt-1 text-[12px]">Awaiting admin approval</div>
        </div>
        <div class="bg-white rounded-2xl p-lg border border-outline-variant/30 shadow-[0_4px_24px_rgba(0,0,0,0.02)]">
            <div class="w-12 h-12 rounded-xl bg-tertiary/10 text-tertiary flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[24px]">check_circle</span>
            </div>
            <div class="font-label-sm text-outline font-bold uppercase tracking-wider mb-1">Settled Lots</div>
            <div class="font-headline-lg text-[#111] font-bold">{{ $stats['settledLots'] }}</div>
            <div class="font-body-sm text-on-surface-variant mt-1 text-[12px]">Completed transactions</div>
        </div>
        <div class="bg-white rounded-2xl p-lg border border-outline-variant/30 shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-secondary/10 rounded-full blur-xl"></div>
            <div class="w-12 h-12 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center mb-4 relative z-10">
                <span class="material-symbols-outlined text-[24px]">payments</span>
            </div>
            <div class="font-label-sm text-outline font-bold uppercase tracking-wider mb-1 relative z-10">Total Revenue</div>
            <div class="font-headline-lg text-[#111] font-bold relative z-10">₹{{ number_format($stats['totalRevenue'] / 1000, 1) }}K</div>
            <div class="font-body-sm text-on-surface-variant mt-1 text-[12px] relative z-10">From released escrow</div>
        </div>
    </div>

    {{-- Recent Lots Table --}}
    @if($recentLots->isNotEmpty())
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden mb-xl">
        <div class="p-lg border-b border-outline-variant/30 flex justify-between items-center">
            <h3 class="font-label-lg text-[#111] font-bold">Recent Listings</h3>
            <a href="{{ route('seller.ledger') }}" class="text-primary font-label-sm hover:underline">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-surface-container-lowest/50 border-b border-outline-variant/20">
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Lot #</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Fiber Type</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Weight</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Top Bid</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @foreach($recentLots as $lot)
                    <tr class="hover:bg-surface-container-lowest transition-colors">
                        <td class="p-4 font-mono text-sm font-bold text-primary">{{ $lot->lot_number }}</td>
                        <td class="p-4 font-body-sm text-[#111]">{{ $lot->fiber_type }}</td>
                        <td class="p-4 font-body-sm text-on-surface-variant">{{ number_format($lot->weight_kg) }} kg</td>
                        <td class="p-4 font-label-md font-bold text-secondary">
                            {{ $lot->highestBid ? '₹' . number_format($lot->highestBid->amount, 2) . '/kg' : '—' }}
                        </td>
                        <td class="p-4">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full capitalize
                                {{ $lot->status === 'active' ? 'bg-secondary/10 text-secondary border border-secondary/20' : '' }}
                                {{ $lot->status === 'draft' ? 'bg-outline/10 text-outline border border-outline/20' : '' }}
                                {{ $lot->status === 'settled' ? 'bg-primary/10 text-primary border border-primary/20' : '' }}
                                {{ $lot->status === 'awarded' ? 'bg-tertiary/10 text-tertiary border border-tertiary/20' : '' }}
                                {{ $lot->status === 'cancelled' ? 'bg-error/10 text-error border border-error/20' : '' }}">
                                {{ str_replace('_', ' ', $lot->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
        <a href="{{ route('seller.create') }}" class="btn-magnetic glass-panel bg-white/70 border-white p-lg rounded-[1.5rem] flex items-center gap-4 hover:shadow-[0_8px_24px_rgba(0,108,73,0.1)] transition-all group">
            <div class="w-14 h-14 rounded-2xl bg-secondary/10 text-secondary flex items-center justify-center group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-[28px]">add_box</span>
            </div>
            <div>
                <div class="font-label-lg text-primary font-bold">Create New Listing</div>
                <div class="font-body-sm text-on-surface-variant">Add a new waste lot to the market floor</div>
            </div>
        </a>
        <a href="{{ route('auctions') }}" class="btn-magnetic glass-panel bg-white/70 border-white p-lg rounded-[1.5rem] flex items-center gap-4 hover:shadow-[0_8px_24px_rgba(0,108,73,0.1)] transition-all group">
            <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-[28px]">gavel</span>
            </div>
            <div>
                <div class="font-label-lg text-primary font-bold">Browse Auctions</div>
                <div class="font-body-sm text-on-surface-variant">View all active listings on the market</div>
            </div>
        </a>
    </div>

    {{-- ── BUYER Stats ──────────────────────────────────────────── --}}
    @else
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-md mb-xl">
        <div class="bg-white rounded-2xl p-lg border border-outline-variant/30 shadow-[0_4px_24px_rgba(0,0,0,0.02)]">
            <div class="w-12 h-12 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[24px]">gavel</span>
            </div>
            <div class="font-label-sm text-outline font-bold uppercase tracking-wider mb-1">Active Bids</div>
            <div class="font-headline-lg text-[#111] font-bold">{{ $stats['activeBids'] }}</div>
            <div class="font-body-sm text-on-surface-variant mt-1 text-[12px]">Bids you are currently leading</div>
        </div>
        <div class="bg-white rounded-2xl p-lg border border-outline-variant/30 shadow-[0_4px_24px_rgba(0,0,0,0.02)]">
            <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[24px]">emoji_events</span>
            </div>
            <div class="font-label-sm text-outline font-bold uppercase tracking-wider mb-1">Lots Won</div>
            <div class="font-headline-lg text-[#111] font-bold">{{ $stats['wonBids'] }}</div>
            <div class="font-body-sm text-on-surface-variant mt-1 text-[12px]">Auctions you have won</div>
        </div>
        <div class="bg-white rounded-2xl p-lg border border-outline-variant/30 shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-tertiary/10 rounded-full blur-xl"></div>
            <div class="w-12 h-12 rounded-xl bg-tertiary/10 text-tertiary flex items-center justify-center mb-4 relative z-10">
                <span class="material-symbols-outlined text-[24px]">payments</span>
            </div>
            <div class="font-label-sm text-outline font-bold uppercase tracking-wider mb-1 relative z-10">Total Spend</div>
            <div class="font-headline-lg text-[#111] font-bold relative z-10">₹{{ number_format($stats['totalSpend'] / 1000, 1) }}K</div>
            <div class="font-body-sm text-on-surface-variant mt-1 text-[12px] relative z-10">Across all purchases</div>
        </div>
    </div>

    {{-- Quick Actions for Buyer --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-md mb-xl">
        <a href="{{ route('auctions') }}" class="btn-magnetic glass-panel bg-white/70 border-white p-lg rounded-[1.5rem] flex items-center gap-4 hover:shadow-[0_8px_24px_rgba(0,108,73,0.1)] transition-all group">
            <div class="w-14 h-14 rounded-2xl bg-secondary/10 text-secondary flex items-center justify-center group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-[28px]">gavel</span>
            </div>
            <div>
                <div class="font-label-lg text-primary font-bold">Browse Auctions</div>
                <div class="font-body-sm text-on-surface-variant">Find and bid on live material lots</div>
            </div>
        </a>
        <a href="{{ route('buyer.bids') }}" class="btn-magnetic glass-panel bg-white/70 border-white p-lg rounded-[1.5rem] flex items-center gap-4 hover:shadow-[0_8px_24px_rgba(0,108,73,0.1)] transition-all group">
            <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-[28px]">receipt_long</span>
            </div>
            <div>
                <div class="font-label-lg text-primary font-bold">My Bids</div>
                <div class="font-body-sm text-on-surface-variant">Track all your active and won bids</div>
            </div>
        </a>
    </div>
    @endif

    {{-- Market Prices Ticker --}}
    @if($marketPrices->isNotEmpty())
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden mt-xl">
        <div class="p-lg border-b border-outline-variant/30 flex justify-between items-center">
            <h3 class="font-label-lg text-[#111] font-bold">Weekly Market Index</h3>
            <a href="{{ route('market') }}" class="text-primary font-label-sm hover:underline">Full Index →</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 divide-x divide-outline-variant/20">
            @foreach($marketPrices as $price)
            <div class="p-4 text-center">
                <div class="text-[10px] text-outline uppercase font-bold tracking-wider mb-1 truncate">{{ Str::limit($price->fiber_category, 15) }}</div>
                <div class="font-label-lg text-primary font-bold">₹{{ number_format($price->price_per_kg, 2) }}</div>
                @if($price->previous_price)
                    @php $diff = $price->price_per_kg - $price->previous_price; @endphp
                    <div class="text-[11px] font-bold mt-1 {{ $diff >= 0 ? 'text-secondary' : 'text-error' }} flex items-center justify-center gap-0.5">
                        <span class="material-symbols-filled text-[12px]">{{ $diff >= 0 ? 'arrow_upward' : 'arrow_downward' }}</span>
                        ₹{{ abs($diff) }}
                    </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Recent Transactions --}}
    @if($recentTransactions->isNotEmpty())
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden mt-xl">
        <div class="p-lg border-b border-outline-variant/30 flex justify-between items-center">
            <h3 class="font-label-lg text-[#111] font-bold">Recent Transactions</h3>
        </div>
        <div class="flex flex-col divide-y divide-outline-variant/10">
            @foreach($recentTransactions as $txn)
            <div class="p-4 flex items-center gap-3 hover:bg-surface-container-lowest transition-colors">
                <div class="w-9 h-9 rounded-full bg-{{ $txn->payment_status === 'released' ? 'secondary' : 'primary' }}/10 text-{{ $txn->payment_status === 'released' ? 'secondary' : 'primary' }} flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[18px]">{{ $txn->payment_status === 'released' ? 'check_circle' : 'pending' }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-label-md text-[#111] font-semibold">{{ $txn->transaction_number }}</div>
                    <div class="text-[11px] text-on-surface-variant">{{ $txn->lot->fiber_type ?? 'N/A' }} · {{ number_format($txn->total_amount, 2) }}</div>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full capitalize
                    {{ $txn->payment_status === 'released' ? 'bg-secondary/10 text-secondary border border-secondary/20' : 'bg-primary/10 text-primary border border-primary/20' }}">
                    {{ $txn->payment_status }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

@endsection
