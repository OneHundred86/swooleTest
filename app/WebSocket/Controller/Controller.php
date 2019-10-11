<?php
namespace App\WebSocket\Controller;

use App\WebSocket\Lib\IO;
/**
 * 
 */
class Controller
{
    protected $io;

    public function __construct(IO $io)
    {
        $this->io = $io;
    }
}