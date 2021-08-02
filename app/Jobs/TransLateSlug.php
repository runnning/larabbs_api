<?php

namespace App\Jobs;

use App\Handlers\SlugTranslateHandler;
use App\Models\Topic;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class TransLateSlug implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $topic;
    public function __construct(Topic $topic)
    {
        //队列任务构造器接受了Eloquent 模型，将会只序列化模型ID
        $this->topic=$topic;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //请求百度API接口进行翻译
        $slug=app(SlugTranslateHandler::class)->translate($this->topic->title);

        //避免模型监控器死循环调用,使用Db类对数据库进行操作
        DB::table('topics')->where('id',$this->topic->id)->update(['slug'=>$slug]);
    }
}
