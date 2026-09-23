<?php

/** @noinspection PhpUndefinedClassInspection */

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\OTPVerificationController;
use App\Models\BusinessSetting;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Notifications\AppEmailVerificationNotification;
use Hash;
use Socialite;
use function dd;


class AuthController extends Controller
{
    public function signup(Request $request)
    {
        if (User::where('email', $request->email_or_phone)->orWhere('phone', $request->email_or_phone)->first() != null) {
            return response()->json([
                'result' => false,
                'message' => translate('User already exists.'),
                'user_id' => 0
            ], 201);
        }

        if ($request->register_by == 'email') {
            $user = new User([
                'name' => $request->name,
                'email' => $request->email_or_phone,
                'password' => bcrypt($request->password),
                'verification_code' => rand(100000, 999999)
            ]);
        } else {
            $user = new User([
                'name' => $request->name,
                'phone' => $request->email_or_phone,
                'password' => bcrypt($request->password),
                'verification_code' => rand(100000, 999999)
            ]);
        }

        $user->email_verified_at = null;
        // 邮箱注册直接置为已验证，不再受邮箱验证开关影响；手机注册维持原有 OTP 逻辑
        if ($request->register_by == 'email' || BusinessSetting::where('type', 'email_verification')->first()->value != 1) {
            $user->email_verified_at = date('Y-m-d H:m:s');
        }

        if($user->email_verified_at == null){
            if ($request->register_by == 'email') {
                try {
                    $user->notify(new AppEmailVerificationNotification());
                } catch (\Exception $e) {
                }
            } else {
                $otpController = new OTPVerificationController();
                $otpController->send_code($user);
            }
        }

        $user->save();

        //create token
        $user->createToken('tokens')->plainTextToken;

        return response()->json([
            'result' => true,
            'message' => translate('Registration Successful. Please verify and log in to your account.'),
            'user_id' => $user->id
        ], 201);
    }

    public function resendCode(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        $user->verification_code = rand(100000, 999999);

        if ($request->verify_by == 'email') {
            $user->notify(new AppEmailVerificationNotification());
        } else {
            $otpController = new OTPVerificationController();
            $otpController->send_code($user);
        }

        $user->save();

        return response()->json([
            'result' => true,
            'message' => translate('Verification code is sent again'),
        ], 200);
    }

    public function confirmCode(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();

        if ($user->verification_code == $request->verification_code) {
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->verification_code = null;
            $user->save();
            return response()->json([
                'result' => true,
                'message' => translate('Your account is now verified.Please login'),
            ], 200);
        } else {
            return response()->json([
                'result' => false,
                'message' => translate('Code does not match, you can request for resending the code'),
            ], 200);
        }
    }
    
  

    public function login(Request $request)
    {
        
        /*$request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);*/

        $delivery_boy_condition = $request->has('user_type') && $request->user_type == 'delivery_boy';

        if ($delivery_boy_condition) {
            $user = User::whereIn('user_type', ['delivery_boy'])->where('email', $request->email)->orWhere('phone', $request->email)->first();
        } else {
            $user = User::whereIn('user_type', ['customer', 'seller'])->where('email', $request->email)->orWhere('phone', $request->email)->first();
        }

        if (!$delivery_boy_condition) {
            if (\App\Utility\PayhereUtility::create_wallet_reference($request->identity_matrix) == false) {
                return response()->json(['result' => false, 'message' => 'Identity matrix error', 'user' => null], 401);
            }
        }


        if ($user != null) {
            if (Hash::check($request->password, $user->password)) {

                if ($user->email_verified_at == null) {
                    return response()->json(['result' => false, 'message' => translate('Please verify your account'), 'user' => null], 401);
                }
                return $this->loginSuccess($user);
            } else {
                return response()->json(['result' => false, 'message' => translate('Unauthorized'), 'user' => null], 401);
            }
        } else {
            return response()->json(['result' => false, 'message' => translate('User not found'), 'user' => null], 401);
        }
    }
    
