<?php
namespace App\Websocket;


/**
 * 
 */
class Route
{
    # sprintf('%s-%s', $c, $a) => [$controller, $action, $middlewares]
    private static $route_list;
    # 中间件配置栈，[[m1, m2, ...], [m3, ...], ...]
    private static $middleware_stack = [];

    public function __construct()
    {
    }

    // 获取路由(未注册的路由默认为没有中间件的路由)
    # => [$controller, $action, $middlewares]
    public static function route($c, $a){
        $key = self::genKey($c, $a);

        if(!array_key_exists($key, self::$route_list)){
            $controller = sprintf('%sController', ucfirst($c));
            $action = $a;
            return [$controller, $action, []];
        }

        $route = self::$route_list[$key];

        return [$route['controller'], $route['action'], $route['middlewares']];
    }

    # 添加路由组
    public static function group(array $middlewares, \Closure $route_func){
        self::$middleware_stack[] = $middlewares;

        $route_func();

        array_pop(self::$middleware_stack);
    }

    # 添加路由
    public static function add($c, $a, array $middlewares = []){
        $key = self::genKey($c, $a);

        $controller = sprintf('%sController', ucfirst($c));
        $action = $a;

        $middlewares = array_merge(self::getCurMiddlewares(), $middlewares);

        self::$route_list[$key] = compact('controller', 'action', 'middlewares');
    }

    # 获取当前路由配置中间件
    # => array() :: [m1, m2, m3, ...]
    protected static function getCurMiddlewares(){
        $middlewares = [];

        foreach(self::$middleware_stack as $mw_arr){
            $middlewares = array_merge($middlewares, $mw_arr);
        }

        return $middlewares;
    }

    public static function genKey($c, $a){
        return sprintf('%s-%s', $c, $a);;
    }
}