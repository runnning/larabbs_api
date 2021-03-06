<?php

namespace App\Observers;

use App\Handlers\SlugTranslateHandler;
use App\Jobs\TransLateSlug;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{

    public function saving(Topic $topic)
    {
        //xss过滤
        $topic->body=clean($topic->body,'user_topic_body');
        //生成话题摘要
        $topic->excerpt= make_excerpt($topic->body);


    }
    public function saved(Topic $topic){
        //如果slug字段无内容,即使使用翻译器对title进行翻译
        if(!$topic->slug){
            //推送任务到队列
            dispatch(new TransLateSlug($topic));
        }
    }

    public function deleted(Topic $topic){
        DB::table('replies')->where('topic_id', $topic->id)->delete();
    }
}
