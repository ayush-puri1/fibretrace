<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use App\Mail\UserVerifiedMail;
use App\Mail\UserRejectedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller
{
    /** List all users pending GSTIN verification */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $users = User::whereIn('role', ['seller', 'buyer'])
            ->where('status', $status)
            ->orderByDesc('created_at')
            ->paginate(20);

        $counts = [
            'pending'   => User::whereIn('role', ['seller', 'buyer'])->where('status', 'pending')->count(),
            'verified'  => User::whereIn('role', ['seller', 'buyer'])->where('status', 'verified')->count(),
            'rejected'  => User::whereIn('role', ['seller', 'buyer'])->where('status', 'rejected')->count(),
            'suspended' => User::whereIn('role', ['seller', 'buyer'])->where('status', 'suspended')->count(),
        ];

        return view('admin.verifications', compact('users', 'counts', 'status'));
    }

    /** Approve a user's GSTIN — grants access to the platform */
    public function approve(Request $request, User $user)
    {
        $user->update([
            'status'      => 'verified',
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'user_verified',
            'subject_type' => User::class,
            'subject_id'   => $user->id,
            'description'  => "Approved GSTIN for {$user->company_name} ({$user->gstin})",
            'ip_address'   => $request->ip(),
        ]);

        // Queue verification approval email — non-blocking, sent via database queue
        Mail::to($user->email)->queue(new UserVerifiedMail($user));

        return back()->with('success', "{$user->company_name} has been verified and granted platform access.");
    }

    /** Reject a user's GSTIN — blocks platform access */
    public function reject(Request $request, User $user)
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $user->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'user_rejected',
            'subject_type' => User::class,
            'subject_id'   => $user->id,
            'description'  => "Rejected GSTIN for {$user->company_name}. Reason: {$request->reason}",
            'ip_address'   => $request->ip(),
        ]);

        // Queue rejection email with the admin's reason
        Mail::to($user->email)->queue(new UserRejectedMail($user, $request->reason));

        return back()->with('success', "{$user->company_name}'s registration has been rejected.");
    }
}
