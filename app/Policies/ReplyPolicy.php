<?php

namespace App\Policies;

use App\Models\Topic;
use App\Models\User;
use App\Models\Reply;

class ReplyPolicy extends Policy
{
    // 检测回复删除的时候，是否有权限
    public function destroy(User $user, Reply $reply)
    {
        $topic = Topic::find($reply->topic_id);
        // 话题的作者和评论的作者，才有权限删除评论
        return $user->id == $reply->user_id || $user->id == $topic->user_id;
    }
}
