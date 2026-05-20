@extends('layouts.admin')

@section('title', 'GSTIN Verification Queue - FibreTrace Admin')

@section('page-title', 'Verification Queue')

@section('admin-content')
    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="bg-secondary/10 border border-secondary/30 text-secondary font-label-sm px-4 py-3 rounded-xl mb-lg flex items-center gap-3">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-md mb-lg">
        <div>
            <h2 class="font-headline-md text-[#111] font-bold">Pending Registrations</h2>
            <p class="font-body-sm text-on-surface-variant">Review and verify factory/recycler GSTINs before granting platform access.</p>
        </div>
        
        <div class="flex items-center gap-2">
            @if($counts['pending'] > 0)
            <span class="bg-error/10 text-error font-bold px-3 py-1.5 rounded-lg border border-error/20 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-error animate-pulse"></span> {{ $counts['pending'] }} Pending
            </span>
            @endif
        </div>
    </div>

    {{-- Status Filter Tabs --}}
    <div class="flex gap-2 mb-lg flex-wrap">
        @foreach(['pending' => 'Pending', 'verified' => 'Verified', 'rejected' => 'Rejected', 'suspended' => 'Suspended'] as $tab => $label)
        <a href="{{ route('admin.verifications') }}?status={{ $tab }}"
           class="px-4 py-2 rounded-lg font-label-sm font-semibold border transition-colors
                  {{ $status === $tab ? 'bg-[#111] text-white border-[#111] shadow-sm' : 'bg-white text-on-surface-variant border-outline-variant/50 hover:bg-surface-container-lowest' }}">
            {{ $label }}
            <span class="ml-1.5 text-[11px] font-bold opacity-70">({{ $counts[$tab] }})</span>
        </a>
        @endforeach
    </div>

    <!-- Verification Table -->
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-lowest/50 border-b border-outline-variant/30">
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Date Submitted</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Company Name</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Account Type</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">GSTIN Number</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider">Contact Info</th>
                        <th class="p-4 font-label-sm text-outline font-bold uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    @forelse($users as $user)
                    <tr class="hover:bg-surface-container-lowest transition-colors group">
                        <td class="p-4 font-body-sm text-on-surface-variant whitespace-nowrap">{{ $user->created_at->format('d M, h:i A') }}</td>
                        <td class="p-4">
                            <div class="font-label-md text-[#111] font-bold">{{ $user->company_name }}</div>
                            <div class="text-[11px] text-outline">{{ $user->city }}, {{ $user->state }}</div>
                            @if($user->status === 'rejected' && $user->rejection_reason)
                                <div class="text-[11px] text-error mt-1 italic">Reason: {{ $user->rejection_reason }}</div>
                            @endif
                        </td>
                        <td class="p-4">
                            @if($user->role === 'seller')
                                <span class="bg-primary/10 text-primary border border-primary/20 text-[10px] font-bold px-2 py-0.5 rounded uppercase">Seller / Factory</span>
                            @else
                                <span class="bg-secondary/10 text-secondary border border-secondary/20 text-[10px] font-bold px-2 py-0.5 rounded uppercase">Buyer / Recycler</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="font-mono text-sm font-semibold text-[#111] bg-surface-container-lowest border border-outline-variant/30 px-2 py-1 rounded inline-block">{{ $user->gstin }}</div>
                        </td>
                        <td class="p-4">
                            <div class="font-body-sm text-[#111]">{{ $user->masked_phone }}</div>
                            <div class="text-[11px] text-outline">{{ $user->email }}</div>
                        </td>
                        <td class="p-4 text-right">
                            @if($user->status === 'pending')
                            <div class="flex items-center justify-end gap-2">
                                {{-- Reject Button → triggers modal --}}
                                <button onclick="document.getElementById('reject-modal-{{ $user->id }}').classList.remove('hidden')"
                                        class="btn-magnetic bg-surface-container-lowest text-error border border-error/30 hover:bg-error/10 font-label-sm px-3 py-1.5 rounded-lg shadow-sm transition-colors">
                                    Reject
                                </button>
                                {{-- Approve Form --}}
                                <form method="POST" action="{{ route('admin.verifications.approve', $user) }}">
                                    @csrf
                                    <button type="submit" class="btn-magnetic bg-[#111] text-white font-label-sm px-4 py-1.5 rounded-lg hover:bg-[#333] shadow-sm transition-colors">
                                        Approve
                                    </button>
                                </form>
                            </div>
                            @elseif($user->status === 'verified')
                                <span class="bg-secondary/10 text-secondary border border-secondary/20 text-[10px] font-bold px-2 py-0.5 rounded uppercase">Verified</span>
                            @elseif($user->status === 'rejected')
                                <span class="bg-error/10 text-error border border-error/20 text-[10px] font-bold px-2 py-0.5 rounded uppercase">Rejected</span>
                            @elseif($user->status === 'suspended')
                                <span class="bg-outline/10 text-outline border border-outline/20 text-[10px] font-bold px-2 py-0.5 rounded uppercase">Suspended</span>
                            @endif
                        </td>
                    </tr>

                    {{-- Reject Modal for this user --}}
                    @if($user->status === 'pending')
                    <tr>
                        <td colspan="6" class="p-0">
                            <div id="reject-modal-{{ $user->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                                <div class="bg-white rounded-2xl shadow-2xl p-xl w-full max-w-md mx-4">
                                    <h3 class="font-headline-sm text-[#111] font-bold mb-2">Reject Registration</h3>
                                    <p class="font-body-sm text-on-surface-variant mb-4">Provide a reason for rejecting <strong>{{ $user->company_name }}</strong>. This will be emailed to the applicant.</p>
                                    <form method="POST" action="{{ route('admin.verifications.reject', $user) }}">
                                        @csrf
                                        <textarea name="reason" rows="3" required placeholder="e.g., GSTIN format invalid, duplicate account detected..."
                                                  class="w-full border border-outline-variant/50 rounded-xl p-3 font-body-sm text-[#111] focus:border-error focus:ring-1 focus:ring-error outline-none mb-4 resize-none"></textarea>
                                        <div class="flex gap-3 justify-end">
                                            <button type="button" onclick="document.getElementById('reject-modal-{{ $user->id }}').classList.add('hidden')"
                                                    class="font-label-sm px-4 py-2 rounded-lg border border-outline-variant/50 text-on-surface-variant hover:bg-surface-container-lowest">
                                                Cancel
                                            </button>
                                            <button type="submit" class="btn-magnetic bg-error text-white font-label-sm px-5 py-2 rounded-lg hover:bg-error/90 shadow-sm">
                                                Confirm Rejection
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-on-surface-variant font-body-sm">
                            <span class="material-symbols-outlined text-[48px] text-outline-variant block mb-2">inbox</span>
                            No {{ $status }} registrations found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
        <div class="p-4 border-t border-outline-variant/30 flex items-center justify-between bg-surface-container-lowest/30">
            <span class="font-body-sm text-outline-variant">Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }}</span>
            <div class="flex gap-1">
                {{ $users->links() }}
            </div>
        </div>
        @endif
    </div>
@endsection
