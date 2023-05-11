<?php

namespace App\Console;

use App\Mail\EmailManager;
use App\Models\EmailTask;
use App\Models\Order;
use App\Models\Shop;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $timestamp = now()->timestamp;
            $ok1 = Order::query()->whereNotNull('freeze_expired_at')
                ->where('freeze_expired_at', '<=', $timestamp)
                ->where('delivery_status', 'delivered')
                ->where('product_storehouse_total', '>', 0)
                ->chunk(100, function ($orders) {
                    foreach ($orders as $order) {
                        \product_storehouse_order_free_up($order->id);
                    }
                });
            /*$ok2 = Order::query()->whereNotNull('order_type')
                ->chunk(100, function ($orders) {
                    foreach ($orders as $order) {
                        \timedquery($order->id);
                    }
                });*/

            // 按物流时间，定时更新发货状态
            $ok3 = Order::query()->where('product_storehouse_status', 1)
                ->whereNotIn('delivery_status', ['delivered', 'cancelled'])
                ->chunk(100, function ($orders) {
                    \Log::debug('按物流时间，定时更新发货状态 orders num is ' . count($orders));
                    foreach ($orders as $order) {
                        \scheduled_update_delivery_status($order);
                    }
                });

            // 定制发送邮件
            EmailTask::query()->where('status', 0)->chunk(100, function ($tasks) {
                \Log::debug('定时发送邮件 ' . count($tasks));
                foreach ($tasks as $task) {
                    try {
                        $array = json_decode($task['array'], true);
                        \Mail::to($task->email)->queue(new EmailManager($array));

                        $task->status = 1;
                        $task->save();
                    } catch (\Exception $e) {
                        Log::debug(var_export(['email_task_id' => $task->id, $e->getMessage()], true));
                        $task->status = 2;
                        $task->save();
                    }
                }
            });

            // 定时增加访问量
            Shop::query()->where('verification_status', 1)->chunk(500, function ($shops) {
                \Log::debug('定时增加访问量 ' . count($shops));
                foreach ($shops as $shop) {
                    try {
                        $cache_key = sprintf('shop:add_views:%s', $shop->id);
                        if (empty(\Cache::get($cache_key))) {
                            $shop->views += 1;
                            $shop->save();

                            $min = 900;
                            $max = 1800;
                            if (!empty($shop->view_rand_range)) {
                                $range = explode("-", $shop->view_rand_range);
                                if (count($range) > 1) {
                                    $min = min($range);
                                    $max = max($range);
                                }
                            }

                            $ttl = rand($min, $max);
                            if ($ttl > 0) {
                                \Cache::set($cache_key, 1, $ttl);
                            }
                        }

                    } catch (\Exception $exception) {
                        \Log::debug('定时增加访问量 ' . $e->getMessage);
                    }
                }
            });

            // 触发翻译
            if (\Redis::get('trigger_translate')) {
                echo '触发翻译' . PHP_EOL;
                \Redis::del('trigger_translate');
                Artisan::call("translate:run");
            }
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
