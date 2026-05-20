@extends('layouts.admin')

@section('title', 'Listing Moderation - FibreTrace Admin')

@section('page-title', 'Moderation Center')

@section('admin-content')
    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="bg-secondary/10 border border-secondary/30 text-secondary font-label-sm px-4 py-3 rounded-xl mb-lg flex items-center gap-3">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-md mb-lg">
        <div>
            <h2 class="font-headline-md text-[#111] font-bold">Active Listings Moderation</h2>
            <p class="font-body-sm text-on-surface-variant">Review live auction lots for terms of service violations.</p>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex gap-2 mb-lg flex-wrap">
        @foreach(['pending' => 'Pending Review', 'flagged' => 'Flagged', 'all' => 'Active', 'suspended' => 'Suspended'] as $tab => $label)
        <a href="{{ route('admin.moderation') }}?filter={{ $tab }}"
           class="px-4 py-2 rounded-lg font-label-sm font-semibold border transition-colors
                  {{ $filter === $tab ? 'bg-[#111] text-white border-[#111] shadow-sm' : 'bg-white text-on-surface-variant border-outline-variant/50 hover:bg-surface-container-lowest' }}">
            {{ $label }}
            <span class="ml-1.5 text-[11px] font-bold opacity-70">({{ $counts[$tab] }})</span>
        </a>
        @endforeach
    </div>

    <!-- Active Lots Grid (Admin View) -->
    @if($lots->isNotEmpty())
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-md">
        @foreach($lots as $lot)
        <div class="bg-white rounded-[1.5rem] border {{ $lot->flagged ? 'border-error/40 shadow-[0_4px_24px_rgba(186,26,26,0.05)]' : 'border-outline-variant/30 shadow-[0_4px_24px_rgba(0,0,0,0.02)]' }} p-4 flex flex-col relative group">
            @if($lot->flagged)
                <div class="absolute inset-x-0 top-0 h-1 bg-error rounded-t-[1.5rem]"></div>
            @endif
            
            <div class="flex justify-between items-start mb-3">
                <div class="flex items-center gap-2">
                    <span class="font-mono text-[11px] font-bold text-primary bg-primary/10 px-2 py-0.5 rounded">#{{ $lot->lot_number }}</span>
                    @if($lot->flagged)
                        <span class="bg-error/10 text-error text-[10px] px-1.5 py-0.5 rounded font-bold flex items-center gap-1">
                            <span class="material-symbols-outlined text-[12px]">warning</span> Flagged ({{ $lot->flag_count }})
                        </span>
                    @else
                        <span class="text-[10px] text-outline font-semibold">Listed {{ $lot->created_at->diffForHumans() }}</span>
                    @endif
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex gap-3 mb-4">
                <!-- Image Thumbnail or Placeholder -->
                <div class="w-20 h-20 bg-surface-container-low rounded-xl flex items-center justify-center shrink-0 border border-outline-variant/30 relative group-hover:border-primary/50 transition-colors overflow-hidden">
                    @if($lot->images->isNotEmpty())
                        <img src="{{ asset('storage/' . $lot->images->first()->file_path) }}" class="w-full h-full object-cover" alt="{{ $lot->fiber_type }}" onerror="this.parentElement.innerHTML='<span class=\'material-symbols-outlined text-[24px] text-outline-variant/50\'>image</span>'">
                    @else
                        <span class="material-symbols-outlined text-[24px] text-outline-variant/50">image</span>
                    @endif
                    @if($lot->images->count() > 1)
                    <div class="absolute bottom-1 right-1 bg-black/60 text-white text-[9px] px-1.5 rounded font-bold">{{ $lot->images->count() }}</div>
                    @endif
                </div>
                <div>
                    <h3 class="font-label-md text-[#111] font-bold leading-tight mb-1">{{ $lot->fiber_type }}</h3>
                    <div class="text-[11px] text-on-surface-variant flex flex-col gap-0.5">
                        <span>{{ number_format($lot->weight_kg) }} kg • {{ $lot->color_description }}</span>
                        <span>Seller: {{ $lot->seller->company_name ?? 'Unknown' }}</span>
                        <span class="flex items-center gap-1.5 mt-1 text-[11px]">
                            Status: 
                            @if($lot->status === 'pending_review')
                                <span class="bg-amber-100 text-amber-800 text-[10px] px-2 py-0.5 rounded-full font-bold">Pending Review</span>
                            @elseif($lot->status === 'active')
                                <span class="bg-emerald-100 text-emerald-800 text-[10px] px-2 py-0.5 rounded-full font-bold">Active</span>
                            @elseif($lot->status === 'suspended')
                                <span class="bg-rose-100 text-rose-800 text-[10px] px-2 py-0.5 rounded-full font-bold">Suspended</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 text-[10px] px-2 py-0.5 rounded-full font-bold capitalize">{{ $lot->status }}</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            @if($lot->flagged && $lot->reports->isNotEmpty())
            <div class="bg-error-container/20 rounded-lg p-2 mb-3 border border-error/20">
                <div class="text-[10px] text-error font-bold uppercase tracking-wider mb-0.5">User Report</div>
                <div class="text-[11px] text-on-surface-variant italic">"{{ Str::limit($lot->reports->first()->reason, 80) }}"</div>
            </div>
            @endif

            <div class="border-t border-outline-variant/20 pt-3 mt-auto flex justify-between items-center">
                <div class="flex flex-col">
                    <span class="text-[10px] text-outline uppercase font-bold tracking-wider">Highest Bid</span>
                    @if($lot->highestBid)
                        <span class="font-label-md text-secondary font-bold">₹{{ number_format($lot->highestBid->amount, 2) }}/kg</span>
                    @else
                        <span class="font-label-md text-outline-variant font-bold">None</span>
                    @endif
                </div>
                <div class="flex gap-2">
                    @if($lot->status === 'suspended')
                        <form method="POST" action="{{ route('admin.moderation.restore', $lot) }}">
                            @csrf
                            <button type="submit" class="btn-magnetic bg-secondary/10 text-secondary hover:bg-secondary/20 font-label-sm px-3 py-1.5 rounded-lg transition-colors font-semibold">
                                Restore
                            </button>
                        </form>
                    @elseif($lot->status === 'pending_review')
                        <form method="POST" action="{{ route('admin.moderation.approve', $lot) }}">
                            @csrf
                            <button type="submit" class="btn-magnetic bg-primary text-white hover:bg-primary/90 font-label-sm px-3 py-1.5 rounded-lg transition-colors font-semibold shadow-sm flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">check</span> Approve
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.moderation.suspend', $lot) }}">
                            @csrf
                            <button type="submit" class="btn-magnetic bg-error-container text-error hover:bg-error/20 font-label-sm px-3 py-1.5 rounded-lg transition-colors font-semibold">
                                Reject
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.moderation.suspend', $lot) }}">
                            @csrf
                            <button type="submit" class="btn-magnetic {{ $lot->flagged ? 'bg-error text-white hover:bg-error/90 shadow-sm' : 'bg-error-container text-error hover:bg-error/20' }} font-label-sm px-3 py-1.5 rounded-lg transition-colors font-semibold">
                                {{ $lot->flagged ? 'Take Down' : 'Suspend' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($lots->hasPages())
    <div class="mt-lg flex justify-center">{{ $lots->withQueryString()->links() }}</div>
    @endif

    @else
    <div class="bg-white rounded-2xl border border-outline-variant/30 p-xl text-center">
        <span class="material-symbols-outlined text-[48px] text-outline-variant block mb-2">check_circle</span>
        <div class="font-label-lg text-secondary font-bold">All Clear</div>
        <div class="font-body-sm text-on-surface-variant">No {{ $filter }} lots found.</div>
    </div>
    @endif
@endsection
