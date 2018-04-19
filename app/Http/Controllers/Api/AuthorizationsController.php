<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationRequest;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;


class AuthorizationsController extends Controller
{
    // 第三方平台登陆 仅限微信
    public function socialStore(SocialAuthorizationRequest $request, $type)
    {
        if(!in_array($type, ['weixin'])){
            return $this->response->errorBadRequest();
        }
        // 实例化微信登陆类
        $driver  = Socialite::driver($type);

        try{
            // 通过code获取accesstoken
            if($code = $request->code){
                $response = $driver->getAccessTokenResponse($code);

                $token = array_get($response, 'access_token');
            } else {
            // 将传入的token和openid绑定
                $token = $request->access_token;
                if($type == 'weixin'){
                    $driver->setOpenId($request->openid);
                }
            }
            // 通过token获取用户信息
            $oauthUser = $driver->userFromToken($token);

        }catch(Exception $e){
            return $this->response->errorUnauthorized('参数错误，未获取用户信息');
        }

        switch ($type) {
            case 'weixin':

                // 判断是否有unionid
                $unionid = $oauthUser->offsetExists('unionid') ? $oauthUser->offsetGet('unionid') : null;

                // unionid 在数据库里面查找 user
                if($unionid){
                    $user = User::where('weixin_unionid', $unionid)->first();
                } else {
                    // 通过openid查找user
                    $user = User::where('weixin_openid' ,$oauthUser->getId())->first();
                }

                // 如果不存在就插入用户
                if(!$user){
                    $user = User::create([
                        'name' => $oauthUser->getNickname(),
                        'avatar' => $oauthUser->getAvatar(),
                        'weixin_openid' => $oauthUser->getId(),
                        'weixin_unionid' => $unionid,
                    ]);
                }
                break;
        }

        // 使用jwt生成token
        $token = Auth::guard('api')->fromUser($user);
        // 返回token和其他信息
        return $this->respondWithToken($token)->setStatusCode(201);
    }


    // 账号密码登陆
    public function store(AuthorizationRequest $request)
    {
        $username = $request->username;

        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $username:
            $credentials['phone'] = $username;

        $credentials['password'] = $request->password;

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return $this->response->errorUnauthorized('用户名或密码错误');
        }
        return $this->respondWithToken($token)->setStatusCode(201);
    }

    // 封装返回方法
    protected function respondWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }


    /*
     * 需要在请求的herader里面加上 Authorization： Bearer { token }
     */


    //刷新token
    public function update()
    {
        $token = Auth::guard('api')->refresh();
        return $this->respondWithToken($token);
    }

    // 删除token
    public function destroy()
    {
        Auth::guard('api')->logout();
        return $this->response->noContent();
    }
}
