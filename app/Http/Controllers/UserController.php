<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Handlers\ImageUploadHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => [ 'show' ]]);
    }

    //用户个人中心
    public function show(User $user)
    {
        $topics = $user->topics()->orderBy('id','desc')->paginate(5);
        return view('users.show', compact('user', 'topics'));
    }

    //显示更新资料界面
    public function edit(User $user)
    {
        $this->authorize('edit', $user);
        return view('users.edit', compact('user'));
    }

    //执行更新资料
    public function update(UserRequest $request, User $user)
    {
        $uploader = new ImageUploadHandler;

        $this->authorize('edit', $user);        //   App\Policies\UserPolicy 是否为当前登录用户
        $data = $request->all();                //  自动注入 Request\UserRequest 里面定义规则

        if($request->avatar) {
            $res = $uploader->save($request->avatar, 'avatar', $user->id);
            if($res){
                $data['avatar'] = $res['path'];
            }
        }
        $user->update($data);
        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功');
    }

    //用户通知
    public function notifications()
    {
        $notifications = DB::table('notifications')->where('notifiable_id', Auth::id())->paginate(15);
        if($notifications){
            foreach ( $notifications as $notification) {
                $notification->data = json_decode($notification->data, true);
            }
        }
        //归零通知总数
        Auth::user()->clearNotification();
        return view('users.notifications', compact('notifications'));
    }

    //后台访问拒绝

    /*
    public function permissionDenied()
    {

        // 如果当前用户有权限访问后台，直接跳转访问
        if (config('administrator.permission')()) {
            return redirect(url(config('administrator.uri')), 302);
        }
        // 否则使用视图
        return view('users.permission_denied');
    }
    */

}
