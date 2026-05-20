@extends('layouts.admin')

@section('title', 'PII & Transaction Audit Ledger - FibreTrace Admin')

@section('page-title', 'Security & Audit Ledger')

@section('admin-content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-md mb-lg">
        <div>
            <h2 class="font-headline-md text-error font-bold flex items-center gap-2">
                <span class="material-symbols-outlined text-[28px]">lock_person</span> Transaction Audit Log
            </h2>
            <p class="font-body-sm text-on-surface-variant">Restricted view. All activity on this page is logged for compliance.</p>
        </div>
    </div>

    <!-- Security Warning Banner -->
    <div class="bg-error/10 border border-error/30 rounded-xl p-4 flex items-center gap-3 mb-lg">
        <span class="material-symbols-filled text-error text-[20px]">warning</span>
        <span class="font-body-sm text-error font-semibold">
            CONFIDENTIAL: You are viewing {{ $isSuperAdmin ? 'unmasked' : 'partially masked' }} PII data. All access to this page is logged and monitored for compliance.
        </span>
    </div>

    {{-- Activity Log Filter Bar --}}
    <form method="GET" action="{{ route('admin.audit') }}" class="bg-white rounded-2xl border border-outline-variant/30 p-4 mb-lg flex flex-wrap gap-3 items-end shadow-sm">
        <div class="flex flex-col gap-1">
            <label class="font-label-sm text-outline font-semibold">Action Type</label>
            <select name="action" class="px-3 py-2 rounded-lg border border-outline-variant/50 focus:border-[#111] outline-none text-label-sm font-semibold bg-white shadow-sm">
                <option value="">All Actions</option>
                @foreach($actionTypes as $type)
                <option value="{{ $type }}" {{ request('action') === $type ? 'selected' : '' }}>{{ str_replace('_', ' ', Str::title($type)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-col gap-1">
            <label class="font-label-sm text-outline font-semibold">From Date</label>
            <input type="date" name="from" value="{{ request('from') }}" class="px-3 py-2 rounded-lg border border-outline-variant/50 focus:border-[#111] outline-none text-label-sm bg-white shadow-sm">
        </div>
        <div class="flex flex-col gap-1">
            <label class="font-label-sm text-outline font-semibold">To Date</label>
            <input type="date" name="to" value="{{ request('to') }}" class="px-3 py-2 rounded-lg border border-outline-variant/50 focus:border-[#111] outline-none text-label-sm bg-white shadow-sm">
        </div>
        <button type="submit" class="px-5 py-2 bg-[#111] text-white font-label-sm rounded-lg hover:bg-[#333] transition-colors shadow-sm">Apply Filters</button>
        <a href="{{ route('admin.audit') }}" class="px-5 py-2 bg-surface-container-low text-on-surface-variant font-label-sm rounded-lg hover:bg-surface-container transition-colors">Clear</a>
    </form>

    {{-- Activity Log Table --}}
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden mb-xl">
        <div class="p-4 border-b border-outline-variant/20 flex justify-between items-center bg-surface-container-lowest/30">
            <h3 class="font-label-md text-[#111] font-bold">Platform Activity Log</h3>
            <span class="font-body-sm text-outline text-[12px]">{{ $logs->total() }} total events</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-lowest/50 border-b border-outline-variant/30">
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Timestamp</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Actor</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Action</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Description</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @forelse($logs as $log)
                    <tr class="hover:bg-surface-container-lowest transition-colors">
                        <td class="p-4">
                            <div class="font-mono text-[11px] text-[#111] font-semibold">{{ $log->created_at->format('d M Y') }}</div>
                            <div class="font-mono text-[11px] text-on-surface-variant">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="p-4">
                            @if($log->user)
                                <div class="font-label-sm text-[#111] font-semibold">{{ $log->user->company_name }}</div>
                                <div class="text-[11px] text-outline capitalize">{{ $log->user->role }}</div>
                            @else
                                <span class="text-[11px] text-outline italic">System</span>
                            @endif
                        </td>
                        <td class="p-4">
                            @php
                                $actionColor = match(true) {
                                    str_contains($log->action, 'verified') => 'bg-secondary/10 text-secondary border-secondary/20',
                                    str_contains($log->action, 'reject')   => 'bg-error/10 text-error border-error/20',
                                    str_contains($log->action, 'suspend')  => 'bg-error/10 text-error border-error/20',
                                    str_contains($log->action, 'bid')      => 'bg-primary/10 text-primary border-primary/20',
                                    default                                => 'bg-outline/10 text-outline border-outline/20',
                                };
                            @endphp
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $actionColor }} capitalize">
                                {{ str_replace('_', ' ', $log->action) }}
                            </span>
                        </td>
                        <td class="p-4 font-body-sm text-on-surface max-w-[320px]">
                            <div class="truncate" title="{{ $log->description }}">{{ $log->description }}</div>
                        </td>
                        <td class="p-4 font-mono text-[11px] text-outline">{{ $log->ip_address ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-on-surface-variant font-body-sm">
                            <span class="material-symbols-outlined text-[48px] text-outline-variant block mb-2">search_off</span>
                            No activity logs match your filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="p-4 border-t border-outline-variant/30 flex items-center justify-between bg-surface-container-lowest/30">
            <span class="font-body-sm text-outline-variant">Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} events</span>
            <div>{{ $logs->withQueryString()->links() }}</div>
        </div>
        @endif
    </div>

    <!-- Transaction Audit Table -->
    <div class="bg-white rounded-2xl border border-error/20 shadow-[0_4px_24px_rgba(186,26,26,0.02)] overflow-hidden">
        <div class="p-4 border-b border-error/20 bg-error/5 flex justify-between items-center">
            <h3 class="font-label-md text-error font-bold uppercase tracking-wider">Transaction Audit (PII {{ $isSuperAdmin ? 'Unmasked' : 'Masked' }})</h3>
        </div>
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-lowest/50 border-b border-outline-variant/30">
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Trans. / Date</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Lot Details</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider border-l border-error/10 bg-error/5">Seller</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider border-l border-error/10 bg-error/5">Buyer</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider text-right">Settlement</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    @forelse($transactions as $txn)
                    <tr class="hover:bg-surface-container-lowest transition-colors">
                        <td class="p-4">
                            <div class="font-mono text-[12px] font-bold text-[#111]">{{ $txn->transaction_number }}</div>
                            <div class="font-body-sm text-on-surface-variant mt-1 text-[11px]">{{ $txn->created_at->format('d M Y, H:i') }}</div>
                        </td>
                        <td class="p-4">
                            <div class="font-label-md text-primary font-bold">{{ $txn->lot->lot_number ?? 'N/A' }}</div>
                            <div class="text-[11px] text-on-surface-variant">{{ $txn->lot->fiber_type ?? '—' }}</div>
                            <div class="text-[11px] text-on-surface-variant">{{ number_format($txn->actual_weight_kg) }} kg @ ₹{{ $txn->agreed_price }}</div>
                        </td>
                        <td class="p-4 border-l border-error/10 bg-error/5">
                            <div class="font-label-sm text-[#111] font-bold">{{ $txn->seller->company_name ?? '—' }}</div>
                            @if($isSuperAdmin && $txn->seller)
                                <div class="text-[11px] text-[#111] font-mono mt-0.5">GSTIN: {{ $txn->seller->gstin }}</div>
                                <div class="text-[11px] text-[#111] mt-0.5">{{ $txn->seller->phone }}</div>
                            @else
                                <div class="text-[11px] text-outline italic">PII Masked</div>
                            @endif
                        </td>
                        <td class="p-4 border-l border-error/10 bg-error/5">
                            <div class="font-label-sm text-[#111] font-bold">{{ $txn->buyer->company_name ?? '—' }}</div>
                            @if($isSuperAdmin && $txn->buyer)
                                <div class="text-[11px] text-[#111] font-mono mt-0.5">GSTIN: {{ $txn->buyer->gstin }}</div>
                                <div class="text-[11px] text-[#111] mt-0.5">{{ $txn->buyer->phone }}</div>
                            @else
                                <div class="text-[11px] text-outline italic">PII Masked</div>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            <div class="font-label-md text-[#111] font-bold">₹{{ number_format($txn->total_amount, 2) }}</div>
                            @php
                                $paymentColors = [
                                    'released' => 'bg-secondary/10 text-secondary border-secondary/20',
                                    'paid'     => 'bg-primary/10 text-primary border-primary/20',
                                    'pending'  => 'bg-outline/10 text-outline border-outline/20',
                                    'disputed' => 'bg-error/10 text-error border-error/20',
                                ];
                            @endphp
                            <span class="{{ $paymentColors[$txn->payment_status] ?? 'bg-outline/10 text-outline' }} text-[9px] font-bold px-2 py-0.5 rounded-full border inline-block mt-1 uppercase">
                                {{ $txn->payment_status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-on-surface-variant font-body-sm">No transactions found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
