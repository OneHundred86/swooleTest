<?php

namespace App\Console\Commands\Swoole;

use Illuminate\Console\Command;

class ProcessPool extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:process_pool';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test swoole process pool';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $workerNum = 3;
        $pool = new \Swoole\Process\Pool($workerNum);

        $pool->on("WorkerStart", function ($pool, $workerId) {
            echo sprintf("worker#{%d} is started", $workerId) . PHP_EOL;

            for($i = 0; $i < 100; $i++) {
                sleep(1);
                echo sprintf("worker#{%d}: %d", $workerId, $i) . PHP_EOL;
            }
        });

        $pool->on("WorkerStop", function ($pool, $workerId) {
            echo sprintf("worker#{%d} is stopped", $workerId) . PHP_EOL;
        });

        $pool->start();
    }
}










