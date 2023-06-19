<?php


namespace App\Console\Commands;


use Illuminate\Console\Command;

class Test extends Command
{
    protected $signature = 'test';

    public function handle() {
        echo \Illuminate\Support\Carbon::now()->addDays(1)->timestamp;
    }

}
