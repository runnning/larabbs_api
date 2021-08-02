<?php
/**
 *User:ywn
 *Date:2021/8/1
 *Time:0:21
 */

namespace App\Observers;


use App\Models\Link;
use Illuminate\Support\Facades\Cache;

class LinkObserver
{
    //在保存时清空cache_key对应的缓存
    public function saved(Link $link){
        Cache::forget($link->cache_key);
    }
}
