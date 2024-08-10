<?php

namespace App;

class Application
{
    public Logger $logger;
    public PortForwarder $portForwarder;
    public function __construct()
    {
        $this->logger = new Logger();
        $this->portForwarder = new PortForwarder($this->logger);
    }

    /**
     * @return void Create an application instance and start app
     */
    public static function start(): void
    {
        try {
            new self();
        }
        catch (\Throwable $exception){

        }
    }

    /**
     * Sleep time to handle process need duration
     */
    public static function sleep(float $second): void
    {
        usleep($second * 1000000);
    }
}
