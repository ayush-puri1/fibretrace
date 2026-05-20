@extends('layouts.dashboard')

@section('title', 'Settlement #FT-' . $transaction->lot_id . ' - FibreTrace')

@section('page-title', 'Escrow Settlement')

@section('dashboard-content')
    <div class="max-w-[900px] mx-auto">
        <!-- Breadcrumb & Header -->
        <div class="mb-lg">
            <div class="flex items-center gap-2 text-on-surface-variant font-label-sm mb-2">
                <a href="{{ url('/dashboard') }}" class="hover:text-primary transition-colors">Overview</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-primary font-bold">Settlement #SET-{{ $transaction->id }}</span>
            </div>
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-md">
                <div>
                    <h2 class="font-headline-md text-primary font-bold flex items-center gap-3">
                        Settlement Invoice
                        @if($transaction->payment_status === 'pending')
                            <span class="bg-tertiary/10 text-tertiary border border-tertiary/20 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Awaiting Payment</span>
                        @elseif($transaction->payment_status === 'escrow')
                            <span class="bg-secondary/10 text-secondary border border-secondary/20 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">In Escrow</span>
                        @else
                            <span class="bg-outline-variant/30 text-outline border border-outline-variant/50 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">{{ $transaction->payment_status }}</span>
                        @endif
                    </h2>
                    <p class="font-body-sm text-on-surface-variant">For Lot #FT-{{ $transaction->lot_id }} ({{ $transaction->lot->primary_fiber }})</p>
                </div>
                
                <button class="btn-magnetic bg-surface-container-lowest text-primary border border-outline-variant/50 font-label-sm px-4 py-2.5 rounded-xl hover:border-primary shadow-sm flex items-center gap-2 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">download</span> Download PDF
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-secondary/10 border border-secondary/30 text-secondary font-label-sm px-4 py-3 rounded-xl mb-lg flex items-center gap-3">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-md mb-lg">
            <!-- Buyer Info -->
            <div class="bg-surface-container-lowest border border-outline-variant/30 p-md rounded-2xl">
                <div class="font-label-sm text-outline font-bold uppercase tracking-wider mb-3">Billed To (Buyer)</div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-full bg-secondary text-white flex items-center justify-center font-bold">
                        {{ substr($transaction->buyer->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="font-label-md text-primary font-bold">{{ $transaction->buyer->name }}</div>
                        <div class="text-[12px] text-on-surface-variant">Verified Buyer #{{ $transaction->buyer_id }}</div>
                    </div>
                </div>
                <div class="text-[12px] text-on-surface-variant leading-relaxed">
                    {{ $transaction->buyer->city ?? 'N/A' }}<br>
                    {{ $transaction->buyer->state ?? 'N/A' }}
                </div>
            </div>

            <!-- Seller Info -->
            <div class="bg-surface-container-lowest border border-outline-variant/30 p-md rounded-2xl">
                <div class="font-label-sm text-outline font-bold uppercase tracking-wider mb-3">Payable To (Seller)</div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold">
                        {{ substr($transaction->seller->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="font-label-md text-primary font-bold">{{ $transaction->seller->name }}</div>
                        <div class="text-[12px] text-on-surface-variant">Verified Seller #{{ $transaction->seller_id }}</div>
                    </div>
                </div>
                <div class="text-[12px] text-on-surface-variant leading-relaxed">
                    {{ $transaction->seller->city ?? 'N/A' }}<br>
                    {{ $transaction->seller->state ?? 'N/A' }}
                </div>
            </div>
        </div>

        <!-- Invoice Breakdown -->
        <div class="glass-panel p-lg rounded-[2rem] bg-white/70 border-white/90 shadow-[0_8px_32px_rgba(0,53,39,0.03)] mb-lg">
            
            <table class="w-full text-left mb-6">
                <thead>
                    <tr class="border-b border-outline-variant/50">
                        <th class="pb-3 font-label-sm text-outline font-bold uppercase tracking-wider">Description</th>
                        <th class="pb-3 font-label-sm text-outline font-bold uppercase tracking-wider text-right">Qty (kg)</th>
                        <th class="pb-3 font-label-sm text-outline font-bold uppercase tracking-wider text-right">Rate</th>
                        <th class="pb-3 font-label-sm text-outline font-bold uppercase tracking-wider text-right">Total (₹)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    <tr>
                        <td class="py-4">
                            <div class="font-label-md text-primary font-bold">{{ $transaction->lot->primary_fiber }}</div>
                            <div class="text-[11px] text-on-surface-variant">Lot #FT-{{ $transaction->lot_id }} • {{ $transaction->lot->is_color_sorted ? 'Color Sorted' : 'Mixed Colors' }}</div>
                        </td>
                        <td class="py-4 text-right font-body-sm">{{ number_format($transaction->actual_weight_kg) }}</td>
                        <td class="py-4 text-right font-body-sm">₹{{ number_format($transaction->agreed_price, 2) }}</td>
                        <td class="py-4 text-right font-label-md text-primary font-bold">{{ number_format($transaction->subtotal, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="w-full md:w-1/2 ml-auto">
                <div class="flex justify-between items-center py-2 text-on-surface-variant font-body-sm">
                    <span>Subtotal</span>
                    <span>₹{{ number_format($transaction->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-2 text-on-surface-variant font-body-sm border-b border-outline-variant/30">
                    <span>Platform Escrow Fee</span>
                    <span>₹{{ number_format($transaction->commission_amount, 2) }}</span>
                </div>
                <div class="flex justify-between items-center pt-4 mt-2">
                    <span class="font-label-lg text-primary font-bold">Total Amount Due</span>
                    <span class="font-headline-md text-secondary font-bold">₹{{ number_format($transaction->total_amount, 2) }}</span>
                </div>
            </div>
            
        </div>

        <!-- Action / Security Banner -->
        @if($transaction->payment_status === 'pending' && auth()->id() === $transaction->buyer_id)
        <div class="flex flex-col md:flex-row gap-lg items-center bg-surface-container-low border border-outline-variant/50 rounded-[1.5rem] p-md">
            <div class="flex-1 flex items-start gap-4">
                <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">lock</span>
                </div>
                <div>
                    <h4 class="font-label-md text-primary font-bold mb-1">Secure Escrow Protection</h4>
                    <p class="text-[12px] text-on-surface-variant leading-relaxed">Funds will be held securely in the FibreTrace Escrow account. They will only be released to the seller once material inspection and logistics are confirmed.</p>
                </div>
            </div>
            
            <form action="{{ route('settlement.pay', $transaction) }}" method="POST" class="w-full md:w-auto">
                @csrf
                <button type="submit" class="btn-magnetic bg-primary text-white font-label-lg px-8 py-4 rounded-xl hover:bg-secondary transition-all shadow-[0_8px_20px_rgba(0,53,39,0.2)] flex items-center gap-2 whitespace-nowrap w-full md:w-auto justify-center group relative overflow-hidden">
                    <span class="relative z-10">Authorize Payment</span>
                    <span class="material-symbols-outlined text-[20px] relative z-10">payments</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1s_infinite]"></div>
                </button>
            </form>
        </div>
        @elseif($transaction->payment_status === 'escrow')
        <div class="flex flex-col md:flex-row gap-lg items-center bg-secondary-container/20 border border-secondary/30 rounded-[1.5rem] p-md">
            <div class="flex-1 flex items-start gap-4">
                <div class="w-12 h-12 rounded-full bg-secondary/10 text-secondary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">verified_user</span>
                </div>
                <div>
                    <h4 class="font-label-md text-secondary font-bold mb-1">Funds in Escrow</h4>
                    <p class="text-[12px] text-on-surface-variant leading-relaxed">The payment has been successfully secured in escrow. The seller has been notified to dispatch the material.</p>
                </div>
            </div>
            <a href="{{ route('dispatch.show', $transaction) }}" class="btn-magnetic bg-white border border-outline-variant/30 text-primary font-label-lg px-8 py-4 rounded-xl hover:bg-surface-container-lowest transition-all shadow-sm flex items-center gap-2 whitespace-nowrap w-full md:w-auto justify-center group">
                <span class="relative z-10">View Dispatch Status</span>
                <span class="material-symbols-outlined text-[20px] relative z-10">local_shipping</span>
            </a>
        </div>
        @endif
    </div>
@endsection
