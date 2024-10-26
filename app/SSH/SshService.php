<?php

namespace App\SSH;

use App\Application;
use App\Logger\LogColor;
use App\Logger\Logger;
use App\Logger\LogLevel;
use App\Tools\Reporter;
use Exception;
use Throwable;

class SshService
{
    public Logger $logger;
    public Reporter $reporter;
    public function __construct()
    {
        $this->logger = new Logger();
        $this->reporter = Application::$reporter;
    }

    public function manageSSHTunnels(): void
    {
        foreach (PORTS as $processId => $port) {
            try {
                list($localPort, $remotePort) = explode(':', $port);
                Application::$reporter->add($processId,[
                    'local_port' => $localPort,
                    'remote_port' => $remotePort,
                ]);
                $this->logger->showLine(80,LogColor::BLUE);
                $this->logger->info("Starting forwarding port $localPort to $remotePort port ... ");
                sleep(1);
                if (is_numeric($localPort) && is_numeric($remotePort)) {
                    if (!$this->isPortFree($localPort)) {
                        $this->startTunnel($localPort, $remotePort);
                        $message = "Successful remote connection started $localPort:$remotePort";
                        $this->logger->success($message);
                        $this->reporter->update($processId,[
                            'message' => $message ,
                            'result' => LogLevel::SUCCESS
                        ]);
                    } else {
                        $message = "The port forwarding process ($localPort:$remotePort) exist";
                        $this->reporter->update($processId,[
                            'message' => $message ,
                            'result' => LogLevel::INFO
                        ]);
                        $this->logger->info($message);
                    }
                } else {
                    $this->reporter->update($processId , [
                        'message' => 'Invalid forwarding format',
                        'result' => LogLevel::ERROR ,
                    ]);
                }
            } catch (Exception $exception) {
                $this->logger->error("Unknown error in running process : {$exception->getMessage()}");
            } finally {
                if (ENABLE_DATABASE)
                    Application::$database->storeLog(
                        logLevel: $this->reporter->find($processId,'result'),
                        message: $message ?? null,
                        localHost: '0.0.0.0', localPort: $localPort ?? null,
                        remoteHost: SSH_HOST, remotePort: $remotePort ?? null,
                        sshHost: SSH_HOST, sshPort: SSH_PORT
                    );

                $this->logger->showLine(80,LogColor::BLUE);
                $this->logger->endSection();
            }
        }
    }

    public function startTunnel(int $localPort, int $remotePort): bool
    {
        try {
            $sshHost = SSH_HOST;
            $sshPort = SSH_PORT;
            $localIp = LOCAL_IP;
            $remoteIp = REMOTE_IP;
            $command = "ssh -p $sshPort -f -N -L $localIp:$localPort:$remoteIp:$remotePort root@$sshHost";
            $this->logger->info("Executing => $command ...");
            if (!DEBUG_MODE){
                $this->logger->warning('Skipping ssh command in debugging mode ... ');
                return true;
            }
            else {
                exec($command,$output);
                var_dump($output);
                return true;
            }
        }
        catch (Throwable $exception){
            $this->logger->error("Error in executing ssh command : {$exception->getMessage()}");
            return false;
        }
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
        } catch (Throwable $exception) {
            $this->logger->error("Error in checking tunnel alive : {$exception->getMessage()}");
            return false;
        }
    }


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
        $this->logger->info("Old remote connection killed in ports $localPort:$remotePort");
    }
}