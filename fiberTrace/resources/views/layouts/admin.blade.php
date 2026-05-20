@extends('layouts.app')

@section('body-classes', 'bg-[#f4f7f6] text-on-surface font-body-md min-h-screen flex flex-col selection:bg-error-container selection:text-error relative')

@section('background')
    <!-- Admin Background (Subtle differences from client portal) -->
    <div class="fixed inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMSIgZmlsbD0iIzFhMWExYSIgZmlsbC1vcGFjaXR5PSIwLjA0Ii8+PC9zdmc+')] mix-blend-overlay pointer-events-none -z-10"></div>
@endsection

@section('header')
    <!-- Admin Top Bar -->
    <header class="h-[72px] bg-[#1a1a1a]/95 backdrop-blur-md border-b border-white/10 flex items-center justify-between px-lg sticky top-0 z-40 lg:ml-[280px]">
        <div class="flex items-center gap-md">
            <button class="lg:hidden text-white/70 hover:text-white p-2 transition-colors">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <h1 class="font-headline-sm text-[20px] text-white font-bold hidden sm:block">
                @yield('page-title', 'Admin Control')
            </h1>
        </div>

        <div class="flex items-center gap-lg">
            <!-- Global Admin Search -->
            <div class="hidden md:flex relative group w-[320px]">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-white/40 group-focus-within:text-white transition-colors">search</span>
                <input type="text" placeholder="Search users, GSTINs, lots..." class="w-full bg-white/10 pl-12 pr-4 py-2.5 rounded-full border border-transparent focus:border-white/30 focus:bg-white/20 outline-none transition-all font-body-sm text-white placeholder:text-white/40 shadow-sm">
                <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1 opacity-60 text-white">
                    <kbd class="font-sans text-[10px] border border-white/30 rounded px-1.5 py-0.5">⌘</kbd>
                    <kbd class="font-sans text-[10px] border border-white/30 rounded px-1.5 py-0.5">K</kbd>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button class="relative p-2 text-white/70 hover:text-white bg-white/5 hover:bg-white/10 rounded-full transition-colors">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-error rounded-full"></span>
                </button>
                <div class="h-8 w-[1px] bg-white/10 hidden sm:block"></div>
                <button class="flex items-center gap-3 pl-2 pr-1 py-1 rounded-full hover:bg-white/5 transition-colors border border-transparent hover:border-white/10">
                    <div class="text-right hidden sm:block">
                        <div class="font-label-sm text-white font-bold leading-tight">Admin System</div>
                        <div class="font-body-sm text-[10px] text-error leading-tight">Level 2 Access</div>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-error text-white flex items-center justify-center font-bold font-headline-sm shadow-inner">
                        A
                    </div>
                </button>
            </div>
        </div>
    </header>
@endsection

@section('content')
    <!-- Admin Sidebar Navigation -->
    <aside class="w-[280px] bg-[#111111] border-r border-white/5 h-screen fixed left-0 top-0 z-50 flex flex-col hidden lg:flex shadow-2xl">
        <!-- Logo Area -->
        <div class="h-[72px] flex items-center px-lg border-b border-white/10 bg-black/20">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-white hover:opacity-80 transition-opacity">
                <span class="font-headline-sm text-[22px] font-bold tracking-tight flex items-center gap-2">
                    FibreTrace <span class="bg-error/20 text-error text-[10px] px-2 py-0.5 rounded border border-error/30 uppercase tracking-widest">Admin</span>
                </span>
            </a>
        </div>

        <!-- Main Nav -->
        <nav class="flex-1 overflow-y-auto py-lg px-md flex flex-col gap-2 custom-scrollbar">
            
            <a href="{{ url('/admin') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/60 hover:bg-white/5 hover:text-white font-label-lg font-medium transition-colors group {{ request()->is('admin') ? 'bg-white/10 text-white' : '' }}">
                <span class="material-symbols-outlined text-[20px]">space_dashboard</span>
                Control Panel
            </a>
            
            <div class="font-label-sm text-white/40 font-bold uppercase tracking-wider mb-2 mt-6 px-4">Verification</div>
            
            <a href="{{ url('/admin/verifications') }}" class="flex items-center justify-between px-4 py-3 rounded-xl text-white/60 hover:bg-white/5 hover:text-white font-label-lg font-medium transition-colors group {{ request()->is('admin/verifications') ? 'bg-white/10 text-white' : '' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[20px]">fact_check</span>
                    GSTIN Queue
                </div>
                <span class="bg-error/20 text-error text-[11px] px-2 py-0.5 rounded-full font-bold">14</span>
            </a>

            <div class="font-label-sm text-white/40 font-bold uppercase tracking-wider mb-2 mt-6 px-4">Moderation</div>

            <a href="{{ url('/admin/moderation') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/60 hover:bg-white/5 hover:text-white font-label-lg font-medium transition-colors group {{ request()->is('admin/moderation') ? 'bg-white/10 text-white' : '' }}">
                <span class="material-symbols-outlined text-[20px]">policy</span>
                Active Listings
            </a>
            
            <div class="font-label-sm text-white/40 font-bold uppercase tracking-wider mb-2 mt-6 px-4">Security</div>
            
            <a href="{{ url('/admin/audit') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/60 hover:bg-white/5 hover:text-white font-label-lg font-medium transition-colors group {{ request()->is('admin/audit') ? 'bg-white/10 text-white' : '' }}">
                <span class="material-symbols-outlined text-[20px]">security</span>
                PII & Audit Ledger
            </a>
            
        </nav>

        <!-- Bottom Actions -->
        <div class="p-md border-t border-white/10 bg-black/20">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-4 py-3 w-full rounded-xl text-white/60 hover:bg-error/20 hover:text-error font-label-lg font-medium transition-colors group">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                    Secure Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="lg:ml-[280px] p-container-padding flex-1 relative z-10 overflow-x-hidden">
        @yield('admin-content')
    </main>
@endsection
