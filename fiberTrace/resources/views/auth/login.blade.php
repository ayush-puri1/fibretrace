@extends('layouts.app')

@section('title', 'Log in - FibreTrace')

@section('body-classes', 'bg-surface text-on-surface font-body-md min-h-screen flex selection:bg-secondary-container selection:text-primary relative overflow-hidden')

@section('background')
    <!-- Mesh Background -->
    <div class="fixed inset-0 bg-gradient-to-br from-surface to-tertiary-fixed/30 pointer-events-none mix-blend-multiply opacity-80"></div>
    <div class="fixed inset-0 mesh-bg pointer-events-none mix-blend-overlay opacity-30"></div>
@endsection

@section('header')
    <!-- Minimal Top Nav -->
    <div class="absolute top-0 w-full p-md md:p-lg flex justify-between items-center z-50">
        <a href="{{ url('/') }}" class="flex items-center gap-xs text-primary hover:opacity-80 transition-opacity">
            <span class="font-headline-sm text-headline-sm font-bold tracking-tight">FibreTrace</span>
        </a>
    </div>
@endsection

@section('content')
    <main class="flex-1 flex items-center justify-center p-md relative z-10 w-full min-h-screen">
        
        <!-- Decorative Ambient Orbs -->
        <div class="absolute top-1/4 right-1/3 w-64 h-64 bg-tertiary/20 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute bottom-1/4 left-1/3 w-80 h-80 bg-secondary/10 rounded-full blur-3xl animate-blob" style="animation-delay: 2s;"></div>

        <!-- Main Login Card -->
        <div class="w-full max-w-[480px] glass-panel p-xl rounded-[2.5rem] bg-white/60 border-white/90 shadow-[0_24px_48px_rgba(0,53,39,0.1)] animate-slide-in relative overflow-hidden">
            
            <!-- Accent Stripe -->
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-secondary to-primary"></div>
            
            <div class="mb-8 text-center">
                <h1 class="font-headline-lg text-[32px] leading-tight text-primary font-bold tracking-tight mb-2">Welcome Back</h1>
                <p class="font-body-md text-on-surface-variant text-[15px]">Sign in to access the marketplace.</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block font-label-md text-primary font-semibold mb-1 uppercase tracking-wider text-[11px]">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="w-full bg-white/80 border border-outline-variant/50 rounded-xl px-4 py-3 text-on-surface outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm transition-all" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-error text-[12px]" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block font-label-md text-primary font-semibold mb-1 uppercase tracking-wider text-[11px]">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           class="w-full bg-white/80 border border-outline-variant/50 rounded-xl px-4 py-3 text-on-surface outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm transition-all" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-error text-[12px]" />
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between mt-2">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" name="remember" class="rounded border-outline-variant/50 text-primary shadow-sm focus:ring-primary bg-white/80">
                        <span class="ms-2 text-sm text-on-surface-variant font-medium">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm font-semibold text-primary hover:text-secondary hover:underline transition-colors" href="{{ route('password.request') }}">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-magnetic w-full bg-primary text-white font-label-lg py-4 rounded-xl hover:bg-secondary transition-all shadow-[0_8px_20px_rgba(0,53,39,0.2)] mt-4">
                    Sign In
                </button>

                <div class="text-center mt-4 text-on-surface-variant text-sm">
                    Don't have an account? <a href="{{ route('register') }}" class="text-primary font-semibold hover:underline">Register here</a>
                </div>
            </form>
        </div>
    </main>
@endsection
