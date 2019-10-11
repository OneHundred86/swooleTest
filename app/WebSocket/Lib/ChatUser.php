<?php
namespace App\WebSocket\Lib;

use Swoole\Table;
/**
 * 
 */
class ChatUser
{
    private static $table;

    public static function init(){
        $table = new Table(65536);

        $table->column('id', Table::TYPE_INT, 4);
        $table->column('name', Table::TYPE_STRING, 128);
        $table->create();

        self::$table = $table;
    }

    public static function join($fd, array $userInfo){
        return self::$table->set($fd, $userInfo);
    }

    public static function leave($fd){
        return self::$table->del($fd);
    }

    # => user_list :: array()
    public static function getAllUser(){
        $list = [];
        foreach(self::$table as $k => $v){
            $list[$k] = $v;
        }

        return $list;
    }

    # user :: array()
    public static function getUser($fd){
        return self::$table->get($fd);
    }
}