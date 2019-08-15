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
        // $this->demo1();
        $this->demo2();
    }

    /*
    # 执行结果:
    start coro 1
    start to resume 1 @1
    resume coro 1 @1
    start to resume 1 @2
    resume coro 1 @2
    main
    */
    public function demo1(){
        $id = go(function(){    # go相当于co::create
            $id = co::getUid();
            echo "start coro $id\n";
            co::suspend();
            echo "resume coro $id @1\n";
            co::suspend();
            echo "resume coro $id @2\n";
        });

        echo "start to resume $id @1\n";
        co::resume($id);
        echo "start to resume $id @2\n";
        co::resume($id);
        echo "main\n";
    }

    public function demo2(){
        var_dump(co::stats());
        
        $channel = new co\Channel();

        # 消费者协程
        co::create(function() use ($channel){
            echo 'consumer start' . PHP_EOL;
            while(true){
                $data = $channel->pop();
                echo 'coroutine get data:' . PHP_EOL;
                var_dump($data);
                echo PHP_EOL . PHP_EOL;
            }

            echo 'consumer stop' . PHP_EOL;
        });

        # 生产者协程
        co::create(function() use ($channel){
            echo 'producer start' . PHP_EOL;
            while(true) {
                $d = $this->ask('send data to coroutine:');
                if($d === null)
                    break;

                $channel->push($d);
            }

            echo 'producer stop' . PHP_EOL;
        });

        var_dump(co::stats());
        // swoole_event::wait();
    }
}














