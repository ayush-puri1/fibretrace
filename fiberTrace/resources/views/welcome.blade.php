@extends('layouts.app')

@section('title', 'FibreTrace - Industrial Circularity Solutions')

@section('background')
    <!-- Abstract Mesh Background -->
    <div class="fixed inset-0 mesh-bg pointer-events-none mix-blend-multiply opacity-50"></div>
@endsection

@section('header')
    <!-- TopAppBar -->
    <header class="bg-surface/70 backdrop-blur-lg flex justify-between items-center h-20 px-md md:px-lg w-full sticky top-0 z-50 border-b border-outline-variant/30 transition-all duration-300 shadow-sm" id="main-header">
        <div class="flex items-center gap-xl">
            <span class="font-headline-md text-headline-md font-bold text-primary tracking-tight cursor-pointer hover:opacity-80 transition-opacity">FibreTrace</span>
            <nav class="hidden md:flex gap-lg">
                <a class="nav-link font-label-lg text-label-lg text-on-surface-variant hover:text-primary transition-colors px-xs py-base rounded-DEFAULT cursor-pointer" href="{{ url('/marketplace') }}">Marketplace</a>
                <a class="nav-link font-label-lg text-label-lg text-on-surface-variant hover:text-primary transition-colors px-xs py-base rounded-DEFAULT cursor-pointer" href="{{ url('/auctions') }}">Auctions</a>
                <a class="nav-link font-label-lg text-label-lg text-on-surface-variant hover:text-primary transition-colors px-xs py-base rounded-DEFAULT cursor-pointer" href="{{ url('/dashboard') }}">Inventory</a>
            </nav>
        </div>
        <div class="flex items-center gap-sm">
            <a href="{{ url('/register') }}" class="btn-magnetic bg-primary text-white font-label-lg px-6 py-2 rounded-full hover:bg-secondary transition-all shadow-sm">
                Register
            </a>
            <a href="{{ url('/login') }}" class="btn-magnetic bg-surface-container-lowest text-primary border border-outline-variant/60 font-label-lg px-6 py-2 rounded-full hover:border-primary transition-all shadow-sm">
                Login
            </a>
        </div>
    </header>
@endsection

