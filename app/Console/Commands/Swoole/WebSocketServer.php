<?php

namespace App\Console\Commands\Swoole;

use Illuminate\Console\Command;

class WebSocketServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:websocket_server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test and start swoole websocket server';

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
        $listenIp = '0.0.0.0';
        $listenPort = 9502;
        echo sprintf('start swoole websocket server: %s:%s ...', $listenIp, $listenPort) . PHP_EOL;
        //创建websocket服务器对象，监听0.0.0.0:9502端口
        // $ws = new \swoole_websocket_server($listenIp, $listenPort);
        $ws = new \Swoole\WebSocket\Server($listenIp, $listenPort);
        $ws->set([
            // 'open_tcp_keepalive' => true,
            // 'tcp_keepidle' => 600,
            // 'tcp_keepinterval' => 60,
            // 'tcp_keepcount' => 5,
            // 启用心跳检测，此选项表示每隔多久轮循一次，单位为秒 
            'heartbeat_check_interval' => 10,
            // 与heartbeat_check_interval配合使用。表示连接最大允许空闲的时间 
            'heartbeat_idle_time' => 20,
        ]);

        //监听WebSocket连接打开事件
        $ws->on('open', function ($ws, $request) {
            // var_dump($request->fd, $request->get, $request->server);
            echo sprintf('%s connected: %s', date('Y-m-d H:i:s'), $request->fd) . PHP_EOL;
            // $ws->push($request->fd, "hello, welcome\n");
        });

        //监听WebSocket消息事件
        $ws->on('message', function ($ws, $frame) {
            // var_dump($frame); echo PHP_EOL . PHP_EOL;
            echo sprintf('%s recv message from %s: %s', date('Y-m-d H:i:s'), $frame->fd, $frame->data) . PHP_EOL;
            // $ws->push($frame->fd, $frame->data);
            $msg = json_decode($frame->data);
            if(isset($msg->type) && $msg->type == 'heartbeat'){
                return;
            }

            foreach($ws->connections as $fd){
                $ws->push($fd, $frame->data);
            }
        });

        //监听WebSocket连接关闭事件
        $ws->on('close', function ($ws, $fd) {
            echo sprintf('%s closed: %d', date('Y-m-d H:i:s'), $fd) . PHP_EOL . PHP_EOL;
        });

        $ws->start();
    }
}










