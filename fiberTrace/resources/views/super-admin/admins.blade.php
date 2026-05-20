@extends('layouts.superadmin')

@section('title', 'Admin Account Management - FibreTrace')

@section('page-title', 'Staff Management')

@section('superadmin-content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-md mb-lg">
        <div>
            <h2 class="font-headline-md text-white font-bold">Admin Accounts</h2>
            <p class="font-body-sm text-white/50">Manage platform moderators, their access levels, and security credentials.</p>
        </div>
        
        <div class="flex gap-2">
            <button onclick="document.getElementById('create-admin-modal').classList.remove('hidden')" class="btn-magnetic bg-tertiary text-white font-label-sm px-4 py-2.5 rounded-xl hover:bg-tertiary/90 shadow-sm flex items-center gap-2 transition-colors font-bold">
                <span class="material-symbols-outlined text-[18px]">person_add</span> Register New Admin
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-secondary/10 border border-secondary/30 text-secondary font-label-sm px-4 py-3 rounded-xl mb-lg flex items-center gap-3">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-error/10 border border-error/30 text-error font-label-sm px-4 py-3 rounded-xl mb-lg">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Staff Table -->
    <div class="bg-white/5 rounded-2xl border border-white/10 shadow-[0_4px_24px_rgba(0,0,0,0.2)] overflow-hidden backdrop-blur-sm">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-black/20 border-b border-white/10">
                        <th class="p-4 font-label-sm text-white/40 font-bold uppercase tracking-wider">Staff Member</th>
                        <th class="p-4 font-label-sm text-white/40 font-bold uppercase tracking-wider">Access Level</th>
                        <th class="p-4 font-label-sm text-white/40 font-bold uppercase tracking-wider">Status</th>
                        <th class="p-4 font-label-sm text-white/40 font-bold uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    
                    <!-- Row 1 (Root) -->
                    <tr class="bg-white/5 transition-colors group relative overflow-hidden">
                        <div class="absolute inset-y-0 left-0 w-1 bg-tertiary"></div>
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-tertiary text-on-primary-fixed flex items-center justify-center font-bold text-[14px]">R</div>
                                <div>
                                    <div class="font-label-md text-white font-bold flex items-center gap-2">Root Admin (You) <span class="material-symbols-outlined text-tertiary text-[14px]">verified</span></div>
                                    <div class="text-[11px] text-white/40">{{ auth()->user()->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            <span class="bg-tertiary/20 text-tertiary border border-tertiary/30 text-[10px] font-bold px-2 py-0.5 rounded uppercase">Level 3 (Root)</span>
                        </td>
                        <td class="p-4">
                            <span class="text-white text-[12px] font-semibold">Active</span>
                        </td>
                        <td class="p-4 text-right">
                            <span class="text-[11px] text-white/30 italic mr-2">Cannot edit root</span>
                        </td>
                    </tr>

                    @foreach($admins as $admin)
                    <tr class="{{ $admin->status === 'suspended' ? 'bg-error/5 hover:bg-error/10' : 'hover:bg-white/5' }} transition-colors group">
                        <td class="p-4 pl-5">
                            <div class="flex items-center gap-3 {{ $admin->status === 'suspended' ? 'opacity-40' : '' }}">
                                <div class="w-10 h-10 rounded-full {{ $admin->status === 'suspended' ? 'bg-error/20 text-error' : 'bg-white/10 text-white' }} flex items-center justify-center font-bold text-[14px]">
                                    {{ substr($admin->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-label-md text-white font-bold {{ $admin->status === 'suspended' ? 'line-through decoration-error' : '' }}">{{ $admin->name }}</div>
                                    <div class="text-[11px] text-white/40">{{ $admin->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 {{ $admin->status === 'suspended' ? 'opacity-50' : '' }}">
                            <span class="bg-white/10 text-white border border-white/20 text-[10px] font-bold px-2 py-0.5 rounded uppercase">Admin</span>
                        </td>
                        <td class="p-4">
                            @if($admin->status === 'suspended')
                                <span class="bg-error/20 text-error border border-error/30 px-2 py-0.5 rounded text-[10px] font-bold uppercase">Suspended</span>
                            @else
                                <span class="text-white text-[12px] font-semibold">Active</span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <form action="{{ route('super-admin.admins.toggle', $admin) }}" method="POST">
                                    @csrf
                                    @if($admin->status === 'suspended')
                                        <button type="submit" class="btn-magnetic bg-white/5 text-white border border-white/10 hover:bg-white/10 font-label-sm px-4 py-1.5 rounded-lg shadow-sm transition-colors">
                                            Restore Access
                                        </button>
                                    @else
                                        <button type="submit" class="btn-magnetic bg-error-container/20 text-error border border-error/30 hover:bg-error/30 font-label-sm px-3 py-1.5 rounded-lg shadow-sm transition-colors" title="Revoke Access">
                                            <span class="material-symbols-outlined text-[16px]">block</span>
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Admin Modal -->
    <div id="create-admin-modal" class="hidden fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-[#121416]/95 backdrop-blur-md border border-white/10 rounded-[2rem] p-8 w-full max-w-md shadow-[0_24px_64px_rgba(0,0,0,0.6)]">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-tertiary">person_add</span> Register New Admin
            </h3>
            <form action="{{ route('super-admin.admins.create') }}" method="POST">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-semibold text-white/60 uppercase tracking-wider mb-2">Full Name</label>
                        <input type="text" name="name" placeholder="Enter admin full name" required 
                               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder:text-white/20 outline-none focus:border-tertiary focus:ring-2 focus:ring-tertiary/20 transition-all font-body-sm shadow-inner">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-white/60 uppercase tracking-wider mb-2">Email Address</label>
                        <input type="email" name="email" placeholder="admin@fibretrace.in" required 
                               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder:text-white/20 outline-none focus:border-tertiary focus:ring-2 focus:ring-tertiary/20 transition-all font-body-sm shadow-inner">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-white/60 uppercase tracking-wider mb-2">Temporary Password</label>
                        <input type="password" name="password" placeholder="••••••••" required 
                               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder:text-white/20 outline-none focus:border-tertiary focus:ring-2 focus:ring-tertiary/20 transition-all font-body-sm shadow-inner">
                    </div>
                    <div class="flex justify-end gap-3 pt-6 border-t border-white/5">
                        <button type="button" onclick="document.getElementById('create-admin-modal').classList.add('hidden')" 
                                class="px-5 py-2.5 rounded-xl text-white/70 hover:text-white hover:bg-white/5 font-label-sm transition-all font-bold">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-tertiary hover:bg-tertiary/90 text-white font-label-sm px-6 py-2.5 rounded-xl shadow-lg transition-all font-bold">
                            Create Admin
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
