<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PendingController extends Controller
{
    /**
     * Show the "waiting for verification" screen.
     * Only accessible to authenticated users (any status).
     * If already verified, redirect to dashboard.
     */
    public function show()
    {
        $user = auth()->user();

        // If somehow a verified user lands here, send them to dashboard
        if ($user->status === 'verified') {
            return redirect()->route('dashboard');
        }

        return view('auth.pending', compact('user'));
    }
}
