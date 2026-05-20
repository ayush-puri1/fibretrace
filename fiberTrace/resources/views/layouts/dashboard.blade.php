@extends('layouts.app')

@section('title')
    @yield('title', 'Dashboard - FibreTrace')
@endsection

@section('body-classes', 'bg-[#f0f4f8] text-on-surface font-body-md min-h-screen flex flex-col selection:bg-secondary-container selection:text-primary relative')

@section('header')
    <!-- Dashboard Top Bar -->
    <header
        class="h-[72px] bg-white/80 backdrop-blur-md border-b border-outline-variant/30 flex items-center justify-between px-lg sticky top-0 z-40 lg:ml-[280px]">
        <div class="flex items-center gap-md">
            <button class="lg:hidden text-on-surface-variant hover:text-primary p-2">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <h1 class="font-headline-sm text-headline-sm text-primary font-bold hidden sm:block">
                @yield('page-title', 'Overview')</h1>
        </div>

        <div class="flex items-center gap-lg">
            <!-- Global Search -->
            <div class="hidden md:flex relative group w-[320px]">
                <span
                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant group-focus-within:text-primary transition-colors">search</span>
                <input type="text" placeholder="Search lots, IDs, or buyers..."
                    class="w-full bg-surface-container-low pl-12 pr-4 py-2.5 rounded-full border border-transparent focus:border-primary/30 focus:bg-white outline-none transition-all font-body-sm text-on-surface placeholder:text-outline-variant shadow-sm">
                <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1 opacity-60">
                    <kbd class="font-sans text-[10px] border border-outline-variant rounded px-1.5 py-0.5">⌘</kbd>
                    <kbd class="font-sans text-[10px] border border-outline-variant rounded px-1.5 py-0.5">K</kbd>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button
                    class="relative p-2 text-on-surface-variant hover:text-primary bg-surface-container-low hover:bg-surface-container-high rounded-full transition-colors">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-error rounded-full border-2 border-white"></span>
                </button>
                <div class="h-8 w-[1px] bg-outline-variant/30 hidden sm:block"></div>
                <button
                    class="flex items-center gap-3 pl-2 pr-1 py-1 rounded-full hover:bg-surface-container-low transition-colors border border-transparent hover:border-outline-variant/30">
                    <div class="text-right hidden sm:block">
                            <div class="font-label-sm text-primary font-bold leading-tight">{{ Auth::user()->company_name }}</div>
                            <div class="font-body-sm text-[10px] text-on-surface-variant leading-tight capitalize">Verified {{ Auth::user()->role }}</div>
                        </div>
                        <div
                            class="w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center font-bold font-headline-sm shadow-inner">
                            {{ strtoupper(substr(Auth::user()->company_name, 0, 1)) }}
                        </div>
                </button>
            </div>
        </div>
    </header>
@endsection

