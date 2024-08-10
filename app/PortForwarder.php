<?php

namespace App;

use Exception;
use Throwable;

class PortForwarder
{
    public Logger $logger;
    public Database $database;

    public array $report = [];

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
        $this->logger->log(PHP_EOL . "Welcome to ssh port forwarder script", "success", 0.5);
        $this->logger->log(PHP_EOL . "Starting SSH Forwarding Application ...", "purple", 1);
        $this->database = new Database($this->logger);
        $this->endSection();
        $this->manageSSHTunnels();
        $this->showAppProcessReport();
    }

    /**
     * Manager defined ssh port to forwarding
     *
     * @return void
     */
    public function manageSSHTunnels(): void
    {
        foreach (PORTS as $processId => $port) {
            try {
                list($localPort, $remotePort) = explode(':', $port);
                $this->report[$processId] = [
                    'local_port' => $localPort,
                    'remote_port' => $remotePort,
                ];
                $this->logger->log(str_repeat('*', 50));
                $this->logger->log("Starting forwarding port {$localPort} to {$remotePort} port ... ", 'light_blue');
                sleep(1);
                if (is_numeric($localPort) && is_numeric($remotePort)) {
                    if (!$this->isPortFree($localPort)) {
                        $this->startTunnel($localPort, $remotePort);
                        $message = "Successful remote connection started $localPort:$remotePort";
                        $this->logger->log($message, "success");
                        $this->report[$processId]['message'] = $message;
                        $this->report[$processId]['result'] = 'success';
                    } else {
                        $message = "The port forwarding process ($localPort:$remotePort) exist";
                        $this->report[$processId]['message'] = $message;
                        $this->logger->log($message, 'info');
                        $this->report[$processId]['result'] = 'info';
                    }
                } else {
                    $this->report[$processId]['message'] = "Invalid forwarding format";
                    $this->report[$processId]['result'] = 'error';
                }
                $this->endSection();
            } catch (Exception $exception) {
                $this->logger->log("Unknown error in running process");
            } finally {
                if (SQLITE_LOGGER)
                    $this->database->storeLog(
                        logLevel: $this->report[$processId]['result'],
                        message: $message ?? null,
                        localHost: '0.0.0.0', localPort: $localPort ?? null,
                        remoteHost: SSH_HOST, remotePort: $remotePort ?? null,
                        sshHost: SSH_HOST, sshPort: SSH_PORT
                    );
            }

        }
    }

    /**
     * Check a port is free is the os and can run ssh port forwarding
     *
     * @param int $port
     * @return bool
     */
    public function isPortFree(int $port): bool
    {
        $connection = @fsockopen('0.0.0.0', $port);
        if (is_resource($connection)) {
            fclose($connection);
            return true;
        }
        return false;
    }

    /**
     * Kill current exist ssh port forwarding process the os
     *
     * @param int $localPort
     * @param int $remotePort
     * @return void
     */
    public function killExistingTunnels(int $localPort, int $remotePort): void
    {
        exec("pkill -f 'ssh -p " . SSH_PORT . " -f -N -L 0.0.0.0:$localPort:$remotePort:$localPort'");
        $this->logger->log("Old remote connection killed in ports $localPort:$remotePort");
    }

    /**
     * Trying to start port forwarding with ssh service
     *
     * @param int $localPort Local server port
     * @param int $remotePort Remote server port
     * @return void
     */
    public function startTunnel(int $localPort, int $remotePort): void
    {
        if (APP_MODE === Contracts::PRODUCTION_MODE) {
            exec("ssh -p " . SSH_PORT . " -f -N -L 0.0.0.0:$localPort:" . SSH_HOST . ":$remotePort root@" . SSH_HOST);
        }
        else
            $this->logger->log("SSH Forward command skipped in development running" , 'yellow');
    }

    /**
     * Test a tcp connection to ssh server with remote port
     *
     * @param int $localPort
     * @return bool
     */
    public function isTunnelAlive(int $localPort): bool
    {
        try {
            $host = SSH_HOST;
            $timeout = 4;
            $socket = @fsockopen($host, $localPort, $errno, $error, $timeout);
            $isAlive = is_resource($socket);
            fclose($socket);
            return $isAlive;
        } catch (Throwable $throwable) {
            return false;
        }
    }

    /**
     * Script end sections views
     *
     * @param int $skippedLines
     * @param bool $showLine
     * @return void
     */
    public function endSection(int $skippedLines = 3, bool $showLine = false): void
    {
        if ($showLine)
            $this->logger->log(str_repeat('*', 50));
        echo str_repeat(PHP_EOL, $skippedLines);
    }

    /**
     * Show final application result
     *
     * @return void
     */
    public function showAppProcessReport(): void
    {
        $this->logger->log(PHP_EOL . "Port Forwarding Results:", 'purple');
        $this->logger->log(str_repeat('-', 75), 'info');
        $this->logger->log("| " . str_pad("Result", 15) . "| " . str_pad("Local Port", 15) . " | " . str_pad("Remote Port", 15) . " | " . str_pad("Result", 15) . " | " . "Message", 'info');
        $this->logger->log(str_repeat('-', 75), 'info');
        foreach ($this->report as $process) {
            $localPort = $process['local_port'] ?? 0;
            $remotePort = $process['remote_port'] ?? 0;
            $result = $process['result'] ?? 'error';
            $message = $process['message'] ?? 'There is a problem running the port-forwarding service';

            $lineColor = 'light_blue';
            if ($result === 'success') {
                $lineColor = 'success';
            }if ($result === 'info') {
                $lineColor = 'info';
            } elseif ($result === 'OK') {
                $lineColor = 'warning';
            } elseif ($result === 'error') {
                $lineColor = 'error';
            }

            $resultTitle = 'Successful';
            if ($result === 'warning')
                $resultTitle = 'Skipped';
            else if ($result === 'error')
                $resultTitle = 'Failed';

            $this->logger->log(
                sprintf("| %s | %s | %s | %s | %s",
                    str_pad($resultTitle, 15),
                    str_pad($localPort, 15),
                    str_pad($remotePort, 15),
                    str_pad($result, 15),
                    $message
                ),
                $lineColor
            );
        }
        $this->logger->log(str_repeat('-', 75), 'info');
    }
}
