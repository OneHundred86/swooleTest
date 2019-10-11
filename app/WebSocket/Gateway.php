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

        # 
        $class_c = "App\\WebSocket\\Controller\\$controller";
        $obj_c = new $class_c($io);
        
        return $obj_c->$action($io);
    }

    public static function handleClose($ws, $fd){
        ChatUser::leave($fd);
    }
}




