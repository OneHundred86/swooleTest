<?php
namespace App\WebSocket\Middleware;

/**
 * 
 */
abstract class Middleware
{
    public function __construct()
    {
    }

    // 中间件的回调方法，true表示继续执行，false表示终止
    # => true | false
    abstract public function handle(IO $io);
}