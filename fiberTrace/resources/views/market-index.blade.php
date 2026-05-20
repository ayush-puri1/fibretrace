@extends('layouts.dashboard')

@section('title', 'Market Index - FibreTrace')

@section('page-title', 'Detailed Market Index')

@section('dashboard-content')
    <!-- Header / Controls -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-md mb-lg">
        <div>
            <h2 class="font-headline-md text-primary font-bold">Historical Pricing Trends</h2>
            <p class="font-body-sm text-on-surface-variant">Ludhiana-Panipat Recycling Corridor</p>
        </div>
        
        <div class="flex gap-sm">
            <button class="btn-magnetic bg-surface-container-lowest text-primary border border-outline-variant/50 font-label-sm px-4 py-2.5 rounded-xl hover:border-primary shadow-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">calendar_month</span> Last 90 Days
            </button>
            <button class="btn-magnetic bg-primary text-white font-label-sm px-4 py-2.5 rounded-xl hover:bg-secondary shadow-md flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">download</span> Export CSV
            </button>
        </div>
    </div>

    <!-- Category Tabs -->
    <div class="flex overflow-x-auto custom-scrollbar gap-2 mb-lg pb-2">
        <button class="px-5 py-2.5 rounded-full bg-primary text-white font-label-md font-bold shadow-sm whitespace-nowrap">
            100% Cotton
        </button>
        <button class="px-5 py-2.5 rounded-full bg-surface-container-lowest text-on-surface-variant border border-outline-variant/30 hover:bg-surface-container-low hover:text-primary font-label-md font-medium transition-colors whitespace-nowrap">
            Poly-Blend (65/35)
        </button>
        <button class="px-5 py-2.5 rounded-full bg-surface-container-lowest text-on-surface-variant border border-outline-variant/30 hover:bg-surface-container-low hover:text-primary font-label-md font-medium transition-colors whitespace-nowrap">
            Acrylic Soft
        </button>
        <button class="px-5 py-2.5 rounded-full bg-surface-container-lowest text-on-surface-variant border border-outline-variant/30 hover:bg-surface-container-low hover:text-primary font-label-md font-medium transition-colors whitespace-nowrap">
            Denim Offcuts
        </button>
    </div>

    <!-- Main Chart Area (Massive SVG Spline) -->
    <div class="glass-panel p-xl rounded-[2rem] border-white/80 bg-white/60 h-[600px] flex flex-col relative shadow-[0_12px_40px_rgba(0,53,39,0.05)]">
        
        <!-- Live Stat overlay -->
        <div class="absolute top-8 left-10 z-20">
            <div class="font-label-sm text-outline font-bold uppercase tracking-wider mb-1">Current Baseline</div>
            <div class="flex items-end gap-3">
                <span class="font-headline-lg text-[48px] text-primary font-bold leading-none tracking-tight">₹48.20</span>
                <span class="font-body-md text-on-surface-variant mb-2">/ kg</span>
                <span class="flex items-center gap-1 text-secondary font-bold bg-secondary/10 px-2 py-1 rounded-md mb-2">
                    <span class="material-symbols-filled text-[14px]">trending_up</span> 2.4%
                </span>
            </div>
        </div>

        <div class="flex-1 w-full relative mt-24 border-b-2 border-l-2 border-outline-variant/30 pb-4 pl-4">
            <!-- Y Axis Labels -->
            <div class="absolute left-[-40px] top-0 bottom-8 flex flex-col justify-between text-[12px] text-outline font-semibold">
                <span>₹60</span>
                <span>₹55</span>
                <span>₹50</span>
                <span>₹45</span>
                <span>₹40</span>
                <span>₹35</span>
            </div>
            <!-- X Axis Labels -->
            <div class="absolute bottom-[-30px] left-4 right-0 flex justify-between text-[12px] text-outline font-semibold">
                <span>Aug 1</span>
                <span>Aug 15</span>
                <span>Sep 1</span>
                <span>Sep 15</span>
                <span>Oct 1</span>
                <span>Oct 15</span>
                <span class="text-primary">Today</span>
            </div>
            
            <!-- Graph SVG -->
            <svg class="w-full h-full overflow-visible" preserveAspectRatio="none" viewBox="0 0 100 100">
                <!-- Grid Lines -->
                <path d="M 0 20 L 100 20 M 0 40 L 100 40 M 0 60 L 100 60 M 0 80 L 100 80" fill="none" stroke="currentColor" class="text-outline-variant/30" stroke-width="0.3" stroke-dasharray="2 2"></path>
                
                <!-- Spline Area Fill -->
                <path d="M 0 85 C 10 90, 20 60, 35 75 C 50 90, 60 40, 75 50 C 85 55, 95 20, 100 15 L 100 100 L 0 100 Z" fill="url(#main-gradient)" opacity="0.4"></path>
                
                <!-- Spline Line -->
                <path d="M 0 85 C 10 90, 20 60, 35 75 C 50 90, 60 40, 75 50 C 85 55, 95 20, 100 15" fill="none" stroke="url(#main-line)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="drop-shadow-[0_8px_12px_rgba(0,108,73,0.4)]"></path>
                
                <!-- Data Points -->
                <circle cx="35" cy="75" r="1.5" fill="white" stroke="#006c49" stroke-width="1"></circle>
                <circle cx="75" cy="50" r="1.5" fill="white" stroke="#006c49" stroke-width="1"></circle>
                
                <!-- Active Hover Point -->
                <circle cx="100" cy="15" r="2.5" fill="#006c49" stroke="white" stroke-width="1.5" class="animate-pulse shadow-[0_0_15px_#006c49]"></circle>
                
                <defs>
                    <linearGradient id="main-gradient" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#006c49" stop-opacity="0.4"></stop>
                        <stop offset="100%" stop-color="#003527" stop-opacity="0"></stop>
                    </linearGradient>
                    <linearGradient id="main-line" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0%" stop-color="#80bea6"></stop>
                        <stop offset="100%" stop-color="#003527"></stop>
                    </linearGradient>
                </defs>
            </svg>
        </div>
    </div>
@endsection
