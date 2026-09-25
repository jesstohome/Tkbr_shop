<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
      Schema::defaultStringLength(191);
      Paginator::useBootstrap();

      if(config('app.env') === 'production') {
          \URL::forceScheme('https');
      }

      // 邮件总开关：仅放行商家注册邮箱验证码邮件、找回密码邮件和 SMTP 测试邮件，其余邮件一律不发送
      \Event::listen(\Illuminate\Mail\Events\MessageSending::class, function ($event) {
          $allowed_subjects = [get_email_verification_subject(), get_email_reset_subject(), 'SMTP Test'];
          return in_array($event->message->getSubject(), $allowed_subjects);
      });
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    //
  }
}
