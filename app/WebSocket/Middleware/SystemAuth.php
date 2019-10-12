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
            return $io->e('鉴权参数缺失');
        }

        if($app != 'testSystemAuth'){
            return $io->e('app不存在');
        }

        $ticket = 'testSystemAuth-123456';
        if(md5(sprintf('%s-%s', $app, $ticket)) != $token){
            return $io->e('token验证失败');
        }

        return true;
    }
}