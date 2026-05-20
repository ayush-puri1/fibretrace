@extends('layouts.app')

@section('title', 'Register - FibreTrace')

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
        <div class="font-label-md text-on-surface-variant flex items-center gap-sm bg-white/40 backdrop-blur-md px-4 py-2 rounded-full border border-white/60">
            Already have an account? 
            <a href="{{ route('login') }}" class="text-primary hover:underline font-semibold">Sign In</a>
        </div>
    </div>
@endsection

@section('content')
    <main class="flex-1 flex items-center justify-center p-md relative z-10 w-full min-h-screen pt-[100px] pb-[40px]">
        
        <!-- Decorative Ambient Orbs -->
        <div class="fixed top-1/4 left-1/4 w-64 h-64 bg-tertiary/20 rounded-full blur-3xl animate-blob"></div>
        <div class="fixed bottom-1/4 right-1/4 w-80 h-80 bg-secondary/10 rounded-full blur-3xl animate-blob" style="animation-delay: 2s;"></div>

        <!-- Main Register Card -->
        <div class="w-full max-w-[720px] glass-panel p-xl rounded-[2.5rem] bg-white/60 border-white/90 shadow-[0_24px_48px_rgba(0,53,39,0.1)] animate-slide-in relative overflow-hidden my-auto">
            
            <!-- Accent Stripe -->
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-primary to-secondary"></div>
            
            <div class="mb-8 text-center">
                <h1 class="font-headline-lg text-[32px] leading-tight text-primary font-bold tracking-tight mb-2">Create an Account</h1>
                <p class="font-body-md text-on-surface-variant text-[15px]">Join the verified network for textile waste trading.</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-5">
                @csrf

                <!-- Account Type (Role) -->
                <div>
                    <label class="block font-label-md text-primary font-semibold mb-3 uppercase tracking-wider text-[11px]">I want to register as a:</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="seller" class="peer sr-only" {{ old('role', 'seller') === 'seller' ? 'checked' : '' }}>
                            <div class="rounded-xl border border-outline-variant/50 px-4 py-4 bg-white/80 peer-checked:bg-primary/5 peer-checked:border-primary peer-checked:ring-1 peer-checked:ring-primary transition-all text-center">
                                <span class="material-symbols-outlined text-[28px] text-primary mb-2">factory</span>
                                <div class="font-bold text-primary">Seller</div>
                                <div class="text-[11px] text-on-surface-variant mt-1">Textile Mill / Factory</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="buyer" class="peer sr-only" {{ old('role') === 'buyer' ? 'checked' : '' }}>
                            <div class="rounded-xl border border-outline-variant/50 px-4 py-4 bg-white/80 peer-checked:bg-secondary/5 peer-checked:border-secondary peer-checked:ring-1 peer-checked:ring-secondary transition-all text-center">
                                <span class="material-symbols-outlined text-[28px] text-secondary mb-2">recycling</span>
                                <div class="font-bold text-secondary">Buyer</div>
                                <div class="text-[11px] text-on-surface-variant mt-1">Recycler / Spinner</div>
                            </div>
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('role')" class="mt-2 text-error text-[12px]" />
                </div>

                <hr class="border-outline-variant/30 my-2">

                <!-- User Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="name" class="block font-label-md text-primary font-semibold mb-1 uppercase tracking-wider text-[11px]">Full Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                               class="w-full bg-white/80 border border-outline-variant/50 rounded-xl px-4 py-3 text-on-surface outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm transition-all" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-error text-[12px]" />
                    </div>
                    <div>
                        <label for="phone" class="block font-label-md text-primary font-semibold mb-1 uppercase tracking-wider text-[11px]">Phone Number</label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required
                               class="w-full bg-white/80 border border-outline-variant/50 rounded-xl px-4 py-3 text-on-surface outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm transition-all" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2 text-error text-[12px]" />
                    </div>
                </div>

                <!-- Business Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="company_name" class="block font-label-md text-primary font-semibold mb-1 uppercase tracking-wider text-[11px]">Company Name</label>
                        <input id="company_name" type="text" name="company_name" value="{{ old('company_name') }}" required
                               class="w-full bg-white/80 border border-outline-variant/50 rounded-xl px-4 py-3 text-on-surface outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm transition-all" />
                        <x-input-error :messages="$errors->get('company_name')" class="mt-2 text-error text-[12px]" />
                    </div>
                    <div>
                        <label for="gstin" class="block font-label-md text-primary font-semibold mb-1 uppercase tracking-wider text-[11px]">GSTIN (15 characters)</label>
                        <input id="gstin" type="text" name="gstin" value="{{ old('gstin') }}" required maxlength="15" style="text-transform: uppercase"
                               class="w-full bg-white/80 border border-outline-variant/50 rounded-xl px-4 py-3 text-on-surface outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm transition-all" />
                        <x-input-error :messages="$errors->get('gstin')" class="mt-2 text-error text-[12px]" />
                    </div>
                </div>

                <!-- Location -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="city" class="block font-label-md text-primary font-semibold mb-1 uppercase tracking-wider text-[11px]">City</label>
                        <input id="city" type="text" name="city" value="{{ old('city') }}" required
                               class="w-full bg-white/80 border border-outline-variant/50 rounded-xl px-4 py-3 text-on-surface outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm transition-all" />
                        <x-input-error :messages="$errors->get('city')" class="mt-2 text-error text-[12px]" />
                    </div>
                    <div>
                        <label for="state" class="block font-label-md text-primary font-semibold mb-1 uppercase tracking-wider text-[11px]">State</label>
                        <input id="state" type="text" name="state" value="{{ old('state') }}" required
                               class="w-full bg-white/80 border border-outline-variant/50 rounded-xl px-4 py-3 text-on-surface outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm transition-all" />
                        <x-input-error :messages="$errors->get('state')" class="mt-2 text-error text-[12px]" />
                    </div>
                </div>

                <hr class="border-outline-variant/30 my-2">

                <!-- Account Credentials -->
                <div>
                    <label for="email" class="block font-label-md text-primary font-semibold mb-1 uppercase tracking-wider text-[11px]">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                           class="w-full bg-white/80 border border-outline-variant/50 rounded-xl px-4 py-3 text-on-surface outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm transition-all" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-error text-[12px]" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="password" class="block font-label-md text-primary font-semibold mb-1 uppercase tracking-wider text-[11px]">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                               class="w-full bg-white/80 border border-outline-variant/50 rounded-xl px-4 py-3 text-on-surface outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm transition-all" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-error text-[12px]" />
                    </div>
                    <div>
                        <label for="password_confirmation" class="block font-label-md text-primary font-semibold mb-1 uppercase tracking-wider text-[11px]">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                               class="w-full bg-white/80 border border-outline-variant/50 rounded-xl px-4 py-3 text-on-surface outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm transition-all" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-error text-[12px]" />
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-magnetic w-full bg-primary text-white font-label-lg py-4 rounded-xl hover:bg-secondary transition-all shadow-[0_8px_20px_rgba(0,53,39,0.2)] mt-4">
                    Submit Application for Verification
                </button>
                <div class="text-center mt-2 text-on-surface-variant text-[12px]">
                    By registering, you agree to FibreTrace's Terms of Service and Escrow policies.
                </div>
            </form>
        </div>
    </main>
@endsection
