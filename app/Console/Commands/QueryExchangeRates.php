<?php


namespace App\Console\Commands;


use Illuminate\Console\Command;

class QueryExchangeRates extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queryExchangeRates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '查询当天的实时汇率';

    public function handle() {
        queryExchangeRates();
    }
}
