<?php
namespace App\WebSocket;

use App\WebSocket\Gateway;
use App\WebSocket\Route;

/**
 * 
 */
class WebSocket
{
    public static function start($listenIp = '0.0.0.0', $listenPort = '8090'){
        echo sprintf('start websocket server: %s:%s ...', $listenIp, $listenPort) . PHP_EOL;

        $ws = new \Swoole\WebSocket\Server($listenIp, $listenPort);
        $ws->set([
            # 启动的Worker进程数
            'worker_num' => 8,
            # 启用心跳检测，此选项表示每隔多久轮循一次，单位为秒(https://wiki.swoole.com/wiki/page/283.html)
            'heartbeat_check_interval' => 10,
            # 与heartbeat_check_interval配合使用。表示连接最大允许空闲的时间(https://wiki.swoole.com/wiki/page/284.html)
            'heartbeat_idle_time' => 60,
        ]);

        //监听WebSocket连接打开事件
        $ws->on('open', function($ws, $request){
            return self::onOpen($ws, $request);
        });

        //监听WebSocket消息事件
        $ws->on('message', function($ws, $frame){
            return self::onMessage($ws, $frame);
        });

        //监听WebSocket连接关闭事件
        $ws->on('close', function($ws, $fd){
            return self::onClose($ws, $fd);
        });

        # 注册路由
        // Route::register();
        include_once('app/WebSocket/Config/route.php');

        self::init($ws);

        $ws->start();
    }

    public static function onOpen(\Swoole\WebSocket\Server $ws, $request) {
        // var_dump($request->fd, $request->get, $request->server);
        echo sprintf('%s connected: %s', date('Y-m-d H:i:s'), $request->fd) . PHP_EOL;
    }

    public static function onMessage(\Swoole\WebSocket\Server $ws, \Swoole\WebSocket\Frame $frame) {
        // var_dump($frame); echo PHP_EOL . PHP_EOL;
        echo sprintf('%s recv message from %s: %s', date('Y-m-d H:i:s'), $frame->fd, $frame->data) . PHP_EOL;

        # 消息处理
        try {
            return Gateway::handleMessage($ws, $frame);
        } catch (\Exception $e) {
            $stack_trace = json_encode(array_slice(debug_backtrace(), 0, 5), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            \Log::error(__METHOD__, ['error' => $e->getMessage(), 'stack_trace' => $stack_trace]);
        }
    }

    public static function onClose(\Swoole\WebSocket\Server $ws, $fd) {
        echo sprintf('%s closed: %d', date('Y-m-d H:i:s'), $fd) . PHP_EOL . PHP_EOL;

        # 消息处理
        try {
            return Gateway::handleClose($ws, $fd);
        } catch (\Exception $e) {
            $stack_trace = json_encode(array_slice(debug_backtrace(), 0, 5), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            \Log::error(__METHOD__, ['error' => $e->getMessage(), 'stack_trace' => $stack_trace]);
        }
    }

    # websocket开启前的初始化
    protected static function init(\Swoole\WebSocket\Server $ws){
        Gateway::init($ws);
    }
}











