<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveAdministrator
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->is_active !== true) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'This administrator account is inactive.']);
        }

        return $next($request);
    }
}
