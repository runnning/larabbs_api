<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CalculateActiveUser extends Command
{

    //供我们调用命令
    protected $signature = 'larabbs:calculate-active-user';


    //命令描述
    protected $description = '生成活跃用户';

    //最终执行方法
    public function handle(User $user)
    {
        //最终执行的方法
        $this->info('开始计算...');

        $user->calculateAndCacheActiveUsers();

        $this->info('成功生产');
    }
}
