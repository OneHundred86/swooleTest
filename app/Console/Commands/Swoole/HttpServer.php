<?php

namespace App\Console\Commands\Swoole;

use Illuminate\Console\Command;

class HttpServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:http_server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test and start swoole http server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->start_http_server();
    }

    public function start_http_server(){
        $listenIp = '0.0.0.0';
        $listenPort = 9501;
        echo sprintf('start swoole http server: %s:%s ...', $listenIp, $listenPort) . PHP_EOL;
        $http = new \swoole_http_server($listenIp, $listenPort);

        $http->on('request', function ($request, $response) {
            var_dump($request->get, $request->post);
            $response->header("Content-Type", "text/html; charset=utf-8");
            $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
        });

        $http->start();
    }
}






