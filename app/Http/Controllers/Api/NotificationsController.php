<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Transformers\NotificationTransformer;

class NotificationsController extends Controller
{
    // 消息列表
    public function index()
    {
        //用户模型的 notifications 方法是 Laravel 的消息通知系统 为我们提供的方法，按通知创建时间倒叙排序。
        $notifications = $this->user()->notifications()->paginate(1);
        return $this->response->paginator($notifications, new NotificationTransformer());
    }

    // 未读消息
    public function stats()
    {
        return $this->response->array([
            'unread_count' => $this->user()->notification_count
        ]);
    }
}
