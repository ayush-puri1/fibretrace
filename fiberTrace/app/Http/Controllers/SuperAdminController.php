<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lot;
use App\Models\Transaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class SuperAdminController extends Controller
{
    /** Super-admin dashboard with platform-wide health metrics */
    public function dashboard()
    {
        $stats = [
            'total_users'       => User::whereIn('role', ['seller', 'buyer'])->count(),
            'total_admins'      => User::where('role', 'admin')->count(),
            'total_lots'        => Lot::count(),
            'active_auctions'   => Lot::where('status', 'active')->count(),
            'total_volume'      => Transaction::whereIn('payment_status', ['paid', 'released'])->sum('total_amount'),
            'pending_users'     => User::where('status', 'pending')->count(),
            'flagged_lots'      => Lot::where('flagged', true)->count(),
        ];

        $recentActivity = ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('super-admin.dashboard', compact('stats', 'recentActivity'));
    }

    /** List all admin accounts */
    public function admins()
    {
        $admins = User::where('role', 'admin')
            ->orderByDesc('created_at')
            ->get();

        return view('super-admin.admins', compact('admins'));
    }

    /** Create a new admin account */
    public function createAdmin(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $admin = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'admin',
            'status'   => 'verified', // Admins are auto-verified
        ]);

        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'admin_created',
            'subject_type' => User::class,
            'subject_id'   => $admin->id,
            'description'  => "Created admin account for {$admin->name} ({$admin->email})",
            'ip_address'   => $request->ip(),
        ]);

        return redirect()->route('super-admin.admins')
            ->with('success', "Admin account for {$admin->name} created.");
    }

    /** Toggle admin account active/suspended */
    public function toggleAdmin(Request $request, User $user)
    {
        abort_if($user->role !== 'admin', 422, 'User is not an admin.');

        $newStatus = $user->status === 'verified' ? 'suspended' : 'verified';
        $user->update(['status' => $newStatus]);

        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'admin_toggled',
            'subject_type' => User::class,
            'subject_id'   => $user->id,
            'description'  => "Admin {$user->name} status changed to {$newStatus}",
            'ip_address'   => $request->ip(),
        ]);

        return back()->with('success', "Admin account {$newStatus}.");
    }
}
