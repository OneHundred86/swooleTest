<?php
namespace App\WebSocket\Middleware;


use App\WebSocket\Lib\ChatUser;
/**
 * 
 */
class UserAuth extends Middleware
{
    
    public function handle($io){
        $fd = $io->getInputFd();
        if(!ChatUser::getUser($fd)){
            return $io->e('未登录');
        }

        return true;
    }
}