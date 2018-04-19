<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ChuanglanSmsApi;
use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class VerificationCodesController extends Controller
{
    /**
     * @param array $middleware
     */
    public function store(VerificationCodeRequest $request, ChuanglanSmsApi $clapi)
    {
        $captchaDate = Cache::get($request->captcha_key);

        if(!$captchaDate){
            return $this->response->error('图片验证码已失效', 422);

        }

        if(!hash_equals($captchaDate['code'],$request->captcha_code)){
            Cache::forget($request->captcha_key);
            return $this->response->errorUnauthorized('验证码错误');
        }

        $phone = $captchaDate['phone'];
        $code  = '1234';

        if(config('services.is_send_true_smg')){
            $code = str_pad(rand(1,9999), 4, 0, STR_PAD_LEFT);
            $res = $clapi->execResult( $clapi->sendSMS($phone, "【户动】验证码是: ". $code ."，请在5分钟内完成验证", 'true') );

            if(!isset($res[1]) || $res[1] != 0){
                return json_encode(['msg'=>'短信发送异常']);
            }
        }

        $key = 'verificationCode_'. str_random(15);
        $expiredAt = now()->addMinute(10);

        // 短信验证码写入 redis
        Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);
        // 清除图片验证码
        Cache::forget($request->captcha_key);

        return $this->response->array([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
}