@section('content')
    <!-- Live Ticker -->
    <div class="w-full bg-inverse-surface/95 backdrop-blur-md text-inverse-on-surface py-sm flex items-center justify-start overflow-hidden relative shadow-md border-b border-white/10 z-20">
        <div class="absolute left-0 top-0 bottom-0 z-10 px-container-padding bg-gradient-to-r from-inverse-surface via-inverse-surface/90 to-transparent flex items-center pr-xl">
            <span class="font-bold text-secondary-fixed uppercase tracking-wider text-label-md flex items-center gap-xs">
                <span class="w-2 h-2 rounded-full bg-error animate-pulse shadow-[0_0_8px_rgba(186,26,26,0.8)]"></span>
                Live Baseline
            </span>
        </div>
        <div class="absolute right-0 top-0 bottom-0 z-10 w-32 bg-gradient-to-l from-inverse-surface to-transparent"></div>
        <div class="flex animate-marquee pl-[250px] hover:[animation-play-state:paused]">
            <!-- Dynamic Data Block -->
            <div class="flex gap-xl whitespace-nowrap items-center font-label-md shrink-0 pr-xl">
                @foreach($marketPrices as $price)
                    @php
                        $isUp = $price->previous_price ? ($price->price_per_kg > $price->previous_price) : true;
                        $isDown = $price->previous_price ? ($price->price_per_kg < $price->previous_price) : false;
                        $colorClass = $isUp ? 'price-up text-secondary-fixed' : ($isDown ? 'price-down text-error-container' : 'text-surface-variant');
                        $icon = $isUp ? 'arrow_upward' : ($isDown ? 'arrow_downward' : 'horizontal_rule');
                        $animateClass = $isUp ? 'animate-bounce' : '';
                    @endphp
                    <div class="flex items-center gap-xs px-sm py-1.5 rounded-full bg-white/5 backdrop-blur-sm border border-white/10 {{ $isUp ? 'price-up' : ($isDown ? 'price-down' : '') }} transition-all hover:bg-white/10 cursor-default">
                        <span class="text-tertiary-fixed-dim">{{ $price->fiber_category }}</span>
                        <span class="font-body-sm text-body-sm {{ $colorClass }} font-medium">₹{{ number_format($price->price_per_kg, 2) }}/kg</span>
                        <span class="material-symbols-filled {{ $colorClass }} text-[16px] {{ $animateClass }}">{{ $icon }}</span>
                    </div>
                @endforeach
            </div>
            
            <!-- Duplicate for seamless marquee -->
            <div class="flex gap-xl whitespace-nowrap items-center font-label-md shrink-0 pr-xl">
                @foreach($marketPrices as $price)
                    @php
                        $isUp = $price->previous_price ? ($price->price_per_kg > $price->previous_price) : true;
                        $isDown = $price->previous_price ? ($price->price_per_kg < $price->previous_price) : false;
                        $colorClass = $isUp ? 'price-up text-secondary-fixed' : ($isDown ? 'price-down text-error-container' : 'text-surface-variant');
                        $icon = $isUp ? 'arrow_upward' : ($isDown ? 'arrow_downward' : 'horizontal_rule');
                        $animateClass = $isUp ? 'animate-bounce' : '';
                    @endphp
                    <div class="flex items-center gap-xs px-sm py-1.5 rounded-full bg-white/5 backdrop-blur-sm border border-white/10 {{ $isUp ? 'price-up' : ($isDown ? 'price-down' : '') }} transition-all hover:bg-white/10 cursor-default">
                        <span class="text-tertiary-fixed-dim">{{ $price->fiber_category }}</span>
                        <span class="font-body-sm text-body-sm {{ $colorClass }} font-medium">₹{{ number_format($price->price_per_kg, 2) }}/kg</span>
                        <span class="material-symbols-filled {{ $colorClass }} text-[16px] {{ $animateClass }}">{{ $icon }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="relative w-full max-w-[1440px] mx-auto px-container-padding py-[80px] md:py-[120px] grid grid-cols-1 md:grid-cols-2 gap-xl items-center overflow-hidden animate-fade-in-up group" id="hero-section">
        <!-- Background glow -->
        <div class="absolute inset-0 bg-gradient-to-br from-surface-container-low/50 to-transparent -z-20 pointer-events-none transition-transform duration-1000 ease-out group-hover:scale-105"></div>
        
        <div class="flex flex-col gap-lg z-10 relative">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-secondary-container/40 border border-secondary/30 w-fit backdrop-blur-sm shadow-sm hover:bg-secondary-container/60 transition-colors cursor-default">
                <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                <span class="font-label-sm text-on-secondary-container tracking-wider uppercase font-semibold">Next-Gen Circularity</span>
            </div>
            
            <h1 class="font-headline-lg-mobile text-headline-lg-mobile md:text-[64px] md:leading-[72px] font-bold text-primary tracking-tight">
                Monetize Your <br><span class="text-secondary relative whitespace-nowrap inline-block hover:-translate-y-1 transition-transform duration-300">Textile Waste.<svg class="absolute -bottom-2 left-0 w-full text-secondary-fixed/60 h-4 z-[-1]" preserveAspectRatio="none" viewBox="0 0 100 10"><path d="M0 5 Q 50 10 100 5" fill="transparent" stroke="currentColor" stroke-width="6"></path></svg></span>
            </h1>
            
            <p class="font-body-lg text-[18px] text-on-surface-variant max-w-lg leading-relaxed mt-2 opacity-90">
                Transform industrial offcuts into verifiable assets. Join the premier B2B marketplace bridging the Ludhiana-Panipat circular corridor. Connect directly with certified recyclers and optimize your supply chain recovery.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-md pt-lg">
                <a href="{{ url('/register') }}" class="btn-liquid btn-magnetic bg-secondary text-on-secondary font-label-lg text-label-lg px-8 py-4 rounded-full transition-all flex items-center justify-center gap-sm shadow-[0_8px_24px_rgba(0,108,73,0.3)] hover:shadow-[0_16px_32px_rgba(0,53,39,0.4)]">
                    Register Company
                    <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </a>
                <a href="{{ url('/login') }}" class="btn-magnetic bg-white/60 backdrop-blur-md border-2 border-outline-variant/60 text-primary font-label-lg text-label-lg px-8 py-4 rounded-full hover:bg-white hover:border-primary/30 transition-all flex items-center justify-center gap-xs shadow-sm hover:shadow-lg">
                    Buyer Login
                </a>
            </div>
        </div>

        <!-- Dynamic Abstract Shapes with Parallax -->
        <div class="relative w-full aspect-square md:aspect-[4/3] flex items-center justify-center -z-10 mt-xl md:mt-0 parallax-element">
            <div class="absolute w-[85%] h-[85%] bg-gradient-to-tr from-primary-fixed/80 to-secondary-container/80 rounded-[40%_60%_70%_30%/40%_50%_60%_50%] animate-blob opacity-50 blur-[60px] mix-blend-multiply"></div>
            <div class="absolute w-[70%] h-[70%] bg-gradient-to-bl from-tertiary-fixed/60 to-surface-variant/80 rounded-[60%_40%_30%_70%/50%_60%_40%_50%] animate-blob opacity-60 blur-[40px] mix-blend-multiply" style="animation-delay: 2s; animation-duration: 12s;"></div>
            
            <div class="absolute w-72 h-28 bg-secondary/90 rounded-[120px] animate-float2 rotate-[35deg] top-[15%] left-[5%] mix-blend-multiply opacity-90 backdrop-blur-xl shadow-[0_20px_50px_rgba(0,108,73,0.4)] overflow-hidden border border-white/30 hover:scale-105 transition-transform duration-500 cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent -translate-x-full animate-[shimmer_2.5s_infinite]"></div>
            </div>
            
            <div class="absolute w-80 h-20 bg-primary-fixed/95 rounded-[120px] animate-float3 -rotate-[15deg] bottom-[20%] right-[0%] mix-blend-normal opacity-95 shadow-[0_24px_48px_rgba(0,53,39,0.25)] border border-white/50 flex items-center px-8 gap-4 hover:scale-105 transition-transform duration-500 cursor-pointer backdrop-blur-md">
                <div class="w-10 h-10 rounded-full bg-secondary text-white flex items-center justify-center shadow-inner"><span class="material-symbols-outlined text-base">check</span></div>
                <div class="flex-1">
                    <div class="h-2 w-3/4 bg-secondary/30 rounded-full mb-2"></div>
                    <div class="h-2 w-1/2 bg-secondary/15 rounded-full"></div>
                </div>
            </div>
            
            <div class="absolute w-48 h-48 border-[10px] border-surface-variant/70 rounded-[30%_70%_70%_30%/30%_30%_70%_70%] animate-float1 right-[15%] top-[10%] opacity-70 backdrop-blur-sm"></div>
            <div class="absolute w-36 h-36 bg-gradient-to-br from-tertiary-fixed to-surface-container-lowest rounded-full animate-float2 bottom-[15%] left-[15%] shadow-[inset_0_4px_12px_rgba(255,255,255,0.8),0_12px_32px_rgba(0,0,0,0.05)] flex items-center justify-center border border-white/80 hover:rotate-180 transition-transform duration-1000 cursor-pointer">
                <span class="material-symbols-outlined text-[48px] text-tertiary/60">recycling</span>
            </div>
        </div>
    </section>

    <!-- Transition Divider -->
    <div class="w-full h-24 bg-gradient-to-b from-transparent to-surface-container-low/30 relative z-0">
        <svg class="absolute bottom-0 w-full h-16 text-surface-container-low/30" fill="none" preserveAspectRatio="none" viewBox="0 0 1440 48" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 48H1440V0C1440 0 1140 48 720 48C300 48 0 0 0 0V48Z" fill="currentColor"></path>
        </svg>
    </div>

    <!-- Impact Stats -->
    <section class="bg-gradient-to-b from-surface-container-low/30 to-surface-container-low/80 py-xxl px-container-padding relative z-10 reveal scroll-section active">
        <div class="max-w-[1440px] mx-auto grid grid-cols-1 md:grid-cols-4 gap-lg relative">
            <!-- Data Pocket 1 -->
            <div class="bg-surface/80 backdrop-blur-xl p-[32px] rounded-[2rem] border border-white/60 shadow-[0_12px_40px_-12px_rgba(0,108,73,0.1)] flex flex-col gap-sm relative overflow-hidden group hover:-translate-y-3 hover:shadow-[0_24px_50px_-12px_rgba(0,108,73,0.2)] transition-all duration-500 cursor-default">
                <div class="absolute top-0 right-0 w-32 h-32 bg-secondary-fixed/40 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2 group-hover:scale-[2] group-hover:bg-secondary-fixed/60 transition-all duration-700"></div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-secondary to-primary-fixed transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
                <span class="material-symbols-outlined text-secondary text-[40px] mb-2 relative z-10 group-hover:rotate-12 transition-transform duration-300">recycling</span>
                <h3 class="font-headline-lg text-[56px] leading-none text-primary font-bold relative z-10 counter" data-target="{{ min(100, max(20, $verifiedMills * 15)) }}">0</h3>
                <div class="h-[2px] w-16 bg-gradient-to-r from-outline-variant to-transparent my-2 relative z-10"></div>
                <p class="font-label-lg text-label-lg text-on-surface-variant uppercase tracking-wider relative z-10 font-semibold">Verified Mills</p>
                <p class="font-body-sm text-body-sm text-outline relative z-10 group-hover:text-on-surface-variant transition-colors">Active registered mills participating in the circular corridor.</p>
            </div>

            <!-- Data Pocket 2 -->
            <div class="bg-surface/80 backdrop-blur-xl p-[32px] rounded-[2rem] border border-white/60 shadow-[0_12px_40px_-12px_rgba(0,108,73,0.1)] flex flex-col gap-sm relative overflow-hidden group hover:-translate-y-3 hover:shadow-[0_24px_50px_-12px_rgba(0,108,73,0.2)] transition-all duration-500 cursor-default">
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary-fixed/40 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2 group-hover:scale-[2] group-hover:bg-primary-fixed/60 transition-all duration-700"></div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-primary to-primary-fixed transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
                <span class="material-symbols-outlined text-secondary text-[40px] mb-2 relative z-10 group-hover:-translate-y-1 transition-transform duration-300">balance</span>
                <h3 class="font-headline-lg text-[56px] leading-none text-primary font-bold relative z-10 counter" data-target="{{ max(100, $totalTransactions * 12) }}">0</h3>
                <div class="h-[2px] w-16 bg-gradient-to-r from-outline-variant to-transparent my-2 relative z-10"></div>
                <p class="font-label-lg text-label-lg text-on-surface-variant uppercase tracking-wider relative z-10 font-semibold">Bids Placed</p>
                <p class="font-body-sm text-body-sm text-outline relative z-10 group-hover:text-on-surface-variant transition-colors">Real-time auction dynamics ensure fair market value for all lots.</p>
            </div>

            <!-- Data Pocket 3 -->
            <div class="bg-surface/80 backdrop-blur-xl p-[32px] rounded-[2rem] border border-white/60 shadow-[0_12px_40px_-12px_rgba(0,108,73,0.1)] flex flex-col gap-sm relative overflow-hidden group hover:-translate-y-3 hover:shadow-[0_24px_50px_-12px_rgba(0,108,73,0.2)] transition-all duration-500 cursor-default">
                <div class="absolute top-0 right-0 w-32 h-32 bg-tertiary-fixed/50 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2 group-hover:scale-[2] group-hover:bg-tertiary-fixed/70 transition-all duration-700"></div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-tertiary to-tertiary-fixed transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
                <span class="material-symbols-outlined text-secondary text-[40px] mb-2 relative z-10 group-hover:translate-x-1 transition-transform duration-300">local_shipping</span>
                <h3 class="font-headline-lg text-[56px] leading-none text-primary font-bold relative z-10 flex items-baseline"><span class="counter" data-target="24">0</span><span class="text-[32px]">hr</span></h3>
                <div class="h-[2px] w-16 bg-gradient-to-r from-outline-variant to-transparent my-2 relative z-10"></div>
                <p class="font-label-lg text-label-lg text-on-surface-variant uppercase tracking-wider relative z-10 font-semibold">Rapid Settlement</p>
                <p class="font-body-sm text-body-sm text-outline relative z-10 group-hover:text-on-surface-variant transition-colors">Automated logistics matching and digital escrow minimize delays.</p>
            </div>

            <!-- Data Pocket 4 -->
            <div class="bg-surface/80 backdrop-blur-xl p-[32px] rounded-[2rem] border border-white/60 shadow-[0_12px_40px_-12px_rgba(0,108,73,0.1)] flex flex-col gap-sm relative overflow-hidden group hover:-translate-y-3 hover:shadow-[0_24px_50px_-12px_rgba(0,108,73,0.2)] transition-all duration-500 cursor-default">
                <div class="absolute top-0 right-0 w-32 h-32 bg-secondary-container/50 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2 group-hover:scale-[2] group-hover:bg-secondary-container/70 transition-all duration-700"></div>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-secondary-container to-secondary transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
                <span class="material-symbols-outlined text-secondary text-[40px] mb-2 relative z-10 group-hover:scale-110 transition-transform duration-300">verified</span>
                <h3 class="font-headline-lg text-[56px] leading-none text-primary font-bold relative z-10 flex items-baseline"><span class="counter" data-target="{{ $tonsDiverted }}">0</span><span class="text-[32px]">t+</span></h3>
                <div class="h-[2px] w-16 bg-gradient-to-r from-outline-variant to-transparent my-2 relative z-10"></div>
                <p class="font-label-lg text-label-lg text-on-surface-variant uppercase tracking-wider relative z-10 font-semibold">Tons Diverted</p>
                <p class="font-body-sm text-body-sm text-outline relative z-10 group-hover:text-on-surface-variant transition-colors">Certified textile waste diverted from landfills annually.</p>
            </div>
        </div>
    </section>

    <!-- How It Works (Bento Grid) -->
    <section class="w-full relative px-container-padding py-[120px] bg-gradient-to-br from-surface-bright via-surface-container-low to-surface-bright reveal scroll-section active">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMSIgZmlsbD0iIzAwMzUyNyIgZmlsbC1vcGFjaXR5PSIwLjA1Ii8+PC9zdmc+')] opacity-30 mix-blend-overlay"></div>
        <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-b from-surface-container-low/80 to-transparent"></div>
        <div class="max-w-[1440px] mx-auto relative z-10">
            <div class="mb-[80px] text-center max-w-3xl mx-auto">
                <h2 class="font-headline-md text-[48px] leading-tight text-primary font-bold tracking-tight">How FibreTrace Works</h2>
                <p class="font-body-md text-[18px] text-on-surface-variant mt-md leading-relaxed">A streamlined, four-step protocol engineered for high-volume B2B commodity trading. Moving seamlessly from warehouse floor to circular feedstock.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-lg md:h-[500px]">
                <!-- Step 1 -->
                <div class="md:col-span-5 glass-card rounded-[2.5rem] p-xl flex flex-col justify-between hover:shadow-[0_30px_60px_-15px_rgba(0,108,73,0.2)] transition-all duration-500 hover:-translate-y-2 relative overflow-hidden group bg-white/50 border-white/80">
                    <div class="absolute -right-10 -top-10 w-48 h-48 bg-secondary-fixed/30 rounded-full blur-3xl group-hover:bg-secondary/30 group-hover:scale-150 transition-all duration-700"></div>
                    <div class="absolute inset-0 border-2 border-transparent group-hover:border-secondary/20 rounded-[2.5rem] transition-colors duration-500 pointer-events-none"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-md mb-lg">
                            <div class="w-14 h-14 rounded-full bg-secondary text-white flex items-center justify-center font-bold text-2xl shadow-[0_8px_16px_rgba(0,108,73,0.3)] group-hover:scale-110 transition-transform duration-300">1</div>
                            <h3 class="font-headline-sm text-[28px] text-primary font-bold">Sort & Profile</h3>
                        </div>
                        <p class="font-body-sm text-[16px] text-on-surface-variant mb-6 leading-relaxed group-hover:text-primary transition-colors duration-300">Digitize your inventory. Input composition, weight, and contaminant levels to generate a standardized FibreTrace Lot Profile.</p>
                    </div>
                    <div class="mt-auto flex gap-3 relative z-10">
                        <span class="px-5 py-2 bg-white/80 backdrop-blur-md text-primary font-semibold rounded-full shadow-sm border border-white hover:bg-secondary hover:text-white transition-colors cursor-default">Cotton</span>
                        <span class="px-5 py-2 bg-white/80 backdrop-blur-md text-primary font-semibold rounded-full shadow-sm border border-white hover:bg-secondary hover:text-white transition-colors cursor-default">Poly-Blend</span>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="md:col-span-7 glass-card rounded-[2.5rem] p-xl flex flex-col justify-between hover:shadow-[0_30px_60px_-15px_rgba(0,108,73,0.2)] transition-all duration-500 hover:-translate-y-2 relative overflow-hidden group bg-white/50 border-white/80">
                    <div class="absolute -right-20 top-1/2 -translate-y-1/2 w-96 h-96 bg-primary-fixed/40 rounded-full blur-3xl group-hover:bg-primary-fixed/60 transition-colors duration-700"></div>
                    <div class="absolute inset-0 border-2 border-transparent group-hover:border-primary/20 rounded-[2.5rem] transition-colors duration-500 pointer-events-none"></div>
                    <div class="absolute right-8 top-1/2 -translate-y-1/2 text-primary/10 group-hover:text-primary/20 group-hover:scale-125 group-hover:rotate-12 transition-all duration-700">
                        <span class="material-symbols-outlined text-[200px]">radar</span>
                    </div>
                    <div class="relative z-10 w-2/3">
                        <div class="flex items-center gap-md mb-lg">
                            <div class="w-14 h-14 rounded-full bg-secondary text-white flex items-center justify-center font-bold text-2xl shadow-[0_8px_16px_rgba(0,108,73,0.3)] group-hover:scale-110 transition-transform duration-300">2</div>
                            <h3 class="font-headline-sm text-[28px] text-primary font-bold">Discover</h3>
                        </div>
                        <p class="font-body-sm text-[16px] text-on-surface-variant leading-relaxed group-hover:text-primary transition-colors duration-300">Lots are instantly published to our verified buyer network. Advanced filtering allows recyclers to match exact feedstock requirements with precision accuracy.</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="md:col-span-4 glass-card rounded-[2.5rem] p-xl flex flex-col justify-between hover:shadow-[0_30px_60px_-15px_rgba(0,108,73,0.2)] transition-all duration-500 hover:-translate-y-2 relative overflow-hidden group bg-white/50 border-white/80">
                    <div class="absolute -left-10 -bottom-10 w-48 h-48 bg-error-container/50 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="absolute inset-0 border-2 border-transparent group-hover:border-error/20 rounded-[2.5rem] transition-colors duration-500 pointer-events-none"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-md mb-lg">
                            <div class="w-14 h-14 rounded-full bg-secondary text-white flex items-center justify-center font-bold text-2xl shadow-[0_8px_16px_rgba(0,108,73,0.3)] group-hover:scale-110 transition-transform duration-300">3</div>
                            <h3 class="font-headline-sm text-[28px] text-primary font-bold">Bid</h3>
                        </div>
                        <p class="font-body-sm text-[16px] text-on-surface-variant leading-relaxed group-hover:text-primary transition-colors duration-300">Engage in blind or open auctions. Real-time notifications keep you updated on highest offers until the closing bell rings.</p>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="md:col-span-8 glass-card rounded-[2.5rem] p-xl flex flex-col justify-between hover:shadow-[0_30px_60px_-15px_rgba(0,108,73,0.2)] transition-all duration-500 hover:-translate-y-2 relative overflow-hidden group bg-white/50 border-white/80">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-secondary-container/30 group-hover:to-secondary-container/50 transition-colors duration-700"></div>
                    <div class="absolute inset-0 border-2 border-transparent group-hover:border-secondary-container/50 rounded-[2.5rem] transition-colors duration-500 pointer-events-none"></div>
                    <div class="relative z-10 h-full flex flex-col">
                        <div class="flex items-center gap-md mb-lg">
                            <div class="w-14 h-14 rounded-full bg-secondary text-white flex items-center justify-center font-bold text-2xl shadow-[0_8px_16px_rgba(0,108,73,0.3)] group-hover:scale-110 transition-transform duration-300">4</div>
                            <h3 class="font-headline-sm text-[28px] text-primary font-bold">Settle & Dispatch</h3>
                        </div>
                        <div class="flex flex-col sm:flex-row justify-between gap-xl items-start sm:items-end mt-auto">
                            <p class="font-body-sm text-[16px] text-on-surface-variant max-w-md leading-relaxed group-hover:text-primary transition-colors duration-300">Upon award, auto-generate compliance documentation and secure logistics. Payments are held in escrow ensuring mutual security from dock to dock.</p>
                            <button class="btn-magnetic bg-primary text-white border border-transparent font-label-lg px-8 py-4 rounded-full hover:bg-secondary hover:shadow-[0_8px_24px_rgba(0,108,73,0.4)] transition-all flex items-center gap-3 shrink-0 z-20">
                                View Demo
                                <span class="material-symbols-outlined text-lg">play_circle</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    <!-- Footer -->
    <footer class="w-full py-xl px-container-padding flex flex-col md:flex-row justify-between items-center gap-md bg-surface-container-low border-t border-outline-variant/30 z-10 relative">
        <div class="flex flex-col gap-xs">
            <span class="font-headline-sm text-headline-sm text-primary tracking-tight">FibreTrace</span>
            <span class="font-body-sm text-body-sm text-on-surface-variant">© {{ date('Y') }} FibreTrace. Industrial Circularity Solutions.</span>
        </div>
        <nav class="flex flex-wrap gap-md justify-center md:justify-end">
            <a class="nav-link font-label-sm text-label-sm transition-colors inline-block text-on-surface-variant hover:text-primary py-1" href="#">Privacy Policy</a>
            <a class="nav-link font-label-sm text-label-sm transition-colors inline-block text-on-surface-variant hover:text-primary py-1" href="#">Terms of Service</a>
            <a class="nav-link font-label-sm text-label-sm transition-colors inline-block text-on-surface-variant hover:text-primary py-1" href="#">Sustainability Report</a>
            <a class="nav-link font-label-sm text-label-sm transition-colors inline-block text-on-surface-variant hover:text-primary py-1" href="#">Contact Support</a>
        </nav>
    </footer>
@endsection

@push('scripts')
<script>
    // Header Blur & Shrink on Scroll
    const header = document.getElementById('main-header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('h-16', 'bg-surface/90', 'shadow-md');
            header.classList.remove('h-20', 'bg-surface/70', 'shadow-sm');
        } else {
            header.classList.add('h-20', 'bg-surface/70', 'shadow-sm');
            header.classList.remove('h-16', 'bg-surface/90', 'shadow-md');
        }
    });

    // Scroll Reveal Animation
    const reveals = document.querySelectorAll('.reveal');
    const revealOnScroll = () => {
        const windowHeight = window.innerHeight;
        const elementVisible = 150;

        reveals.forEach(reveal => {
            const elementTop = reveal.getBoundingClientRect().top;
            if (elementTop < windowHeight - elementVisible) {
                reveal.classList.add('active');
            }
        });
    };
    window.addEventListener('scroll', revealOnScroll);
    revealOnScroll(); // Initial check

    // Subtle Parallax Effect for Hero Elements
    document.addEventListener('mousemove', (e) => {
        const parallaxElements = document.querySelectorAll('.animate-float1, .animate-float2, .animate-float3, .animate-blob');
        const mouseX = e.clientX / window.innerWidth;
        const mouseY = e.clientY / window.innerHeight;
        // Parallax implementation goes here...
    });
</script>
@endpush
