<?php

namespace App\Console\Commands\swoole;

use Illuminate\Console\Command;
use App\WebSocket\WebSocket;

class WebSocketServer1 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:websocket_server1';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test and start websocket server';

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
        $listenIp = '0.0.0.0';
        $listenPort = 9502;

        WebSocket::start($listenIp, $listenPort);
    }
}
