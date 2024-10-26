<?php

namespace App\Database;

use App\Logger\Logger;
use App\Logger\LogLevel;
use Exception;
use PDO;
use PDOException;

class SqliteDatabase
{

    public PDO|null $driver = null;

    private const SQL_LITE_FILE = 'logs.db';

    public bool $isConnected = false;
    public Logger $logger;
    public string $table = 'logs';

    public function __construct()
    {
        $this->logger = new Logger('SQLITE');
        $this->initPdo();
        $this->createLogsTable();
    }

    /**
     * Initial pdo sql lite connection
     *
     * @return void
     */
    public function initPdo(): void
    {
        try {
            $this->driver = new PDO('sqlite:' . self::SQL_LITE_FILE);
            $this->driver->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->driver->exec("PRAGMA timezone = 'Asia/Tehran'");
            $this->createLogsTable();
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    private function createLogsTable(): void
    {
        try {
            $createTableQuery = "CREATE TABLE IF NOT EXISTS {$this->table} (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                log_type TEXT,
                message TEXT,
                local_host TEXT,
                local_port INT,
                remote_host TEXT,
                remote_port INT,
                ssh_host TEXT,
                ssh_port INT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )";
            $this->driver->exec($createTableQuery);
            $this->isConnected = true;
            return;
        } catch (Exception $exception) {
            $this->isConnected = false;
            echo $exception->getMessage() . PHP_EOL;
        }
    }

    public function storeLog(?LogLevel $logLevel = null, ?string $message = null, ?string $localHost = null, int|string|null $localPort = null, ?string $remoteHost = null, int|string|null $remotePort = null, ?string $sshHost = null, int|string|null $sshPort = null): void
    {
        if (!$logLevel) $logLevel = LogLevel::INFO;
        try {
            $insertQuery = "INSERT INTO $this->table (log_type, message, local_host, local_port, remote_host, remote_port, ssh_host, ssh_port,created_at) VALUES (:log_type, :message, :local_host, :local_port, :remote_host, :remote_port, :ssh_host, :ssh_port, datetime('now'))";
            $statement = $this->driver->prepare($insertQuery);
            $statement->execute([
                'log_type' => $logLevel->value ?: LogLevel::INFO->value,
                'message' => $message,
                'local_host' => $localHost,
                'local_port' => (int)$localPort,
                'remote_host' => $remoteHost,
                'remote_port' => (int)$remotePort,
                'ssh_host' => $sshHost,
                'ssh_port' => (int)$sshPort,
            ]);
        } catch (PDOException $e) {
            die("Error inserting log into database: " . $e->getMessage());
        }
    }
}
