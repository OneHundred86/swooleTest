<?php
namespace App\WebSocket\Traits;

use App\WebSocket\Lib\ErrorCode;

/**
 *   输出|发送（特殊说明：被trait的类需要声明$ws和$frame类成员变量）
 */
trait Output
{
    // 发送数据包到套接字$fd
    # $data :: mix()
    public function send($fd, $c, $a, $errcode, $errmessage = null, $data = null){
        if(!is_integer($errcode)){
            $data = $errcode;
            $errcode = ErrorCode::OK;
        }
        if(empty($errmessage)){
            $errmessage = ErrorCode::get($errcode);
        }

        $arr = [
            'errcode' => $errcode,
            'errmessage' => $errmessage,
            'c' => $c,
            'a' => $a,
            'data' => $data,
        ];

        return $this->sendArray($fd, $arr);
    }

    public function sendError($fd, $c, $a, $errcode, $errmessage = null){
        return $this->send($fd, $c, $a, $errcode, $errmessage);
    }

    public function sendData($fd, $c, $a, $data){
        return $this->send($fd, $c, $a, ErrorCode::OK, null, $data);
    }

    public function sendArray($fd, array $packet){
        $str = json_encode($packet, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        return $this->sendString($fd, $str);
    }

    public function sendString($fd, string $str){
        # 容错
        if(!$this->ws->exist($fd))
            return false;

        return $this->ws->push($fd, $str);
    }

    // 直接返回数据包到当前套接字
    # errcode : int|mix  0成功，其他数字表示错误
    # data : mix
    public function o($errcode = ErrorCode::OK, $errmessage = null, $data = null){
        $c = isset($this->frame->_obj->c) ? $this->frame->_obj->c : null;
        $a = isset($this->frame->_obj->a) ? $this->frame->_obj->a : null;
        return $this->send($this->frame->fd, $c, $a, $errcode, $errmessage, $data);
    }

    public function e($errcode = ErrorCode::ERROR, $errmessage = null, $data = null){
        if(!is_integer($errcode)){
            $errmessage = $errcode;
            $errcode = ErrorCode::ERROR;
        }

        return $this->o($errcode, $errmessage, $data);
    }
}