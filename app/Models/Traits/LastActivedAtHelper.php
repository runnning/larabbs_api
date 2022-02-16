<?php
/**
 *User:ywn
 *Date:2021/8/1
 *Time:20:40
 */

namespace App\Models\Traits;


use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;


trait LastActivedAtHelper
{
    //Hash表前缀
    protected string $hash_prefix='larabbs_last_actived_at_';
    //字段前缀
    protected string $field_prefix='user_';

    public function recordLastActivedAt(): void
    {

        //获取今日Redis哈希表的命名
        $hash=$this->getHashFormDateString(Carbon::now()->toDateString());

        //字段名称,如user_
        $field=$this->getHashField();

        //当前时间，如：2017-10-21 08:35:15
        $now=Carbon::now()->toDateTimeString();

        //数据写入Redis，字段已存在会被更新
        Redis::hSet($hash,$field,$now);
    }

    /**
     * 同步活跃时间到数据库
    */
    public function syncUserActivedAt(): void
    {

        //获取昨日 Redis 哈希表的命名，如：larabbs_last_actived_at_2017-10-21
        $hash=$this->getHashFormDateString(Carbon::yesterday()->toDateString());

        //从Redis中获取所有哈希表里数据
        $datas=Redis::hGetAll($hash);
        //遍历,并同步到数据库中
        foreach ($datas as $user_id =>$actived_at){
            // 会将 `user_1` 转换为 1
            $user_id=str_replace($this->field_prefix,'',$user_id);

            //只有当用户存在时才更新到数据库中
            if($user= self::find($user_id)){
                $user->last_actived_at=$actived_at;
                $user->save();
            }

        }
        //以数据库为中心的存储,既已同步,即可删除
        Redis::del($hash);
    }

    //访问器
    public function getLastActivedAtAttribute($value): \Illuminate\Support\Carbon|Carbon|null
    {
        // 获取今日Redis 哈希表的命名，如：larabbs_last_actived_at_2017-10-21
        $hash=$this->getHashFormDateString(Carbon::now()->toDateString());

        // 字段名称，如：user_1
        $field=$this->getHashField();

        // 三元运算符，优先选择 Redis 的数据，否则使用数据库中
        $datetime=Redis::hGet($hash,$field)?:$value;

        //如果存在的话,返回时间对应的Carbon实体
        if($datetime){
            return new Carbon($datetime);
        }

        //否则使用用户注册时间
      return $this->created_at;
    }

    /**
     * hash表名称
    */
    public function getHashFormDateString(string $date): string
    {
        // Redis 哈希表的命名，如：larabbs_last_actived_at_2017-10-21
        return $this->hash_prefix.$date;
    }

    /**
     * 字段名称
    */
    public function getHashField(): string
    {
        //字段名称,如user_1
        return $this->field_prefix.$this->id;
    }
}
