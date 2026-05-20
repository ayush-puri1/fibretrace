@extends('layouts.superadmin')

@section('title', 'Super Admin Dashboard - FibreTrace')

@section('page-title', 'System Health & Revenue')

@section('superadmin-content')
    <div class="mb-lg">
        <h2 class="font-headline-md text-white font-bold">Platform Overview</h2>
        <p class="font-body-sm text-white/50">High-level metrics, cumulative revenue, and system status.</p>
    </div>

    <!-- Monetization KPI Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-md mb-xl">
        <!-- KPI 1 -->
        <div class="bg-white/5 rounded-2xl p-lg border border-white/10 relative overflow-hidden backdrop-blur-sm">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-tertiary/10 rounded-full blur-2xl"></div>
            <div class="flex justify-between items-start mb-4 relative z-10">
                <div class="w-12 h-12 rounded-xl bg-tertiary/10 text-tertiary border border-tertiary/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[24px]">account_balance</span>
                </div>
                <span class="flex items-center gap-1 text-secondary font-bold text-[12px] bg-secondary/10 px-2 py-1 rounded-md border border-secondary/20">
                    <span class="material-symbols-filled text-[14px]">trending_up</span> 18.2%
                </span>
            </div>
            <div class="font-label-sm text-white/50 font-bold uppercase tracking-wider mb-1 relative z-10">Total Revenue (Commissions)</div>
            @php $commission = \App\Models\SystemSetting::get('platform_commission', 1.50); @endphp
            <div class="font-headline-lg text-white font-bold relative z-10">₹{{ number_format($stats['total_volume'] * ($commission / 100), 2) }}</div>
            <div class="font-body-sm text-white/40 mt-2 text-[12px] relative z-10">Lifetime earnings at ₹{{ number_format($commission, 2) }}/kg</div>
        </div>

        <!-- KPI 2 -->
        <div class="bg-white/5 rounded-2xl p-lg border border-white/10 backdrop-blur-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[24px]">storefront</span>
                </div>
            </div>
            <div class="font-label-sm text-white/50 font-bold uppercase tracking-wider mb-1">Gross Merchandise Value</div>
            <div class="font-headline-lg text-white font-bold">₹{{ number_format($stats['total_volume'], 2) }}</div>
            <div class="font-body-sm text-white/40 mt-2 text-[12px]">Total value of traded waste</div>
        </div>

        <!-- KPI 3 -->
        <div class="bg-white/5 rounded-2xl p-lg border border-white/10 backdrop-blur-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-white/10 text-white flex items-center justify-center">
                    <span class="material-symbols-outlined text-[24px]">groups</span>
                </div>
            </div>
            <div class="font-label-sm text-white/50 font-bold uppercase tracking-wider mb-1">Total Verified Users</div>
            <div class="font-headline-lg text-white font-bold">{{ number_format($stats['total_users']) }}</div>
            <div class="font-body-sm text-white/40 mt-2 text-[12px]">Pending Approvals: {{ $stats['pending_users'] }}</div>
        </div>
    </div>

    <!-- Additional System Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-md mb-xl">
        <div class="bg-white/5 rounded-2xl p-4 border border-white/10 backdrop-blur-sm">
            <div class="font-label-sm text-white/50 font-bold uppercase mb-1">Total Admins</div>
            <div class="font-headline-md text-white font-bold">{{ $stats['total_admins'] }}</div>
        </div>
        <div class="bg-white/5 rounded-2xl p-4 border border-white/10 backdrop-blur-sm">
            <div class="font-label-sm text-white/50 font-bold uppercase mb-1">Total Lots Created</div>
            <div class="font-headline-md text-white font-bold">{{ number_format($stats['total_lots']) }}</div>
        </div>
        <div class="bg-white/5 rounded-2xl p-4 border border-white/10 backdrop-blur-sm">
            <div class="font-label-sm text-white/50 font-bold uppercase mb-1">Active Auctions</div>
            <div class="font-headline-md text-secondary font-bold">{{ number_format($stats['active_auctions']) }}</div>
        </div>
        <div class="bg-error/10 rounded-2xl p-4 border border-error/20 backdrop-blur-sm">
            <div class="font-label-sm text-error font-bold uppercase mb-1">Flagged Lots</div>
            <div class="font-headline-md text-error font-bold">{{ $stats['flagged_lots'] }}</div>
        </div>
    </div>

    <!-- Charts & System Status -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-md">
        <!-- Revenue Chart Mock -->
        <div class="bg-white/5 rounded-2xl border border-white/10 lg:col-span-2 overflow-hidden backdrop-blur-sm flex flex-col">
            <div class="p-lg border-b border-white/10 flex justify-between items-center">
                <h3 class="font-label-lg text-white font-bold">Monthly Revenue (30 Days)</h3>
                <button class="text-tertiary font-label-sm hover:underline">Download Report</button>
            </div>
            <div class="flex-1 p-lg flex flex-col justify-end gap-2 relative">
                <!-- Grid Lines -->
                <div class="absolute inset-x-lg top-lg bottom-lg flex flex-col justify-between">
                    <div class="border-t border-white/5 w-full"></div>
                    <div class="border-t border-white/5 w-full"></div>
                    <div class="border-t border-white/5 w-full"></div>
                    <div class="border-t border-white/5 w-full"></div>
                </div>
                
                <!-- Bars Mock -->
                <div class="relative z-10 flex items-end justify-between h-[200px] gap-2">
                    <div class="w-full bg-tertiary/20 hover:bg-tertiary/40 transition-colors rounded-t-sm h-[40%] group relative"></div>
                    <div class="w-full bg-tertiary/20 hover:bg-tertiary/40 transition-colors rounded-t-sm h-[35%] group relative"></div>
                    <div class="w-full bg-tertiary/20 hover:bg-tertiary/40 transition-colors rounded-t-sm h-[50%] group relative"></div>
                    <div class="w-full bg-tertiary/20 hover:bg-tertiary/40 transition-colors rounded-t-sm h-[65%] group relative"></div>
                    <div class="w-full bg-tertiary/20 hover:bg-tertiary/40 transition-colors rounded-t-sm h-[45%] group relative"></div>
                    <div class="w-full bg-tertiary/20 hover:bg-tertiary/40 transition-colors rounded-t-sm h-[80%] group relative"></div>
                    <div class="w-full bg-tertiary hover:bg-tertiary/80 transition-colors rounded-t-sm h-[95%] group relative"></div>
                </div>
                <div class="flex justify-between text-[10px] text-white/40 font-mono mt-2">
                    <span>Week 1</span>
                    <span>Week 2</span>
                    <span>Week 3</span>
                    <span>Week 4</span>
                </div>
            </div>
        </div>

        <!-- Recent Activity Feed -->
        <div class="bg-white/5 rounded-2xl border border-white/10 overflow-hidden backdrop-blur-sm flex flex-col h-full">
            <div class="p-lg border-b border-white/10 flex justify-between items-center">
                <h3 class="font-label-lg text-white font-bold">Recent System Activity</h3>
            </div>
            <div class="flex-1 overflow-y-auto max-h-[300px] custom-scrollbar-dark">
                @forelse($recentActivity as $log)
                <div class="p-4 border-b border-white/5 flex gap-3 hover:bg-white/5 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-white/10 text-white flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[16px]">info</span>
                    </div>
                    <div>
                        <div class="font-body-sm text-white/80 line-clamp-2">{{ $log->description }}</div>
                        <div class="text-[10px] text-white/40 mt-1">{{ $log->created_at->diffForHumans() }} by {{ $log->user->name ?? 'System' }}</div>
                    </div>
                </div>
                @empty
                <div class="p-4 text-white/50 text-center font-body-sm">No recent activity.</div>
                @endforelse
            </div>
            <div class="p-3 bg-black/20 text-center border-t border-white/10">
                <span class="text-[12px] text-white/60">Real-time log stream</span>
            </div>
        </div>
    </div>
@endsection