@section('content')
    <!-- Dashboard Sidebar Navigation -->
    <aside
        class="w-[280px] bg-surface-container-lowest border-r border-outline-variant/30 h-screen fixed left-0 top-0 z-50 flex flex-col hidden lg:flex shadow-[4px_0_24px_rgba(0,53,39,0.02)]">
        <!-- Logo Area -->
        <div class="h-[72px] flex items-center px-lg border-b border-outline-variant/30">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-primary hover:opacity-80 transition-opacity">
                <span class="font-headline-sm text-[22px] font-bold tracking-tight">FibreTrace</span>
            </a>
        </div>

        <!-- Main Nav -->
        <nav class="flex-1 overflow-y-auto py-lg px-md flex flex-col gap-2 custom-scrollbar">
            <div class="font-label-sm text-outline font-bold uppercase tracking-wider mb-2 px-4">Main Menu</div>

            <a href="{{ url('/dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('dashboard') ? 'bg-secondary-container/30 text-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container-low hover:text-primary font-medium' }} font-label-lg relative group transition-colors">
                @if(request()->is('dashboard'))
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-secondary rounded-r-full"></div>
                @endif
                <span
                    class="material-symbols-{{ request()->is('dashboard') ? 'filled' : 'outlined' }} text-[20px] {{ request()->is('dashboard') ? 'text-secondary' : 'group-hover:text-primary' }} transition-colors">dashboard</span>
                Overview
            </a>

            <a href="{{ url('/auctions') }}"
                class="flex items-center justify-between px-4 py-3 rounded-xl {{ request()->is('auctions*') ? 'bg-secondary-container/30 text-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container-low hover:text-primary font-medium' }} font-label-lg transition-colors group relative">
                @if(request()->is('auctions*'))
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-secondary rounded-r-full"></div>
                @endif
                <div class="flex items-center gap-3">
                    <span
                        class="material-symbols-{{ request()->is('auctions*') ? 'filled' : 'outlined' }} text-[20px] {{ request()->is('auctions*') ? 'text-secondary' : 'group-hover:text-primary' }} transition-colors">gavel</span>
                    Live Auctions
                </div>
                <span class="bg-primary/10 text-primary text-[11px] px-2 py-0.5 rounded-full font-bold">12 Active</span>
            </a>

            <a href="{{ url('/seller/ledger') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('seller/ledger') ? 'bg-secondary-container/30 text-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container-low hover:text-primary font-medium' }} font-label-lg relative group transition-colors">
                @if(request()->is('seller/ledger'))
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-secondary rounded-r-full"></div>
                @endif
                <span
                    class="material-symbols-{{ request()->is('seller/ledger') ? 'filled' : 'outlined' }} text-[20px] {{ request()->is('seller/ledger') ? 'text-secondary' : 'group-hover:text-primary' }} transition-colors">inventory_2</span>
                My Inventory
            </a>

            <a href="{{ url('/dispatch') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('dispatch') ? 'bg-secondary-container/30 text-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container-low hover:text-primary font-medium' }} font-label-lg relative group transition-colors">
                @if(request()->is('dispatch'))
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-secondary rounded-r-full"></div>
                @endif
                <span
                    class="material-symbols-{{ request()->is('dispatch') ? 'filled' : 'outlined' }} text-[20px] {{ request()->is('dispatch') ? 'text-secondary' : 'group-hover:text-primary' }} transition-colors">local_shipping</span>
                Dispatch & Logistics
            </a>

            <div class="font-label-sm text-outline font-bold uppercase tracking-wider mb-2 mt-6 px-4">Finance</div>

            <a href="{{ url('/wallet') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('wallet') ? 'bg-secondary-container/30 text-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container-low hover:text-primary font-medium' }} font-label-lg relative group transition-colors">
                @if(request()->is('wallet'))
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-secondary rounded-r-full"></div>
                @endif
                <span
                    class="material-symbols-{{ request()->is('wallet') ? 'filled' : 'outlined' }} text-[20px] {{ request()->is('wallet') ? 'text-secondary' : 'group-hover:text-primary' }} transition-colors">account_balance_wallet</span>
                Escrow Wallet
            </a>

            <a href="{{ url('/settlement') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('settlement') ? 'bg-secondary-container/30 text-primary font-semibold' : 'text-on-surface-variant hover:bg-surface-container-low hover:text-primary font-medium' }} font-label-lg relative group transition-colors">
                @if(request()->is('settlement'))
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-secondary rounded-r-full"></div>
                @endif
                <span
                    class="material-symbols-{{ request()->is('settlement') ? 'filled' : 'outlined' }} text-[20px] {{ request()->is('settlement') ? 'text-secondary' : 'group-hover:text-primary' }} transition-colors">receipt_long</span>
                Settlements
            </a>
        </nav>

        <!-- Bottom Actions -->
        <div class="p-md border-t border-outline-variant/30">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center gap-3 px-4 py-3 w-full rounded-xl text-on-surface-variant hover:bg-error-container/50 hover:text-error font-label-lg font-medium transition-colors group">
                    <span class="material-symbols-outlined text-[20px] group-hover:text-error transition-colors">logout</span>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="lg:ml-[280px] flex-1 flex flex-col relative z-10 overflow-x-hidden">
        <!-- Live Ticker -->
        <div
            class="w-full bg-inverse-surface/95 backdrop-blur-md text-inverse-on-surface py-2 flex items-center justify-start overflow-hidden relative shadow-sm border-b border-outline-variant/20 z-20 sticky top-[12px]">
            <div
                class="absolute left-0 top-0 bottom-0 z-10 px-4 md:px-lg bg-gradient-to-r from-inverse-surface via-inverse-surface/90 to-transparent flex items-center pr-12">
                <span
                    class="font-bold text-secondary-fixed uppercase tracking-wider text-[10px] md:text-[12px] flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-error animate-pulse shadow-[0_0_8px_rgba(186,26,26,0.8)]"></span>
                    Live Market
                </span>
            </div>
            <div
                class="absolute right-0 top-0 bottom-0 z-10 w-32 bg-gradient-to-l from-inverse-surface to-transparent pointer-events-none">
            </div>
            @php
                $marketPrices = \App\Models\MarketPrice::latest()->take(8)->get();
            @endphp
            <div class="flex animate-marquee pl-[200px] hover:[animation-play-state:paused]">
                <!-- Dynamic Data Block -->
                <div class="flex gap-8 whitespace-nowrap items-center font-label-md shrink-0 pr-8">
                    @foreach($marketPrices as $price)
                        @php
                            $isUp = $price->previous_price ? ($price->price_per_kg > $price->previous_price) : true;
                            $isDown = $price->previous_price ? ($price->price_per_kg < $price->previous_price) : false;
                            $colorClass = $isUp ? 'text-secondary-fixed' : ($isDown ? 'text-error-container' : 'text-surface-variant');
                            $icon = $isUp ? 'arrow_upward' : ($isDown ? 'arrow_downward' : 'horizontal_rule');
                            $animateClass = $isUp ? 'animate-bounce' : '';
                        @endphp
                        <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 backdrop-blur-sm border border-white/10 transition-all hover:bg-white/10 cursor-default">
                            <span class="text-tertiary-fixed-dim text-[12px]">{{ $price->fiber_category }}</span>
                            <span class="font-body-sm {{ $colorClass }} font-medium">₹{{ number_format($price->price_per_kg, 2) }}/kg</span>
                            <span class="material-symbols-outlined {{ $colorClass }} text-[14px] {{ $animateClass }}">{{ $icon }}</span>
                        </div>
                    @endforeach
                </div>
                <!-- Duplicate for seamless marquee -->
                <div class="flex gap-8 whitespace-nowrap items-center font-label-md shrink-0 pr-8">
                    @foreach($marketPrices as $price)
                        @php
                            $isUp = $price->previous_price ? ($price->price_per_kg > $price->previous_price) : true;
                            $isDown = $price->previous_price ? ($price->price_per_kg < $price->previous_price) : false;
                            $colorClass = $isUp ? 'text-secondary-fixed' : ($isDown ? 'text-error-container' : 'text-surface-variant');
                            $icon = $isUp ? 'arrow_upward' : ($isDown ? 'arrow_downward' : 'horizontal_rule');
                            $animateClass = $isUp ? 'animate-bounce' : '';
                        @endphp
                        <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 backdrop-blur-sm border border-white/10 transition-all hover:bg-white/10 cursor-default">
                            <span class="text-tertiary-fixed-dim text-[12px]">{{ $price->fiber_category }}</span>
                            <span class="font-body-sm {{ $colorClass }} font-medium">₹{{ number_format($price->price_per_kg, 2) }}/kg</span>
                            <span class="material-symbols-outlined {{ $colorClass }} text-[14px] {{ $animateClass }}">{{ $icon }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="p-container-padding flex-1">
            @yield('dashboard-content')
        </div>
    </div>
@endsection