<?php
namespace App\WebSocket;

use App\WebSocket\Route;
use App\WebSocket\Lib\ErrorCode;
use App\WebSocket\Lib\IO;
use App\WebSocket\Lib\ChatUser;

/**
 * 
 */
class Gateway
{
    public static function init($ws){
        ChatUser::init();
    }

    public static function handleMessage($ws, $frame){
        $io = new IO($ws, $frame);

        $c = $io->I('c');
        $a = $io->I('a');

        # 非法(无效)消息
        if(!$c || !$a){
            return $io->e(ErrorCode::INVALID_MSG);
        }

        # 路由
        list($controller, $action, $middlewares) = Route::route($c, $a);

        # 中间件验证
        if($middlewares){
            foreach($middlewares as $m){
                $class_m = "App\\WebSocket\\Middleware\\$m";
                $obj_m = new $class_m();

                if(!$obj_m->handle($io))
                    return;
            }
        }

        # 处理消息
        $class_c = sprintf('App\WebSocket\Controller\%s', $controller);
        if(!class_exists($class_c))
            throw new \Exception(sprintf('class not found: %s', $class_c), 1);
            
        $obj_c = new $class_c($io);

        if(!method_exists($obj_c, $action))
            throw new \Exception(sprintf('method not found: %s::%s', $class_c, $action), 1);
            
        return $obj_c->$action($io);
    }

    public static function handleClose($ws, $fd){
        ChatUser::leave($fd);
    }
}




