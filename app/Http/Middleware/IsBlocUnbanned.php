<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class IsBlocUnbanned
{
    public function handle($request, Closure $next)
    {
        if (auth()->check() && !auth()->user()->bloc->status) {

            $redirect_to = "";
            if(auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff'){
                $redirect_to = "login";
            }else{
                $redirect_to = "user.login";
            }

            auth()->logout();



            $message = translate("Your Bloc are banned");
            flash($message, 'warning');

            return redirect()->route($redirect_to);


        }

        return $next($request);
    }
}
