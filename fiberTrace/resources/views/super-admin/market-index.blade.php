@extends('layouts.superadmin')

@section('title', 'Live Market Index Manager - FibreTrace')

@section('page-title', 'Market Index Control')

@section('superadmin-content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-md mb-lg">
        <div>
            <h2 class="font-headline-md text-white font-bold">Baseline Pricing Matrix</h2>
            <p class="font-body-sm text-white/50">Update the weekly baseline index values. This data feeds directly into the client portal's historical charts and auto-suggest algorithms.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-secondary/10 border border-secondary/30 text-secondary font-label-sm px-4 py-3 rounded-xl mb-lg flex items-center gap-3">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-error/10 border border-error/30 text-error font-label-sm px-4 py-3 rounded-xl mb-lg">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Edit Matrix -->
    <div class="bg-white/5 rounded-2xl border border-white/10 shadow-[0_4px_24px_rgba(0,0,0,0.2)] overflow-hidden backdrop-blur-sm">
        
        <div class="p-4 border-b border-white/10 flex justify-between items-center bg-black/20">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-tertiary">calendar_today</span>
                <span class="font-label-md text-white font-bold">Week of {{ now()->startOfWeek()->format('M d, Y') }}</span>
            </div>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-black/40 border-b border-white/10">
                        <th class="p-4 font-label-sm text-white/40 font-bold uppercase tracking-wider w-1/4">Fiber Category</th>
                        <th class="p-4 font-label-sm text-white/40 font-bold uppercase tracking-wider">Previous Week (₹/kg)</th>
                        <th class="p-4 font-label-sm text-white/40 font-bold uppercase tracking-wider border-x border-white/5 bg-white/5">Current Week (₹/kg)</th>
                        <th class="p-4 font-label-sm text-white/40 font-bold uppercase tracking-wider text-right">Trend / Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    
                    @foreach($prices as $price)
                    <tr class="hover:bg-white/5 transition-colors group">
                        <form action="{{ route('super-admin.market-index.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="fiber_category" value="{{ $price->fiber_category }}">
                            <input type="hidden" name="sub_label" value="{{ $price->sub_label }}">
                            <input type="hidden" name="week_start" value="{{ now()->startOfWeek()->format('Y-m-d') }}">
                            
                            <td class="p-4">
                                <div class="font-label-md text-white font-bold">{{ $price->fiber_category }}</div>
                                <div class="text-[11px] text-white/40">{{ $price->sub_label }}</div>
                            </td>
                            <td class="p-4 text-white/50 font-mono">₹{{ number_format($price->previous_price ?? $price->price_per_kg, 2) }}</td>
                            <td class="p-4 border-x border-white/5 bg-white/5">
                                <div class="relative w-32">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-white/40 font-mono">₹</span>
                                    <input type="number" name="price_per_kg" step="0.01" value="{{ $price->price_per_kg }}" class="w-full bg-black/40 pl-8 pr-3 py-2 rounded-lg border border-white/10 focus:border-tertiary focus:ring-1 focus:ring-tertiary outline-none transition-all font-mono text-white text-sm">
                                </div>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    @php
                                        $diff = $price->price_per_kg - ($price->previous_price ?? $price->price_per_kg);
                                        $pct = ($price->previous_price ?? $price->price_per_kg) > 0 ? ($diff / ($price->previous_price ?? $price->price_per_kg)) * 100 : 0;
                                    @endphp
                                    @if($diff > 0)
                                        <span class="text-secondary font-bold text-[12px] flex items-center gap-1">
                                            <span class="material-symbols-filled text-[14px]">arrow_upward</span> +{{ number_format($pct, 1) }}%
                                        </span>
                                    @elseif($diff < 0)
                                        <span class="text-error font-bold text-[12px] flex items-center gap-1">
                                            <span class="material-symbols-filled text-[14px]">arrow_downward</span> {{ number_format($pct, 1) }}%
                                        </span>
                                    @else
                                        <span class="text-white/40 font-bold text-[12px] flex items-center gap-1">
                                            <span class="material-symbols-filled text-[14px]">horizontal_rule</span> 0.0%
                                        </span>
                                    @endif
                                    
                                    <button type="submit" class="btn-magnetic bg-tertiary/20 text-tertiary hover:bg-tertiary hover:text-on-primary-fixed font-label-sm px-3 py-1.5 rounded-lg shadow-sm transition-colors text-[11px] font-bold">
                                        Update
                                    </button>
                                </div>
                            </td>
                        </form>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
@endsection
