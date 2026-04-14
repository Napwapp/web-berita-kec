<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed ...$guards
     */
    public function handle($request, Closure $next, ...$guards)
    {
        foreach ($guards ?: [null] as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                if ($user->role === 'admin') {
                    return redirect('/dashboard');
                }
                return redirect('/'); // user biasa
            }
        }

        return $next($request);
    }
}
