<?php

namespace App\Console\Commands\Swoole;

use Illuminate\Console\Command;

class TcpClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:tcp_client';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test and start swoole tcp client';

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
        $serverIp = $this->ask('tcp server ip', '127.0.0.1');
        $serverPort = $this->ask('tcp server port', 9501);

        $client = new \swoole_client(SWOOLE_SOCK_TCP);
        if (!$client->connect($serverIp, $serverPort, -1)){
            exit("connect failed. Error: {$client->errCode}\n");
        }
        echo sprintf('connected to %s:%s', $serverIp, $serverPort) . PHP_EOL;

        while(true) {
            $sendMsg = $this->ask('msg to send');
            if(!$sendMsg)
                break;

            $client->send($sendMsg);
            echo 'recv msg:' . $client->recv() . PHP_EOL;
        }

        $client->close();
    }
}







