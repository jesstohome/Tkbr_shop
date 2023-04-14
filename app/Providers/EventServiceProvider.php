<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
  /**
   * The event listener mappings for the application.
   *
   * @var array
   */
  protected $listen = [
    Registered::class => [
      SendEmailVerificationNotification::class,
    ],
  ];

  /**
   * Register any events for your application.
   *
   * @return void
   */
  public function boot()
  {
    parent::boot();

    // 打印SQL 日志
      \DB::listen(
          function ($db) {
              foreach ($db->bindings as $k => $binding) {
                  if ($binding instanceof \DateTime) {
                      $db->bindings[$k] = $binding->format('\'Y-m-d H:i:s\'');
                  } else {
                      if (is_string($binding)) {
                          $db->bindings[$k] = "'$binding'";
                      }
                  }
              }

              $query = str_replace(array('%', '?'), array('%%', '%s'), $db->sql);

              $query = vsprintf($query, $db->bindings);

              // 保存文件
              $logFile = fopen(
                  storage_path('logs' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_query.log'),
                  'a+'
              );
              fwrite($logFile, date('Y-m-d H:i:s') . "《《《\n" . 'time : ' . $db->time . "\n" . 'sql : ' . $query .
                  PHP_EOL . "》》》\n");
              fclose($logFile);
          }
      );
  }
}
