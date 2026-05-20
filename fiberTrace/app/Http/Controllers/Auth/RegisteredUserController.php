<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     * Captures all FibreTrace KYC fields: company_name, phone, gstin, city, state, role.
     * New accounts default to 'pending' status — they cannot access the platform until admin verifies GSTIN.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'company_name'     => ['required', 'string', 'max:255'],
            'email'            => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone'            => ['required', 'string', 'max:20'],
            'gstin'            => ['required', 'string', 'size:15', 'unique:users,gstin'],
            'role'             => ['required', 'in:seller,buyer'],
            'city'             => ['required', 'string', 'max:100'],
            'state'            => ['required', 'string', 'max:50'],
            'password'         => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'         => $request->name,
            'company_name' => $request->company_name,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'gstin'        => strtoupper($request->gstin),
            'role'         => $request->role,
            'city'         => $request->city,
            'state'        => $request->state,
            'status'       => 'pending', // All new users start as pending — requires admin GSTIN verification
            'password'     => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect to pending screen — user cannot access dashboard until verified
        return redirect()->route('pending');
    }
}
