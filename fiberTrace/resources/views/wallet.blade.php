@extends('layouts.dashboard')

@section('title', 'Escrow Wallet - FibreTrace')

@section('page-title', 'Escrow Wallet')

@section('dashboard-content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-md mb-lg">
        <div>
            <h2 class="font-headline-md text-primary font-bold">Financial Center</h2>
            <p class="font-body-sm text-on-surface-variant">Manage your escrow balance, view yield reports, and withdraw funds securely.</p>
        </div>
        
        <div class="flex gap-3">
            <button class="btn-magnetic bg-surface-container-lowest text-primary border border-outline-variant/50 font-label-sm px-5 py-2.5 rounded-xl hover:border-primary shadow-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">history</span> Statement
            </button>
            @if(auth()->user()->isSeller())
            <button class="btn-magnetic bg-primary text-white font-label-sm px-5 py-2.5 rounded-xl hover:bg-secondary shadow-md flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">account_balance</span> Withdraw Funds
            </button>
            @endif
        </div>
    </div>

    <!-- Top Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-lg mb-xl">
        <!-- Balance Card -->
        <div class="md:col-span-2 glass-card rounded-[2rem] p-xl border-white/80 shadow-[0_12px_40px_rgba(0,53,39,0.05)] bg-gradient-to-br from-primary to-primary-fixed text-white relative overflow-hidden group">
            <!-- Background Decorations -->
            <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-secondary/30 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="absolute top-0 right-0 w-full h-full bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMSIgZmlsbD0iI2ZmZmZmZiIgZmlsbC1vcGFjaXR5PSIwLjA1Ii8+PC9zdmc+')] mix-blend-overlay"></div>
            
            <div class="relative z-10 flex flex-col h-full justify-between">
                <div class="flex justify-between items-start mb-8">
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/20">
                        <span class="w-2 h-2 rounded-full bg-secondary shadow-[0_0_8px_#006c49]"></span>
                        <span class="font-label-sm text-white font-semibold uppercase tracking-wider text-[10px]">Active Escrow</span>
                    </div>
                    <span class="material-symbols-outlined text-[32px] text-white/50">account_balance_wallet</span>
                </div>
                
                <div>
                    <div class="font-label-md text-white/70 uppercase tracking-wider mb-2">Available Balance</div>
                    <div class="font-headline-lg text-[48px] font-bold tracking-tight flex items-baseline gap-1">
                        <span class="text-3xl font-medium text-white/70">₹</span>{{ number_format($totalEscrow, 2) }}
                    </div>
                </div>
                
                <div class="mt-8 pt-6 border-t border-white/20 flex gap-8">
                    @if(auth()->user()->isSeller())
                        <div>
                            <div class="text-[11px] text-white/60 uppercase tracking-wider mb-1">Total Released (YTD)</div>
                            <div class="font-label-lg font-bold text-tertiary-fixed">₹{{ number_format($totalReleased, 2) }}</div>
                        </div>
                    @elseif(auth()->user()->isBuyer())
                        <div>
                            <div class="text-[11px] text-white/60 uppercase tracking-wider mb-1">Total Spent (YTD)</div>
                            <div class="font-label-lg font-bold text-tertiary-fixed">₹{{ number_format($totalSpent, 2) }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Yield Card -->
        <div class="glass-card rounded-[2rem] p-lg border-white/80 shadow-[0_8px_32px_rgba(0,53,39,0.03)] bg-white/60 flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-full bg-tertiary/10 text-tertiary flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-[24px]">trending_up</span>
                </div>
                <h3 class="font-headline-sm text-primary font-bold">Platform Stats</h3>
                <p class="font-body-sm text-on-surface-variant mt-2">All transactions are fully secured by FibreTrace's verified Escrow framework.</p>
            </div>
            
            <div class="mt-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-label-sm text-outline font-bold uppercase tracking-wider">Completed Transactions</span>
                    <span class="font-label-md text-tertiary font-bold">{{ $transactions->where('payment_status', 'released')->count() }}</span>
                </div>
                <div class="w-full bg-surface-container-low rounded-full h-2 overflow-hidden">
                    <div class="bg-tertiary h-2 rounded-full" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="glass-panel rounded-[1.5rem] bg-white/70 border-white/90 shadow-[0_8px_32px_rgba(0,53,39,0.03)] overflow-hidden">
        <div class="p-lg border-b border-outline-variant/30 flex justify-between items-center bg-surface-container-lowest/50">
            <h3 class="font-headline-sm text-primary font-bold">Transaction History</h3>
        </div>
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-lowest/30 border-b border-outline-variant/30">
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Date</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Reference / Lot</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Type</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Status</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    @forelse($transactions as $txn)
                    @php
                        $isSeller = auth()->id() === $txn->seller_id;
                    @endphp
                    <tr class="hover:bg-surface-container-lowest transition-colors">
                        <td class="p-4 font-body-sm text-on-surface">{{ $txn->created_at->format('M d, Y') }}</td>
                        <td class="p-4">
                            <div class="font-label-md text-primary font-bold">
                                <a href="{{ route('settlement.show', $txn) }}" class="hover:underline">Settlement #SET-{{ $txn->id }}</a>
                            </div>
                            <div class="text-[11px] text-outline">Lot #FT-{{ $txn->lot_id }}</div>
                        </td>
                        <td class="p-4">
                            @if($isSeller)
                                <span class="bg-surface-container-highest text-on-surface border border-outline-variant/30 text-[10px] font-bold px-2 py-0.5 rounded-md uppercase">Sale Deposit</span>
                            @else
                                <span class="bg-surface-container-highest text-on-surface border border-outline-variant/30 text-[10px] font-bold px-2 py-0.5 rounded-md uppercase">Purchase</span>
                            @endif
                        </td>
                        <td class="p-4">
                            @if($txn->payment_status === 'pending')
                                <span class="flex items-center gap-1 text-[11px] font-bold text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[14px]">pending</span> Pending
                                </span>
                            @elseif($txn->payment_status === 'escrow')
                                <span class="flex items-center gap-1 text-[11px] font-bold text-secondary">
                                    <span class="material-symbols-outlined text-[14px]">lock</span> In Escrow
                                </span>
                            @elseif($txn->payment_status === 'released')
                                <span class="flex items-center gap-1 text-[11px] font-bold text-primary">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span> Released
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-right font-label-md {{ $isSeller ? 'text-secondary' : 'text-on-surface' }} font-bold">
                            {{ $isSeller ? '+' : '-' }} ₹{{ number_format($txn->total_amount, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-on-surface-variant font-label-md">No transactions found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
        <div class="p-4 border-t border-outline-variant/30 bg-surface-container-lowest/50">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
@endsection
