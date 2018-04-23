<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ReplyRequest;
use App\Models\Reply;
use App\Models\Topic;
use App\Transformers\ReplyTransformer;

class RepliesController extends Controller
{
    // 回复列表
    public function index(Topic $topic)
    {
        $topics = $topic->replies()->paginate(3);
        return $this->response->paginator($topics, new ReplyTransformer());

    }

    // 创建回复
    public function store(ReplyRequest $request, Topic $topic, Reply $reply)
    {
        $reply->content = $request->contents;
        $reply->topic_id = $topic->id;
        $reply->user_id = $this->user()->id;
        $reply->save();

        return $this->response->item($reply, new ReplyTransformer())->setStatusCode(201);
    }

    // 删除回复
    public function destroy(Topic $topic, Reply $reply)
    {
        // 当前话题的id 不是回复的id
        if($topic->id != $reply->topic_id){
            return $this->response->errorBadRequest();
        }

        // 如果当前用户不是话题作者也不是回复者 403
        if($this->user()->id != $reply->user_id && $this->user()->id != $topic->user_id){
            return $this->response->errorForbidden();
        }

        // 若不使用上面的判断， 可选 授权策略 类，
//        $this->authorize('destroy', $reply);

        $reply->delete();
        return $this->response->noContent();
    }
}
