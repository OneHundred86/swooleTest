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
        # swoole
        Commands\Swoole\TcpServer::class,
        Commands\Swoole\TcpClient::class,
        Commands\Swoole\HttpServer::class,
        Commands\Swoole\WebSocketServer::class,
        Commands\Swoole\WebSocketServer1::class,
        Commands\Swoole\ProcessPool::class,
        Commands\Swoole\Coroutine::class,
        Commands\WebSocketClient::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
