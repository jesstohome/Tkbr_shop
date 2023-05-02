<?php

namespace App\Console;

use App\Mail\EmailManager;
use App\Models\EmailTask;
use App\Models\Order;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
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
