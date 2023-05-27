<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            if (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff') {
                \CoreComponentRepository::instantiateShopRepository();
                return redirect()->route('admin.dashboard');
            } elseif (auth()->user()->user_type == 'seller') {
                return redirect()->route('seller.dashboard');
            }

            return redirect('/home');
        }

        return $next($request);
    }
}
