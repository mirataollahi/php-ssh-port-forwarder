<?php

namespace App;

interface Contracts
{
    const DEVELOP_MODE = 'develop_mode';
    const PRODUCTION_MODE = 'production_mode';
    const BASE_PATH = __DIR__.'/..';
    const INFO_LOG = 2;
    const SUCCESS_LOG = 4;
    const WARNING_LOG = 8;
    const ERROR_LOG = 16;
    const LOGS_TABLE = 'logs';
    const ACTIVE_LOGS = self::INFO_LOG | self::SUCCESS_LOG | self::WARNING_LOG | self::ERROR_LOG;
    const DATABASE_LOG = true;
}
