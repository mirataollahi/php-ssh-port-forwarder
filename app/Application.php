<?php

namespace App;

use App\Database\SqliteDatabase;
use App\Logger\Logger;
use App\Logger\LoggerOption;
use App\SSH\SshService;
use App\Tools\Reporter;

class Application
{
    public static SqliteDatabase $database;
    public Logger $logger;
    public static Reporter $reporter;
    public static SshService $sshService;

    public function __construct()
    {
        $this->logger = new Logger();
        $this->logger->info("Welcome to ssh port forwarder script",LoggerOption::PASS_LINE_BEFORE->value);
        $this->logger->info("Starting SSH Forwarding Application ...");
        self::$reporter = new Reporter();
        self::$database = new SqliteDatabase();
        self::$sshService = new SshService();
        $this->logger->endSection();
        self::$sshService->manageSSHTunnels();
        $this->logger->showAppProcessReport();
    }
}
