<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->status === 'pending') {
            return redirect('/pending');
        }

        if (in_array($user->status, ['suspended', 'rejected'])) {
            abort(403, 'Your account has been suspended or your GSTIN verification was rejected.');
        }

        return $next($request);
    }
}
