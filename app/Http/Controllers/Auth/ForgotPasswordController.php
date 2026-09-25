<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use App\Mail\SecondEmailVerifyMailManager;
use App\Utility\SmsUtility;
use Cache;
use Mail;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $request->email)->first();
            if ($user != null) {
                $user->verification_code = rand(100000,999999);
                $user->save();

                $this->sendResetCodeEmail($user);

                return view('auth.passwords.reset');
            }
            else {
                flash(translate('No account exists with this email'))->error();
                return back();
            }
        }
        else{
            $user = User::where('phone', $request->email)->first();
            if ($user != null) {
                $user->verification_code = rand(100000,999999);
                $user->save();
                SmsUtility::password_reset($user);
                return view('otp_systems.frontend.auth.passwords.reset_with_phone');
            }
            else {
                flash(translate('No account exists with this phone number'))->error();
                return back();
            }
        }
    }

    /**
     * 找回密码合并页：邮箱(带发送验证码按钮) + 验证码 + 新密码
     */
    public function showResetPage(Request $request)
    {
        return view('auth.passwords.reset');
    }

    /**
     * 重置密码页：AJAX 发送邮箱验证码（先校验邮箱是否已注册）
     */
    public function sendResetCode(Request $request)
    {
        $email = trim($request->email ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['success' => 0, 'msg' => translate('Please enter a valid email address')]);
        }
        $user = User::where('email', $email)->first();
        if ($user == null) {
            return response()->json(['success' => 0, 'msg' => translate('No account exists with this email')]);
        }
        if (Cache::has('password_reset_code_cooldown:' . $email)) {
            return response()->json(['success' => 0, 'msg' => translate('Please wait 60 seconds before resending')]);
        }

        $user->verification_code = rand(100000,999999);
        $user->save();
        Cache::put('password_reset_code_cooldown:' . $email, 1, 60);

        try {
            $this->sendResetCodeEmail($user);
        } catch (\Throwable $ex) {
            Cache::forget('password_reset_code_cooldown:' . $email);
            return response()->json(['success' => 0, 'msg' => translate('Email sending failed, please check SMTP settings')]);
        }

        return response()->json(['success' => 1, 'msg' => translate('Verification code sent successfully')]);
    }

    /**
     * 组装并发送重置密码验证码邮件
     */
    protected function sendResetCodeEmail($user)
    {
        $subject = get_email_reset_subject();
        $content = get_setting('email_reset_content');
        if (empty($content)) $content = translate('You are applying to reset your password, please enter the verification code below to set a new password.');
        // 验证码由模板大号展示，正文中不再内嵌；{name} 替换为用户姓名
        $content = str_replace(['{code}', '{name}'], ['', $user->name], $content);
        $content = preg_replace('/\n{3,}/', "\n\n", $content);

        $array['view'] = 'emails.reset_password';
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['subject'] = $subject;
        $array['content'] = $content;
        $array['code'] = $user->verification_code;
        $array['footer'] = (string) get_setting('email_reset_footer');

        Mail::to($user->email)->queue(new SecondEmailVerifyMailManager($array));
    }
}
