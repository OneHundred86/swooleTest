<?php
namespace App\WebSocket\Controller;

use App\WebSocket\Lib\ChatUser;
/**
 * 
 */
class UserController extends Controller
{
    # 加入聊天室
    public function join($io){
        // $io = $this->io;

        $fd = $io->getInputFd();
        $name = $io->i('name');
        ChatUser::join($fd, ['id' => $fd, 'name' => $name]);

        $user_list = ChatUser::getAllUser();
        // var_dump($user_list);

        foreach($user_list as $fd => $u){
            $data = [
                'from_user_name' => $name,
                'msg' => '大家好，我是新来的！',
            ];
            $io->sendData($fd, 'user', 'msg', $data);
        }

        return $io->o();
    }
    
    # 发送消息
    public function msg($io){
        $fd = $this->io->getInputFd();
        $user = ChatUser::getUser($fd);

        $msg = $this->io->i('msg');
        $name = $user['name'];

        $user_list = ChatUser::getAllUser();
        // var_dump($user_list);

        foreach($user_list as $fd => $u){
            $data = [
                'from_user_name' => $name,
                'msg' => $msg,
            ];
            $this->io->sendData($fd, 'user', 'msg', $data);
        }
    }
}