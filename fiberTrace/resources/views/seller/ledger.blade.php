@extends('layouts.dashboard')

@section('title', 'My Inventory - FibreTrace')

@section('page-title', 'My Inventory')

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
            <h2 class="font-headline-md text-primary font-bold">Inventory & Ledger</h2>
            <p class="font-body-sm text-on-surface-variant">Manage your active listings, view historical settlements, and track stock.</p>
        </div>
        <a href="{{ route('seller.create') }}" class="btn-magnetic bg-primary text-white font-label-sm px-5 py-2.5 rounded-xl hover:bg-secondary shadow-md flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">add</span> Create Listing
        </a>
    </div>

    <!-- Status Filter Tabs -->
    <div class="flex overflow-x-auto custom-scrollbar gap-2 mb-lg pb-2 border-b border-outline-variant/30">
        @foreach(['active' => 'Active', 'pending_review' => 'Pending Review', 'settled' => 'Settled'] as $tab => $label)
        <a href="{{ route('seller.ledger') }}?status={{ $tab }}"
           class="px-5 py-3 border-b-2 {{ $status === $tab ? 'border-primary text-primary font-bold' : 'border-transparent text-on-surface-variant hover:text-primary font-medium' }} font-label-md transition-colors whitespace-nowrap">
            {{ $label }}
            <span class="ml-1.5 text-[11px] font-bold opacity-70">({{ $counts[$tab] ?? 0 }})</span>
        </a>
        @endforeach
        <a href="{{ route('seller.ledger') }}?status=all"
           class="px-5 py-3 border-b-2 {{ $status === 'all' ? 'border-primary text-primary font-bold' : 'border-transparent text-on-surface-variant hover:text-primary font-medium' }} font-label-md transition-colors whitespace-nowrap">
            All Lots
        </a>
    </div>

    <!-- Ledger Table -->
    <div class="glass-panel rounded-[1.5rem] bg-white/70 border-white/90 shadow-[0_8px_32px_rgba(0,53,39,0.03)] overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-lowest/50 border-b border-outline-variant/30">
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Lot ID</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Fiber Type</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Weight</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Listed On</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Highest Bid</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Status</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    @forelse($lots as $lot)
                    <tr class="hover:bg-surface-container-lowest transition-colors group">
                        <td class="p-4">
                            <div class="font-mono font-bold text-primary text-sm">{{ $lot->lot_number }}</div>
                        </td>
                        <td class="p-4">
                            <div class="font-body-sm text-on-surface">{{ $lot->fiber_type }}</div>
                            <div class="text-[11px] text-outline">{{ $lot->color_sorted ? 'Color Sorted' : 'Mixed Colors' }} · {{ $lot->color_description }}</div>
                        </td>
                        <td class="p-4 font-body-sm text-on-surface">{{ number_format($lot->weight_kg) }} kg</td>
                        <td class="p-4 font-body-sm text-on-surface-variant">{{ $lot->created_at->format('d M, Y') }}</td>
                        <td class="p-4">
                            @if($lot->highestBid)
                                <div class="font-label-md text-secondary font-bold">₹{{ number_format($lot->highestBid->amount, 2) }}/kg</div>
                                @if($lot->auction_ends_at && $lot->auction_ends_at->isFuture())
                                <div class="text-[11px] text-on-surface-variant font-semibold flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px] text-{{ $lot->auction_ends_at->diffInHours(now()) < 1 ? 'error' : 'on-surface-variant' }}">timer</span>
                                    {{ $lot->auction_ends_at->diffForHumans(['short' => true]) }}
                                </div>
                                @endif
                            @else
                                <div class="font-body-sm text-outline italic">No bids yet</div>
                            @endif
                        </td>
                        <td class="p-4">
                            @php
                                $statusMap = [
                                    'active'         => ['bg-secondary-container/30 text-secondary border-secondary/20', 'ACTIVE'],
                                    'pending_review' => ['bg-primary/10 text-primary border-primary/20', 'PENDING'],
                                    'awarded'        => ['bg-tertiary/10 text-tertiary border-tertiary/20', 'AWARDED'],
                                    'settled'        => ['bg-outline/10 text-outline border-outline/20', 'SETTLED'],
                                    'cancelled'      => ['bg-error/10 text-error border-error/20', 'CANCELLED'],
                                    'suspended'      => ['bg-error/10 text-error border-error/20', 'SUSPENDED'],
                                ];
                                [$cls, $lbl] = $statusMap[$lot->status] ?? ['bg-outline/10 text-outline border-outline/20', strtoupper($lot->status)];
                            @endphp
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full border {{ $cls }}">{{ $lbl }}</span>
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('seller.lot.show', $lot) }}" class="p-2 text-on-surface-variant hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="View Details">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </a>
                                @if($lot->status === 'active' && $lot->highestBid)
                                <form method="POST" action="{{ route('seller.lot.accept', $lot) }}" onsubmit="return confirm('Accept highest bid of ₹{{ $lot->highestBid->amount }}/kg?')">
                                    @csrf
                                    <button type="submit" class="p-2 text-on-surface-variant hover:text-secondary hover:bg-secondary/10 rounded-lg transition-colors" title="Accept Highest Bid">
                                        <span class="material-symbols-outlined text-[18px]">gavel</span>
                                    </button>
                                </form>
                                @endif
                                @if(in_array($lot->status, ['active', 'pending_review', 'draft']))
                                <form method="POST" action="{{ route('seller.lot.cancel', $lot) }}" onsubmit="return confirm('Cancel lot {{ $lot->lot_number }}? This cannot be undone.')">
                                    @csrf
                                    <button type="submit" class="p-2 text-on-surface-variant hover:text-error hover:bg-error/10 rounded-lg transition-colors" title="Cancel Lot">
                                        <span class="material-symbols-outlined text-[18px]">close</span>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-on-surface-variant font-body-sm">
                            <span class="material-symbols-outlined text-[48px] text-outline-variant block mb-2">inbox</span>
                            No {{ $status }} lots found.
                            @if($status === 'active')
                            <a href="{{ route('seller.create') }}" class="text-primary font-bold hover:underline ml-1">Create your first listing →</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($lots->hasPages())
        <div class="p-4 border-t border-outline-variant/30 flex items-center justify-between bg-surface-container-lowest/30">
            <span class="font-body-sm text-outline-variant">Showing {{ $lots->firstItem() }} to {{ $lots->lastItem() }} of {{ $lots->total() }} lots</span>
            <div>{{ $lots->withQueryString()->links() }}</div>
        </div>
        @endif
    </div>
@endsection
