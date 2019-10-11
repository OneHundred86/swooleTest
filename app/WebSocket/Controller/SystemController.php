<?php
namespace App\WebSocket\Controller;

use App\WebSocket\Lib\ChatUser;

/**
 * 
 */
class SystemController extends Controller
{
    public function heartbeat(){
        # ignore
    }

    public function broadcast($io){
        $user_list = ChatUser::getAllUser();
        // var_dump($user_list);

        $msg = $io->i('msg');

        foreach($user_list as $fd => $u){
            $data = [
                'from_user_name' => '系统广播',
                'msg' => $msg,
            ];
            $io->sendData($fd, 'user', 'msg', $data);
        }

        return $io->o();
    }
}