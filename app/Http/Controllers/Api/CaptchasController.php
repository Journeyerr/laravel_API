<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CaptchaRequest;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Facades\Cache;

class CaptchasController extends Controller
{
    public function store(CaptchaRequest $request, CaptchaBuilder $captchaBuilder)
    {
        $key = 'captcha-'.str_random(15);
        $phone = $request->phone;

        $captcha = $captchaBuilder->build();    // build 方法，创建出来验证码图片
        $expiredAt = now()->addMinute(2);

        Cache::put($key, ['phone'=>$phone, 'code'=>$captcha->getPhrase()], $expiredAt); //getPhrase 方法获取验证码文本

        $result = [
            'captcha_key' => $key,
            'expired_at' => $expiredAt->toDateString(),
            'captcha_image_content' => $captcha->inline()   //inline 方法获取的 base64 图片验证码
        ];

        return $this->response->array($result)->setStatusCode(201);
    }
}
