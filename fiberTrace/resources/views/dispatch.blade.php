@extends('layouts.dashboard')

@section('title', 'Dispatch Tracking #FT-' . $transaction->lot_id . ' - FibreTrace')

@section('page-title', 'Dispatch Tracking')

@section('dashboard-content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-md mb-lg">
        <div>
            <h2 class="font-headline-md text-primary font-bold">Tracking #SET-{{ $transaction->id }}</h2>
            <p class="font-body-sm text-on-surface-variant">Track outbound shipments and monitor delivery statuses.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('settlement.show', $transaction) }}" class="btn-magnetic bg-surface-container-low text-primary border border-outline-variant/50 font-label-sm px-4 py-2.5 rounded-xl hover:border-primary shadow-sm flex items-center gap-2 transition-colors">
                <span class="material-symbols-outlined text-[18px]">receipt_long</span> View Settlement
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-secondary/10 border border-secondary/30 text-secondary font-label-sm px-4 py-3 rounded-xl mb-lg flex items-center gap-3">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <!-- Active Shipment Tracker -->
    <div class="grid grid-cols-1 gap-lg mb-xl max-w-[800px] mx-auto">
        <div class="glass-card rounded-[1.5rem] p-lg border-white/80 shadow-[0_8px_32px_rgba(0,53,39,0.03)] bg-white/60 hover:shadow-lg transition-shadow relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-32 h-32 {{ $transaction->logistics_status === 'delivered' ? 'bg-secondary/10' : 'bg-primary/10' }} rounded-full blur-2xl transition-colors duration-500"></div>
            
            <div class="flex justify-between items-start mb-4 relative z-10">
                <div class="w-12 h-12 rounded-full {{ $transaction->logistics_status === 'delivered' ? 'bg-secondary' : 'bg-primary' }} text-white flex items-center justify-center shadow-md">
                    <span class="material-symbols-outlined text-[24px]">local_shipping</span>
                </div>
                <span class="bg-primary/10 text-primary border border-primary/20 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">{{ str_replace('_', ' ', $transaction->logistics_status) }}</span>
            </div>
            
            <div class="relative z-10 mb-4">
                <div class="font-body-sm text-outline uppercase tracking-wider mb-1">Waybill #PB-{{ 9000 + $transaction->id }}-XY</div>
                <div class="font-headline-sm text-primary font-bold">Lot #FT-{{ $transaction->lot_id }}</div>
                <div class="font-body-sm text-on-surface-variant mt-1">{{ $transaction->lot->primary_fiber }} • {{ number_format($transaction->actual_weight_kg) }} kg</div>
            </div>
            
            <div class="relative z-10 border-t border-outline-variant/30 pt-4 mt-4">
                @if($transaction->logistics_status === 'awaiting_dispatch')
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full bg-outline-variant"></span>
                        <span class="font-label-sm text-on-surface font-semibold">Status:</span>
                        <span class="font-body-sm text-on-surface-variant">Awaiting Carrier Assignment</span>
                    </div>
                @elseif($transaction->logistics_status === 'delivered')
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full bg-secondary"></span>
                        <span class="font-label-sm text-secondary font-semibold">Status:</span>
                        <span class="font-body-sm text-secondary">Successfully Delivered</span>
                    </div>
                @else
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                        <span class="font-label-sm text-on-surface font-semibold">Current Status:</span>
                        <span class="font-body-sm text-on-surface-variant">{{ ucfirst(str_replace('_', ' ', $transaction->logistics_status)) }}</span>
                    </div>
                    <div class="w-full bg-surface-container-low rounded-full h-1.5 mb-1 overflow-hidden">
                        <div class="bg-secondary h-1.5 rounded-full" style="width: {{ $transaction->logistics_status === 'in_transit' ? '65%' : '30%' }}"></div>
                    </div>
                    <div class="flex justify-between text-[10px] text-outline font-medium uppercase mt-2">
                        <span>{{ $transaction->seller->city ?? 'Origin' }}</span>
                        <span>{{ $transaction->logistics_status === 'in_transit' ? 'In Transit' : 'Dispatched' }}</span>
                        <span>{{ $transaction->buyer->city ?? 'Destination' }}</span>
                    </div>
                @endif
                
                @if(auth()->id() === $transaction->seller_id && $transaction->logistics_status !== 'delivered')
                    <form action="{{ route('dispatch.update', $transaction) }}" method="POST" class="mt-6 flex gap-2">
                        @csrf
                        <select name="status" class="flex-1 bg-white border border-outline-variant/50 rounded-lg px-3 py-2 text-sm outline-none focus:border-primary">
                            @if($transaction->logistics_status === 'awaiting_dispatch')
                                <option value="dispatched">Mark as Dispatched</option>
                            @endif
                            @if(in_array($transaction->logistics_status, ['awaiting_dispatch', 'dispatched']))
                                <option value="in_transit">Mark as In Transit</option>
                            @endif
                            <option value="delivered">Mark as Delivered</option>
                        </select>
                        <button type="submit" class="btn-magnetic bg-surface-container-lowest text-primary border border-outline-variant/50 font-label-sm px-4 py-2 rounded-lg hover:border-primary transition-colors">
                            Update Status
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
