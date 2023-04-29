<?php

ini_set('serialize_precision', -1);

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/
$start_time = microtime(true);
if (function_exists("xhprof_enable") ){
    xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY + XHPROF_FLAGS_NO_BUILTINS);
}
require __DIR__.'/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__.'/bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

function xhprof_log($start_time) {
    echo 'Script executed with success', PHP_EOL;

    Log::log('debug', var_export(['xhprof_enable' => function_exists("xhprof_enable"), $request->path(), 'Script executed with success'], true));

    if (function_exists("xhprof_enable") ) {
        $xhprof_data = xhprof_disable();

        include_once $XHPROF_ROOT . "/public/xhprof/xhprof_lib/utils/xhprof_lib.php";
        include_once $XHPROF_ROOT . "/public/xhprof/xhprof_lib/utils/xhprof_runs.php";

        // save raw data for this profiler run using default
        // implementation of iXHProfRuns.
        $xhprof_runs = new XHProfRuns_Default();
        $end_time = microtime(true);
        $cost_time = $end_time - $start_time ;
        // save the run under a namespace "xhprof_foo"
        $route = str_replace('/',"_",$request->path());
        if($cost_time > 3 ){
            $xhprof_runs->save_run($xhprof_data, "admin_".$route);
        }

    }
}

register_shutdown_function('xhprof_log', $start_time);

$response->send();

$kernel->terminate($request, $response);

