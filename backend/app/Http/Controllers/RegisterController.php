<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\UserActivation;
use Carbon\Carbon;
use Mail;

class RegisterController extends BaseController
{
    // ユーザー認証コード発行 -> 入力画面
    public function verify(Request $request)
    {
        // Emailのvalidation
        Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
        ])->validate();

        // メールアドレスをSessionから復元
        $email = $request->get('email');

        // OKなら残りの値を取得
        $verification_code = $request->get('code');
        $carbon_now =  new Carbon('now');

        //--------------------------------------------------------------------------------
        // 認証コード未入力
        if (!isset($verification_code))
        {
            // すでに同じメールアドレスで認証コード生成済みの場合はそれを再送
            $user_activation = UserActivation::where('email', $email)
                ->where('expired', '>', $carbon_now)
                ->where('activated_at', '==', null)
                ->first();
            
            // 存在しないなら認証コード生成
            if ($user_activation == null)
            {
                $user_activation = UserActivation::create([
                    'email'             => $email,
                    'verification_code' => UserActivation::GenerateActivationCode(),
                    'expired'           => $carbon_now->copy()->addHour(UserActivation::EXPIRE_HOUR),
                ]);
            }

            // 認証コードをメールで送る
            $data = $user_activation->toArray();
            $data['hour'] = UserActivation::EXPIRE_HOUR;
            Mail::send('emails.user_activation', $data, function($message) use ($email) {
                $message->to($email, 'Test')->subject('This is a test mail ');
            });

            // Sessionにメールアドレス保持
            $request->session()->put('activate_email', $email);
            
            return view('user_verify/index', compact('email'));
        }

        //--------------------------------------------------------------------------------
        // 認証コードが入力された
        if (isset($verification_code))
        {
            $user_activation = UserActivation::where('email', $email)
                ->where('expired', '>', $carbon_now)
                ->where('verification_code', $verification_code)
                ->first();
            
            // 認証コードがあってたら認証済みマーク
            if ($user_activation != null)
            {
                $user_activation->activated_at = $carbon_now;
                $user_activation->save();
            }

            return view('auth/register', compact('email'));
        }
    }

    public function index()
    {
        return view('user_verify/index');
    }
}
