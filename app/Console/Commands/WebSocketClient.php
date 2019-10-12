<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use WebSocket\Client;

class WebSocketClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:client';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        // $this->test0();
        // $this->test1();
        $this->testPrivate();
    }

    protected function test0(){
        $url = 'ws://my.swoole:8080/ws/';
        $client = new Client($url);
        $client->send('test');

        try {
            echo $client->receive() . PHP_EOL;
        } catch (\Exception $e){
            echo sprintf('接受消息失败:%s', $e->getMessage()) . PHP_EOL;
        }

        $client->close();
    }

    protected function test1(){
        $url = 'ws://my.swoole:8080/ws/';
        $client = new Client($url);

        $packet = [
            'c' => 'system',
            'a' => 'test',
        ];
        $client->send(json_encode($packet));

        try {
            echo $client->receive() . PHP_EOL;
        } catch (\Exception $e) {
            echo sprintf('接受消息失败:%s', $e->getMessage()) . PHP_EOL;
        }

        $client->close();
    }

    protected function testPrivate(){
        $url = 'ws://my.swoole:8080/ws/';
        $client = new Client($url);

        $app = 'testSystemAuth';
        $ticket = 'testSystemAuth-123456';

        $packet = [
            'c' => 'system',
            'a' => 'broadcast',
            'msg' => '大家好，这是系统广播的消息',
            'app' => $app,
            'token' => md5(sprintf('%s-%s', $app, $ticket)),
        ];
        $client->send(json_encode($packet));

        try {
            $ret = $client->receive();
            $json = json_decode($ret);

            if(!isset($json->errcode)){
                echo $ret . PHP_EOL;
            }elseif($json->errcode != 0){
                echo $json->errmessage . PHP_EOL;
            }else{
                echo '发送成功' . PHP_EOL;
            }
        } catch (\Exception $e) {
            echo sprintf('接受消息失败:%s', $e->getMessage()) . PHP_EOL;
        }

        $client->close();
    }
}
