<?php
namespace App\WebSocket\Lib;
use Swoole\WebSocket\Server as WebSocketServer;


class IO
{
    use \App\WebSocket\Traits\Output;

    protected $ws;
    protected $frame;

    public function __construct(WebSocketServer $ws, $frame){
        $this->ws = $ws;

        $dataObj = json_decode($frame->data);
        $frame->_obj = $dataObj;

        $this->frame = $frame;
    }

    protected function getInputObj(){
        return $this->frame->_obj;
    }

    # 获取当前的套接字
    public function getInputFd(){
        return $this->frame->fd;
    }

    # 获取tos数据包的键的值
    public function input($key, $default = null){
        $obj = $this->getInputObj();

        if(isset($obj->$key))
            return $obj->$key;

        return $default;
    }

    public function i($key, $default = null){
        return $this->input($key, $default);
    }


}