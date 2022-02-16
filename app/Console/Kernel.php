<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule):void
    {
        // $schedule->command('inspire')->hourly();

        //一小时执行一次『活跃用户』数据生产的命令
        $schedule->command('mybbs:calculate-active-user')->hourly();
        //每日零时执行一次
        $schedule->command('mybbs:sync-user-actived-at')->daily('00:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands():void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
