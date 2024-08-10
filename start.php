<?php

require_once __DIR__.'/vendor/autoload.php';


const SSH_HOST = '10.10.10.1';
const PORTS = [
    '12828:12828',
    '12829:12829'
];
const APP_MODE = \App\Contracts::PRODUCTION_MODE;
const SSH_PORT = 22;
const SQLITE_LOGGER = false;




\App\Application::start();


