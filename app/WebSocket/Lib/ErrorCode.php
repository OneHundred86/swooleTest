<?php
namespace App\WebSocket\Lib;

/**
 * 
 */
class ErrorCode
{
    const OK = 0;
    const ERROR = -1;
    const INVALID_MSG = 1;

    public static function get(int $code){
        switch ($code) {
            case self::OK:
                return 'ok';
            case self::ERROR:
                return 'error';
            case self::INVALID_MSG:
                return 'invalid msg';
            
            default:
                return sprintf('undefined error code : %s', $code);
                break;
        }
    }
}