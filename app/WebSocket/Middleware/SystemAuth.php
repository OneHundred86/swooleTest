<?php
namespace App\WebSocket\Middleware;


use App\WebSocket\Lib\ChatUser;
/**
 * 
 */
class SystemAuth extends Middleware
{
    
    public function handle($io){
        $app = $io->i('app');
        $token = $io->i('token');
        if(empty($app) || empty($token)){
            $io->e('鉴权参数缺失');
            return false;
        }

        if($app != 'testSystemAuth'){
            $io->e('app不存在');
            return false;
        }

        $ticket = 'testSystemAuth-123456';
        if(md5(sprintf('%s-%s', $app, $ticket)) != $token){
            $io->e('token验证失败');
            return false;
        }

        return true;
    }
}