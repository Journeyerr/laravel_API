<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UsersController extends Controller
{
    public function store(UserRequest $request)
    {
        $verifyData = Cache::get($request->verification_key);
        
        if(!$verifyData){
            return $this->response->error('验证码失效', 422);
        }

        // hash_equals 第一位开始逐一进行比较的，发现不同就立即返回 false
        if(!hash_equals($verifyData['code'], $request->verification_code)){
            // 返回401
            return $this->response->errorUnauthorized('验证码错误');
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $verifyData['phone'],
            'password' => bcrypt($request->password)
        ]);

        if($user){
            Cache::forget($request->verification_key);
            return $this->response->created();
        } else {
            return $this->response->errorUnauthorized('异常');
        }

    }
}
