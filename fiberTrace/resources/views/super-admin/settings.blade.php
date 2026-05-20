@extends('layouts.superadmin')

@section('title', 'Platform Settings - FibreTrace')

@section('page-title', 'Core Configuration')

@section('superadmin-content')
    <form action="{{ route('super-admin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-md mb-lg">
            <div>
                <h2 class="font-headline-md text-white font-bold">System Constants</h2>
                <p class="font-body-sm text-white/50">Manage algorithmic variables, commission rates, and global platform behaviors.</p>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="btn-magnetic bg-tertiary text-on-primary-fixed font-label-sm px-4 py-2.5 rounded-xl hover:bg-tertiary/90 shadow-sm flex items-center gap-2 transition-colors font-bold">
                    <span class="material-symbols-outlined text-[18px]">save</span> Save Configuration
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-secondary/10 border border-secondary/30 text-secondary font-label-sm px-4 py-3 rounded-xl mb-lg flex items-center gap-3">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        <!-- Settings Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-md">
            
            <!-- Monetization Settings -->
            <div class="bg-white/5 rounded-2xl border border-white/10 shadow-[0_4px_24px_rgba(0,0,0,0.2)] p-lg backdrop-blur-sm relative overflow-hidden">
                <div class="absolute -right-12 -top-12 w-32 h-32 bg-secondary/10 rounded-full blur-2xl"></div>
                
                <h3 class="font-label-lg text-white font-bold mb-4 flex items-center gap-2 relative z-10">
                    <span class="material-symbols-outlined text-secondary">payments</span> Monetization & Escrow
                </h3>

                <div class="space-y-4 relative z-10">
                    <!-- Field -->
                    <div>
                        <label class="block font-label-sm text-white/70 mb-1">Platform Commission Rate (%)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-white/40 font-mono">%</span>
                            <input type="number" name="settings[platform_commission]" step="0.01" value="{{ $settings['platform_commission']->value ?? '1.50' }}" class="w-full bg-black/40 pl-8 pr-3 py-2 rounded-lg border border-white/10 focus:border-tertiary focus:ring-1 focus:ring-tertiary outline-none transition-all font-mono text-white text-sm">
                        </div>
                        <p class="text-[10px] text-white/40 mt-1">This flat percentage fee is applied to the final subtotal of every successful transaction.</p>
                    </div>
                    
                    <!-- Field -->
                    <div>
                        <label class="block font-label-sm text-white/70 mb-1">Escrow Release Delay (Hours)</label>
                        <input type="number" name="settings[escrow_release_delay]" value="{{ $settings['escrow_release_delay']->value ?? '48' }}" class="w-full bg-black/40 px-3 py-2 rounded-lg border border-white/10 focus:border-tertiary focus:ring-1 focus:ring-tertiary outline-none transition-all font-mono text-white text-sm">
                        <p class="text-[10px] text-white/40 mt-1">Time allowed for buyer to raise a dispute after marked 'Delivered'.</p>
                    </div>
                </div>
            </div>

            <!-- Algorithm Settings -->
            <div class="bg-white/5 rounded-2xl border border-white/10 shadow-[0_4px_24px_rgba(0,0,0,0.2)] p-lg backdrop-blur-sm relative overflow-hidden">
                
                <h3 class="font-label-lg text-white font-bold mb-4 flex items-center gap-2 relative z-10">
                    <span class="material-symbols-outlined text-primary">functions</span> Price Auto-Suggest Algorithm
                </h3>

                <div class="space-y-4 relative z-10">
                    <!-- Field -->
                    <div>
                        <label class="block font-label-sm text-white/70 mb-1">Purity Premium Coefficient</label>
                        <input type="number" name="settings[purity_premium]" step="0.01" value="{{ $settings['purity_premium']->value ?? '1.15' }}" class="w-full bg-black/40 px-3 py-2 rounded-lg border border-white/10 focus:border-tertiary focus:ring-1 focus:ring-tertiary outline-none transition-all font-mono text-white text-sm">
                        <p class="text-[10px] text-white/40 mt-1">Multiplier applied if the lot is >95% primary fiber.</p>
                    </div>
                    
                    <!-- Field -->
                    <div>
                        <label class="block font-label-sm text-white/70 mb-1">Color Sort Premium Coefficient</label>
                        <input type="number" name="settings[color_sort_premium]" step="0.01" value="{{ $settings['color_sort_premium']->value ?? '1.08' }}" class="w-full bg-black/40 px-3 py-2 rounded-lg border border-white/10 focus:border-tertiary focus:ring-1 focus:ring-tertiary outline-none transition-all font-mono text-white text-sm">
                        <p class="text-[10px] text-white/40 mt-1">Multiplier applied if the 'Color Sorted' toggle is true.</p>
                    </div>
                </div>
            </div>

            <!-- Global Limits -->
            <div class="bg-white/5 rounded-2xl border border-white/10 shadow-[0_4px_24px_rgba(0,0,0,0.2)] p-lg backdrop-blur-sm lg:col-span-2">
                <h3 class="font-label-lg text-white font-bold mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-error">rule</span> System Limits
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-md">
                    <!-- Field -->
                    <div>
                        <label class="block font-label-sm text-white/70 mb-1">Minimum Lot Weight (kg)</label>
                        <input type="number" name="settings[min_lot_weight]" value="{{ $settings['min_lot_weight']->value ?? '100' }}" class="w-full bg-black/40 px-3 py-2 rounded-lg border border-white/10 focus:border-tertiary focus:ring-1 focus:ring-tertiary outline-none transition-all font-mono text-white text-sm">
                    </div>
                    <!-- Field -->
                    <div>
                        <label class="block font-label-sm text-white/70 mb-1">Maximum Lot Weight (kg)</label>
                        <input type="number" name="settings[max_lot_weight]" value="{{ $settings['max_lot_weight']->value ?? '25000' }}" class="w-full bg-black/40 px-3 py-2 rounded-lg border border-white/10 focus:border-tertiary focus:ring-1 focus:ring-tertiary outline-none transition-all font-mono text-white text-sm">
                    </div>
                    <!-- Field -->
                    <div>
                        <label class="block font-label-sm text-white/70 mb-1">Auction Duration (Hours)</label>
                        <input type="number" name="settings[auction_duration_hours]" value="{{ $settings['auction_duration_hours']->value ?? '24' }}" class="w-full bg-black/40 px-3 py-2 rounded-lg border border-white/10 focus:border-tertiary focus:ring-1 focus:ring-tertiary outline-none transition-all font-mono text-white text-sm">
                    </div>
                </div>
            </div>

        </div>
    </form>
@endsection
