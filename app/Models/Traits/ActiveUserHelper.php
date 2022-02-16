<?php
/**
 *User:ywn
 *Date:2021/7/30
 *Time:20:35
 */

namespace App\Models\Traits;


use App\Models\Reply;
use App\Models\Topic;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Integer;

trait ActiveUserHelper
{
    //用于存放临时用户数据
    protected array $users=[];

    //配置信息
    protected int $topic_weight=4;//话题权重
    protected int $reply_weight=1;//回复权重
    protected int $pass_days=7;//多少天内发表过内容
    protected int $user_number=6;//取出来多少用户

    //缓存相关设置
    protected string $cache_key='larabbs_active_users';
    protected int $cache_expire_in_seconds=65*60;

    /**
     * 获取活跃用户
    */
    public function getActiveUsers(){
        //尝试从缓存中取出 cache_key 对应的数据。如果能取到，便直接返回数据。
        //否则运行匿名函数中的代码来取出活跃用户数据，返回的同时做了缓存。
        return Cache::remember($this->cache_key,$this->cache_expire_in_seconds,function (){
            return $this->calculateActiveUsers();
        });
    }

    /**
     * 计算和缓存活跃用户
    */
    public function calculateAndCacheActiveUsers(): void
    {
        //获取活跃用户列表
        $active_users=$this->calculateActiveUsers();
        //并加以缓存
        $this->cacheActiveUsers($active_users);
    }

    /**
     * 计算活跃用户
    */
    private function calculateActiveUsers(): \Illuminate\Support\Collection
    {

        $this->calculateTopicScore();
        $this->calculateReplyScore();

        //数组按照得分排序
        $users=Arr::sort($this->users, static function ($user){
            return $user['score'];
        });

        // 我们需要的是倒序，高分靠前，第二个参数为保持数组的 KEY 不变
        $users=array_reverse($users,true);

        //只获取想要的数量
        $users=array_slice($users,0,$this->user_number,true);

        //新建一个空集合
        $active_users=collect();

        foreach ($users as $user_id=>$user){
            //寻找下是否可以找到用户
            $user= self::find($user_id);
            //如果数据库里有该用户的话
            if($user){
                //讲此用户实体放入集合的末尾
                $active_users->push($user);
            }
        }
        //返回数据
        return $active_users;
    }

    /**
     * 计算话题分数
    */
    private function calculateTopicScore(): void
    {
        //从话题数据表里取出限定时间范围($pass_days)内,有发表过话题的用户
        //并且同时取出用户此短时间内发布话题的数量
        $topic_users=Topic::query()->select(DB::raw('user_id,count(*) as topic_count'))
            ->where('created_at','>=',Carbon::now()->subDay($this->pass_days))
            ->groupBy('user_id')
            ->get();

        //根据话题数量计算得分
        foreach ($topic_users as $value){
            $this->users[$value->user_id]['score']=$value->topic_count*$this->topic_weight;
        }
    }

    /**
     * 计算回复分数
    */
    private function calculateReplyScore(): void
    {
        // 从回复数据表里取出限定时间范围（$pass_days）内，有发表过回复的用户
        // 并且同时取出用户此段时间内发布回复的数量

        $reply_users=Reply::query()->select(DB::raw('user_id,count(*) as reply_count'))
            ->where('created_at','>=',Carbon::now()->subDay($this->password))
            ->groupBy('user_id')
            ->get();
        //根据回复数量统计计算得分
        foreach ($reply_users as $value){
            $reply_score=$value->reply_count*$this->reply_weight;
            if(isset($this->users[$value->user_id])){
                $this->users[$value->user_id]['score']+=$reply_score;
            }else{
                $this->users[$value->user_id]['score']=$reply_score;
            }
        }
    }

    /**
     * 缓存活跃用户
    */
    private function cacheActiveUsers($active_users): void
    {
        //将数据放入缓存中
        Cache::put($this->cache_key,$active_users,$this->cache_expire_in_seconds);
    }
}
