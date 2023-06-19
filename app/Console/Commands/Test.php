<?php


namespace App\Console\Commands;


use Illuminate\Console\Command;

class Test extends Command
{
    protected $signature = 'test';

    public function handle() {
        echo date("Y-m-d H:i:s", \Illuminate\Support\Carbon::now()->addDays(1)->timestamp) . PHP_EOL;
        $now = time();
        echo 'now=' . date("Y-m-d H:i:s", $now) . PHP_EOL;
        $express_time = json_decode('["2023:06:16 01:14:31","2023:06:16 02:17:41","2023:06:17 04:12:27","2023:06:18 07:04:47","2023:06:19 08:47:06","2023:06:19 14:00:05","2023:06:19 15:15:32"]', true);
        foreach ($express_time as $key => $time) {
            echo $key, ':', $time . PHP_EOL;
            $time = strtotime(is_array($time) ? $time[0] : $time);
            echo $key, ':', $time, '=====',  date("Y-m-d H:i:s", $time). PHP_EOL;
        }
    }

}
