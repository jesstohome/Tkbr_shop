<?php

namespace App\Http\Middleware;

use Closure;

class IsPasswordChanged
{
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            $cache_key = 'password_changed:' . auth()->user()->id;
            if (!empty(\Cache::get($cache_key))) {
                \Cache::delete($cache_key);

                if(auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff'){
                    $redirect_to = "login";
                }else{
                    $redirect_to = "user.login";
                }

                auth()->logout();

                $message = translate("You need to log in again");
                flash($message);

                return redirect()->route($redirect_to);
            }
        }

        return $next($request);
    }
}
