<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class IsIpLimited
{
    public function handle($request, Closure $next)
    {
        if (auth()->check() && auth()->user()->bloc && !empty(auth()->user()->bloc->ip_whitelist)) {
            if (auth()->user()->user_type == 'staff') {
                $ip_whitelist = auth()->user()->bloc->ip_whitelist;
                $ip_whitelist = str_replace(["\r\n", "\n\r", "\r", "\n"], ";", $ip_whitelist);
                $ip_whitelist = str_replace(";;", ";", $ip_whitelist);
                $ip_whitelist = explode(";", $ip_whitelist);
                $ip_whitelist = array_map(function ($val) {
                    return trim($val);
                }, $ip_whitelist);
                $ip = get_ip();
                if (!in_array($ip, $ip_whitelist)) {
                    auth()->logout();

                    $message = translate("Your Ip is limited");
                    flash($message, 'warning');

                    return redirect()->route('login');
                }
            }
        }

        return $next($request);
    }
}
