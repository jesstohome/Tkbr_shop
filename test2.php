<?php
$start_time = microtime(true);
echo 'abc';

function xhprof_log($start_time) {
    echo $start_time . ':Script executed with success', PHP_EOL;


}

register_shutdown_function('xhprof_log', $start_time);