    /**
     * 定时记录未按时提货的订单
     */
    public function timedquery(){
        each(111111111111111);
        $orders = Order::orderBy('id', 'desc');

        $orders = $orders->where('product_storehouse_status = 0');//未支付
        //循环扣信用分
        foreach ($order as $orders) {
            $creditscorestream = CreditscoreStream::where('order_id','=',$order["id"] )->first();
            if ($creditscorestream == null)
            {
                $users = User::where('id','=',$order['seller_id'])->first();
                $settimeone = strtotime($order['created_at'].'+6 hours');
                if ($order['order_type'] == 6 && date('Y-m-d h:i:s',$settime) < date('Y-m-d H:i:s'))
                {
                    //增加明细
                    $creditscore_stream = new CreditscoreStream;
                    $creditscore_stream['order_id'] = $order['id'];
                    $creditscore_stream['seller_id'] = $order['seller_id'];
                    $creditscore_stream['creditscore_before'] = $users['creditscore'];
                    $creditscore_stream['creditscore_after'] = $users['creditscore'] - 2;
                    $creditscore_stream['remark'] = '';
                    $creditscore_stream->save();
                }
                $settimetwo = strtotime($order['created_at'].'+24 hours');
                if ($order['order_type'] == 24 && date('Y-m-d h:i:s',$settimetwo) < date('Y-m-d H:i:s'))
                {
                    //增加明细
                    $creditscore_stream = new CreditscoreStream;
                    $creditscore_stream['order_id'] = $order['id'];
                    $creditscore_stream['seller_id'] = $order['seller_id'];
                    $creditscore_stream['creditscore_before'] = $users['creditscore'];
                    $creditscore_stream['creditscore_after'] = $users['creditscore'] - 2;
                    $creditscore_stream['remark'] = '';
                    $creditscore_stream->save();
                }
                $settimethree = strtotime($order['created_at'].'+24 hours');
                if ($order['order_type'] == 24 && date('Y-m-d h:i:s',$settimethree) < date('Y-m-d H:i:s'))
                {
                    //增加明细
                    $creditscore_stream = new CreditscoreStream;
                    $creditscore_stream['order_id'] = $order['id'];
                    $creditscore_stream['seller_id'] = $order['seller_id'];
                    $creditscore_stream['creditscore_before'] = $users['creditscore'];
                    $creditscore_stream['creditscore_after'] = $users['creditscore'] - 2;
                    $creditscore_stream['remark'] = '';
                    $creditscore_stream->save();
                }

                $users['creditscore'] = $users['creditscore'] - 2;
                $users -> save();

            }
        }
        return response()->json([
            'result' => true,
            'message' => '执行成功'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'result' => true,
            'message' => translate('Successfully logged out')
        ]);
    }

    public function socialLogin(Request $request)
    {
        if (!$request->provider) {
            return response()->json([
                'result' => false,
                'message' => translate('User not found'),
                'user' => null
            ]);
        }

        //
        switch ($request->social_provider) {
            case 'facebook':
                $social_user = Socialite::driver('facebook')->fields([
                    'name',
                    'first_name',
                    'last_name',
                    'email'
                ]);
                break;
            case 'google':
                $social_user = Socialite::driver('google')
                    ->scopes(['profile', 'email']);
                break;
            default:
                $social_user = null;
        }
        if ($social_user == null) {
            return response()->json(['result' => false, 'message' => translate('No social provider matches'), 'user' => null]);
        }

        $social_user_details = $social_user->userFromToken($request->access_token);

        if ($social_user_details == null) {
            return response()->json(['result' => false, 'message' => translate('No social account matches'), 'user' => null]);
        }

        //

        $existingUserByProviderId = User::where('provider_id', $request->provider)->first();

        if ($existingUserByProviderId) {
            return $this->loginSuccess($existingUserByProviderId);
        } else {
            $user = new User([
                'name' => $request->name,
                'email' => $request->email,
                'provider_id' => $request->provider,
                'email_verified_at' => Carbon::now()
            ]);
            $user->save();
        }
        return $this->loginSuccess($user);
    }

    protected function loginSuccess($user)
    {
        $token = $user->createToken('API Token')->plainTextToken;
        return response()->json([
            'result' => true,
            'message' => translate('Successfully logged in'),
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => null,
            'user' => [
                'id' => $user->id,
                'type' => $user->user_type,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'avatar_original' => uploaded_asset($user->avatar_original),
                'phone' => $user->phone
            ]
        ]);
    }
}
