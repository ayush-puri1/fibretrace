@extends('layouts.app')

@section('title', 'Verification Pending - FibreTrace')

@section('body-classes', 'bg-surface text-on-surface font-body-md min-h-screen flex selection:bg-secondary-container selection:text-primary relative overflow-hidden')

@section('background')
    <!-- Mesh Background with distinct warning hue -->
    <div class="fixed inset-0 bg-gradient-to-br from-surface to-tertiary-fixed/30 pointer-events-none mix-blend-multiply opacity-80"></div>
    <div class="fixed inset-0 mesh-bg pointer-events-none mix-blend-overlay opacity-30"></div>
@endsection

@section('header')
    <!-- Minimal Top Nav -->
    <div class="absolute top-0 w-full p-md md:p-lg flex justify-between items-center z-50">
        <a href="{{ url('/') }}" class="flex items-center gap-xs text-primary hover:opacity-80 transition-opacity">
            <span class="font-headline-sm text-headline-sm font-bold tracking-tight">FibreTrace</span>
        </a>
        <div class="font-label-md text-on-surface-variant flex items-center gap-sm bg-white/40 backdrop-blur-md px-4 py-2 rounded-full border border-white/60">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-error hover:underline font-semibold flex items-center gap-1">
                    Sign Out <span class="material-symbols-outlined text-[16px]">logout</span>
                </button>
            </form>
        </div>
    </div>
@endsection

@section('content')
    <main class="flex-1 flex items-center justify-center p-md relative z-10 w-full h-screen">
        
        <!-- Decorative Ambient Orbs -->
        <div class="absolute top-1/4 right-1/3 w-64 h-64 bg-tertiary/20 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute bottom-1/4 left-1/3 w-80 h-80 bg-secondary/10 rounded-full blur-3xl animate-blob" style="animation-delay: 2s;"></div>

        <!-- Main Gatekeeper Card -->
        <div class="w-full max-w-[540px] glass-panel p-xl rounded-[2.5rem] bg-white/60 border-white/90 shadow-[0_24px_48px_rgba(0,53,39,0.1)] animate-slide-in relative overflow-hidden">
            
            <!-- Warning Stripe -->
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-tertiary to-error"></div>
            
            <!-- Header Icon -->
            <div class="flex flex-col items-center text-center mb-lg">
                <div class="relative mb-6">
                    <div class="absolute inset-0 bg-tertiary/20 rounded-full animate-ping"></div>
                    <div class="w-20 h-20 rounded-full bg-surface-container-lowest border-4 border-white flex items-center justify-center relative z-10 shadow-lg text-tertiary">
                        <span class="material-symbols-outlined text-[40px]">admin_panel_settings</span>
                    </div>
                </div>
                
                <h1 class="font-headline-lg text-[32px] leading-tight text-primary font-bold tracking-tight mb-2">Verification Pending</h1>
                <p class="font-body-md text-on-surface-variant text-[15px] px-4">Your account registration was successful. We are currently verifying your GSTIN and business credentials.</p>
            </div>

            <!-- Status Box -->
            <div class="bg-surface-container-lowest/80 border border-outline-variant/30 rounded-2xl p-md mb-lg">
                <div class="flex items-center justify-between mb-4 pb-4 border-b border-outline-variant/30">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-surface-variant text-on-surface-variant flex items-center justify-center">
                            <span class="material-symbols-outlined text-[20px]">store</span>
                        </div>
                        <div>
                            <div class="font-label-sm text-primary font-bold">Global Textiles Ltd.</div>
                            <div class="text-[11px] text-outline font-semibold font-mono mt-0.5">GSTIN: 03AAAAA0000A1Z5</div>
                        </div>
                    </div>
                    <span class="bg-tertiary/10 text-tertiary border border-tertiary/20 text-[10px] font-bold px-2 py-1 rounded-md flex items-center gap-1">
                        <span class="material-symbols-outlined text-[12px]">schedule</span> UNDER REVIEW
                    </span>
                </div>
                
                <div class="flex items-start gap-3">
                    <span class="material-symbols-filled text-secondary mt-0.5">info</span>
                    <div>
                        <div class="font-label-sm text-on-surface font-semibold">Estimated Review Time: 24-48 Hours</div>
                        <div class="text-[12px] text-on-surface-variant leading-relaxed mt-1">
                            To protect the integrity of the FibreTrace marketplace, all accounts undergo a manual KYC review. You will receive an email as soon as you are approved to access the trading floor.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Support Action -->
            <div class="flex flex-col sm:flex-row gap-3">
                <button class="btn-magnetic bg-white border border-outline-variant/50 text-on-surface-variant font-label-lg flex-1 py-3.5 rounded-xl hover:border-primary hover:text-primary transition-all shadow-sm flex justify-center items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">help_center</span> Contact Support
                </button>
                <a href="{{ url('/') }}" class="btn-magnetic bg-surface-container text-primary font-label-lg flex-1 py-3.5 rounded-xl hover:bg-surface-container-high transition-all shadow-sm flex justify-center items-center gap-2">
                    Return Home
                </a>
            </div>
        </div>
    </main>
@endsection
