<?php

namespace App\Console\Commands\Swoole;

use Illuminate\Console\Command;
use \Swoole\Coroutine as co;

class Coroutine extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:coroutine';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test swoole coroutine';

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
        $this->demo1();
        // $this->demo2();
    }

    /*
    # 执行结果:
    start coroutine 1
    start to resume 1 @1
    resume coroutine 1 @1
    start to resume 1 @2
    resume coroutine 1 @2
    main
    */
    public function demo1(){
        $id = go(function(){    # go相当于co::create
            $id = co::getUid();
            echo "start coroutine $id\n";
            co::suspend();  # 携程挂起
            echo "resume coroutine $id @1\n";
            co::suspend();  # 携程挂起
            echo "resume coroutine $id @2\n";
        });

        echo "start to resume $id @1\n";
        co::resume($id);    # 携程恢复
        echo "start to resume $id @2\n";
        co::resume($id);    # 携程恢复

        echo "main\n";
    }

    public function demo2(){
        define('STOP_CODE', -1);
        $channel = new co\Channel();

        # 消费者协程1
        co::create(function() use ($channel){
            echo 'consumer 1 start' . PHP_EOL;
            while(true){
                $data = $channel->pop();    # 如果没有数据，会导致协程挂起
                echo "coroutine 1 get data: $data" . PHP_EOL;

                if($data === STOP_CODE)
                    break;
            }

            echo 'consumer 1 stop' . PHP_EOL;
        });

        # 消费者协程2
        co::create(function() use ($channel){
            echo 'consumer 2 start' . PHP_EOL;
            while(true){
                $data = $channel->pop();    # 如果没有数据，会导致协程挂起
                echo "coroutine 2 get data: $data" . PHP_EOL;

                if($data === STOP_CODE)
                    break;
            }

            echo 'consumer 2 stop' . PHP_EOL;
        });

        # 生产者协程
        co::create(function() use ($channel){
            echo 'producer start' . PHP_EOL;
            
            for ($i=1; $i <= 100; $i++) {
                $channel->push($i); # 如果数据满了，会导致协程挂起
            }

            # 使携程结束
            $channel->push(STOP_CODE);
            $channel->push(STOP_CODE);

            echo 'producer stop' . PHP_EOL;
        });

        \swoole_event::wait();
    }
}














