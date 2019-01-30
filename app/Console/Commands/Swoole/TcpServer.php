<?php

namespace App\Console\Commands\Swoole;

use Illuminate\Console\Command;

class TcpServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:tcp_server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test and start swoole tcp server';

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
        $this->start_tcp_server();
    }

    public function start_tcp_server(){
        //创建Server对象
        $listenIp = '0.0.0.0';
        $listenPort = 9501;
        echo sprintf('start swoole tcp server: %s:%s ...', $listenIp, $listenPort) . PHP_EOL;
        $serv = new \swoole_server($listenIp, $listenPort); 

        //监听连接进入事件
        $serv->on('connect', function ($serv, $fd) {  
            echo "Client Connected: $fd" . PHP_EOL;
        });

        //监听数据接收事件
        $serv->on('receive', function ($serv, $fd, $from_id, $data) {
            echo "Received from $fd: $data" . PHP_EOL;
            $serv->send($fd, "what your send is " . $data);
        });

        //监听连接关闭事件
        $serv->on('close', function ($serv, $fd) {
            echo "Client Closed: $fd" . PHP_EOL;
        });

        //启动服务器
        $serv->start(); 
    }


}










