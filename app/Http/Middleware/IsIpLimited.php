<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class IsIpLimited
{
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            $ip_whitelist = '';
            $user_type = auth()->user()->user_type;
            if ($user_type == 'staff') {
                if (auth()->user()->bloc && !empty(auth()->user()->bloc->ip_whitelist)) {
                    $ip_whitelist = auth()->user()->bloc->ip_whitelist;
                    $ip_whitelist = $this->formatIpWhitelist($ip_whitelist);
                }
            } elseif ($user_type == 'admin') {
                $backend_ip_whitelist = get_setting('backend_ip_whitelist');
                $ip_whitelist = $this->formatIpWhitelist($backend_ip_whitelist);
            }

            if (!empty($ip_whitelist)) {
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

    private function formatIpWhitelist($ip_whitelist) {
        if (empty($ip_whitelist)) return [];

        $ip_whitelist = str_replace(["\r\n", "\n\r", "\r", "\n"], ";", $ip_whitelist);
        $ip_whitelist = str_replace(";;", ";", $ip_whitelist);
        $ip_whitelist = explode(";", $ip_whitelist);
        $ip_whitelist = array_map(function ($val) {
            return trim($val);
        }, $ip_whitelist);

        return $ip_whitelist;
    }
}
