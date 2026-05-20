@extends('layouts.admin')

@section('title', 'Admin Dashboard - FibreTrace')

@section('page-title', 'Control Panel')

@section('admin-content')
    <div class="mb-lg">
        <h2 class="font-headline-md text-[#111] font-bold">System Overview</h2>
        <p class="font-body-sm text-on-surface-variant">Real-time platform metrics and pending action alerts.</p>
    </div>

    {{-- Session Flash Messages --}}
    @if (session('success'))
        <div class="bg-secondary/10 border border-secondary/30 text-secondary font-label-sm px-4 py-3 rounded-xl mb-lg flex items-center gap-3">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Alert Banner (only show if pending verifications exist) --}}
    @if ($stats['pending_verifications'] > 0)
    <div class="bg-error-container/20 border border-error/30 rounded-xl p-md flex items-start gap-4 mb-lg">
        <div class="w-10 h-10 rounded-full bg-error/10 text-error flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-[24px]">priority_high</span>
        </div>
        <div class="flex-1">
            <h4 class="font-label-lg text-error font-bold mb-1">Pending GSTIN Verifications ({{ $stats['pending_verifications'] }})</h4>
            <p class="font-body-sm text-on-surface-variant mb-3">There is a backlog of factory registrations waiting for manual verification. Please review them to allow users to start trading.</p>
            <a href="{{ route('admin.verifications') }}" class="btn-magnetic bg-error text-white font-label-sm px-5 py-2.5 rounded-lg hover:bg-error/90 shadow-sm inline-flex items-center gap-2">
                Go to Verification Queue <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>
    </div>
    @endif

    {{-- Alert Banner for Pending Lots --}}
    @if ($stats['pending_lots'] > 0)
    <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-md flex items-start gap-4 mb-xl">
        <div class="w-10 h-10 rounded-full bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-[24px]">gavel</span>
        </div>
        <div class="flex-1">
            <h4 class="font-label-lg text-amber-800 font-bold mb-1">Pending Lot Listings ({{ $stats['pending_lots'] }})</h4>
            <p class="font-body-sm text-on-surface-variant mb-3">Sellers have submitted new fiber listings for moderation. Please review and approve them so buyers can place bids.</p>
            <a href="{{ route('admin.moderation') }}?filter=pending" class="btn-magnetic bg-amber-600 text-white font-label-sm px-5 py-2.5 rounded-lg hover:bg-amber-600/90 shadow-sm inline-flex items-center gap-2">
                Go to Moderation Queue <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>
    </div>
    @endif

    <!-- KPI Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-md mb-xl">
        <!-- KPI 1: Active Listings -->
        <div class="bg-white rounded-2xl p-lg border border-outline-variant/30 shadow-[0_4px_24px_rgba(0,0,0,0.02)]">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[24px]">gavel</span>
                </div>
            </div>
            <div class="font-label-sm text-outline font-bold uppercase tracking-wider mb-1">Active Listings</div>
            <div class="font-headline-lg text-[#111] font-bold">{{ number_format($stats['active_auctions']) }}</div>
            <div class="font-body-sm text-on-surface-variant mt-2 text-[12px]">Across all categories</div>
        </div>

        <!-- KPI 2: Live Bids -->
        <div class="bg-white rounded-2xl p-lg border border-outline-variant/30 shadow-[0_4px_24px_rgba(0,0,0,0.02)]">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[24px]">sync_alt</span>
                </div>
            </div>
            <div class="font-label-sm text-outline font-bold uppercase tracking-wider mb-1">Live Bids</div>
            <div class="font-headline-lg text-[#111] font-bold">{{ number_format($stats['live_bids']) }}</div>
            <div class="font-body-sm text-on-surface-variant mt-2 text-[12px]">Currently active bids</div>
        </div>

        <!-- KPI 3: Escrow Volume -->
        <div class="bg-white rounded-2xl p-lg border border-outline-variant/30 shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-tertiary/10 rounded-full blur-xl"></div>
            <div class="flex justify-between items-start mb-4 relative z-10">
                <div class="w-12 h-12 rounded-xl bg-tertiary/10 text-tertiary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[24px]">payments</span>
                </div>
            </div>
            <div class="font-label-sm text-outline font-bold uppercase tracking-wider mb-1 relative z-10">Escrow Volume</div>
            <div class="font-headline-lg text-[#111] font-bold relative z-10">₹{{ number_format($stats['escrow_volume'] / 100000, 1) }}L</div>
            <div class="font-body-sm text-on-surface-variant mt-2 text-[12px] relative z-10">Currently in transit</div>
        </div>

        <!-- KPI 4: Active Traders -->
        <div class="bg-white rounded-2xl p-lg border border-outline-variant/30 shadow-[0_4px_24px_rgba(0,0,0,0.02)]">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-[#111]/5 text-[#111] flex items-center justify-center">
                    <span class="material-symbols-outlined text-[24px]">group</span>
                </div>
            </div>
            <div class="font-label-sm text-outline font-bold uppercase tracking-wider mb-1">Verified Traders</div>
            <div class="font-headline-lg text-[#111] font-bold">{{ number_format($stats['verified_users']) }}</div>
            <div class="font-body-sm text-on-surface-variant mt-2 text-[12px]">{{ $stats['total_users'] }} total registered</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-lg mb-xl">
        <!-- Pending Verifications Quick Panel -->
        @if($pendingUsers->isNotEmpty())
        <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
            <div class="p-lg border-b border-outline-variant/30 flex justify-between items-center">
                <h3 class="font-label-lg text-[#111] font-bold">Verification Queue</h3>
                <a href="{{ route('admin.verifications') }}" class="text-primary font-label-sm hover:underline">View All →</a>
            </div>
            <div class="flex flex-col divide-y divide-outline-variant/10">
                @foreach($pendingUsers as $pending)
                <div class="p-4 flex items-center gap-3 hover:bg-surface-container-lowest transition-colors">
                    <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0 font-bold text-sm">
                        {{ strtoupper(substr($pending->company_name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-label-md text-[#111] font-semibold truncate">{{ $pending->company_name }}</div>
                        <div class="text-[11px] text-on-surface-variant">{{ ucfirst($pending->role) }} • {{ $pending->city }}, {{ $pending->state }}</div>
                    </div>
                    <div class="ml-auto text-[11px] text-outline font-medium whitespace-nowrap">{{ $pending->created_at->diffForHumans() }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Flagged Lots Panel -->
        @if($flaggedLots->isNotEmpty())
        <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
            <div class="p-lg border-b border-outline-variant/30 flex justify-between items-center">
                <h3 class="font-label-lg text-[#111] font-bold">Flagged Listings</h3>
                <a href="{{ route('admin.moderation') }}" class="text-primary font-label-sm hover:underline">View All →</a>
            </div>
            <div class="flex flex-col divide-y divide-outline-variant/10">
                @foreach($flaggedLots as $lot)
                <div class="p-4 flex items-center gap-3 hover:bg-surface-container-lowest transition-colors">
                    <div class="w-9 h-9 rounded-full bg-error/10 text-error flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[18px]">flag</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-label-md text-[#111] font-semibold truncate">{{ $lot->lot_number }} — {{ $lot->fiber_type }}</div>
                        <div class="text-[11px] text-on-surface-variant">{{ $lot->weight_kg }}kg • {{ $lot->seller->company_name ?? 'Unknown' }}</div>
                    </div>
                    <span class="bg-error/10 text-error text-[10px] font-bold px-2 py-0.5 rounded-full border border-error/20">{{ $lot->flag_count }} flags</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($pendingUsers->isEmpty() && $flaggedLots->isEmpty())
        <!-- All Clear Panel -->
        <div class="bg-white rounded-2xl border border-secondary/30 shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-lg col-span-2 flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-secondary/10 text-secondary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[28px]">verified</span>
            </div>
            <div>
                <div class="font-label-lg text-secondary font-bold">All Clear</div>
                <div class="font-body-sm text-on-surface-variant">No pending verifications or flagged lots. The platform is running smoothly.</div>
            </div>
        </div>
        @endif
    </div>

    <!-- Recent Activity Log (live from DB via $flaggedLots / system) -->
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
        <div class="p-lg border-b border-outline-variant/30 flex justify-between items-center">
            <h3 class="font-label-lg text-[#111] font-bold">System Stats Summary</h3>
            <a href="{{ route('admin.audit') }}" class="text-primary font-label-sm hover:underline">Full Audit Log →</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-y sm:divide-y-0 divide-outline-variant/20">
            <div class="p-lg text-center">
                <div class="font-headline-md text-primary font-bold">{{ $stats['active_auctions'] }}</div>
                <div class="font-label-sm text-outline mt-1">Active Auctions</div>
            </div>
            <div class="p-lg text-center">
                <div class="font-headline-md text-secondary font-bold">{{ $stats['pending_verifications'] }}</div>
                <div class="font-label-sm text-outline mt-1">Pending KYC</div>
            </div>
            <div class="p-lg text-center">
                <div class="font-headline-md text-[#111] font-bold">{{ $stats['flagged_lots'] }}</div>
                <div class="font-label-sm text-outline mt-1">Flagged Lots</div>
            </div>
            <div class="p-lg text-center">
                <div class="font-headline-md text-error font-bold">{{ $stats['pending_settlements'] }}</div>
                <div class="font-label-sm text-outline mt-1">Pending Settlements</div>
            </div>
        </div>
    </div>
@endsection
