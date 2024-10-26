<?php
/**
 * User: Mirataollahi ( @Mirataollahi124 )
 * Date: 10/24/24  Time: 2:50 PM
 */

namespace App\Logger;

use App\Application;

class Logger
{
    public ?string $tag = null;
    public int $processSleep = 1;
    public bool $addDateTime = true;
    public bool $addLevelText = true;


    public function __construct(?string $tag = null)
    {
        $this->tag = $tag;
    }

    public const LEVEL_COLORS = [
        LogLevel::INFO->value     => LogColor::BLUE,
        LogLevel::SUCCESS->value  => LogColor::GREEN,
        LogLevel::WARNING0->value => LogColor::YELLOW,
        LogLevel::ERROR->value    => LogColor::RED,
        null                      => LogColor::CYAN,
    ];

    public const LEVEL_TEXT = [
        LogLevel::SUCCESS->value  => '[SUCCESS]',
        LogLevel::INFO->value     => '[ INFO  ]',
        LogLevel::WARNING0->value => '[WARNING]',
        LogLevel::ERROR->value    => '[ ERROR ]',
    ];

    private function addCliPrefix(?string $message, LogLevel $level , ?int $options = null): string
    {
        $levelText = $this->addLevelText
            ? self::LEVEL_TEXT[$level->value] : null;
        $dateTime = $this->addDateTime
            ? (' ['.date('Y-m-d H:i:s') .'] ') : null;
        return "$levelText$dateTime$message";
    }

    public function success(?string $message,?int $options = null): void
    {
        $this->cliPrint($message,LogLevel::SUCCESS,$options);
    }

    public function info(?string $message,?int $options = null): void
    {
        $this->cliPrint($message,LogLevel::INFO,$options);
    }

    public function warning(?string $message,?int $options = null): void
    {
        $this->cliPrint($message,LogLevel::WARNING0,$options);
    }
    public function error(?string $message,?int $options = null): void
    {
        $this->cliPrint($message,LogLevel::ERROR,$options);
    }
    public function endSection(int $lineCount = 3): void
    {
        $this->echo(str_repeat(PHP_EOL,$lineCount));
    }
    public function showLine(int $length = 50,?LogColor $logColor = null): void
    {
        $this->echo(str_repeat('*',$length),$logColor);
    }

    public function cliPrint(string|int|null $message,LogLevel $level,?int $options = null): void
    {
        if ($options & LoggerOption::PASS_LINE_BEFORE->value){
            $this->endSection(1);
        }
        /** Command line print */
        $stdOutString = $this->addCliPrefix($message,$level,$options);
        $color = self::LEVEL_COLORS[$level->value] ?? self::LEVEL_TEXT[LogLevel::INFO->value];
        $this->echo($stdOutString,$color);

        /** Check options */
        if ($options & LoggerOption::END_SECTION->value){
            echo str_repeat(PHP_EOL, 3);
        }
        if ($options & LoggerOption::EXPLODE_WITH_LINE->value){
            $this->showLine(50,$color);
        }
        if ($options & LoggerOption::SLEEP->value){
            usleep($this->processSleep * 1000000);
        }
    }

    public function echo(?string $message = null,?LogColor $logColor = null): void
    {
        $colorCode = is_null($logColor) ? LogColor::CYAN->value : $logColor->value;
        $resetCode = LogColor::RESET->value;
        echo "$colorCode$message$resetCode \n";
    }

    /**
     * Show final application result
     *
     * @return void
     */
    public function showAppProcessReport(): void
    {
        $this->showLine(100,LogColor::BLUE);
        $this->echo("| " . str_pad("Result", 15) . "| " . str_pad("Local Port", 15) . " | " . str_pad("Remote Port", 15) . " | " . str_pad("Result", 15) . " | " . "Message", LogColor::BLUE);
        $this->showLine(100, LogColor::BLUE);
        foreach (Application::$reporter->all() as $report) {
            $localPort = $report['local_port'] ?? 0;
            $remotePort = $report['remote_port'] ?? 0;
            $result = $report['result'] ?? 'error';
            $message = $report['message'] ?? 'Empty';

            $lineColor = LogColor::CYAN;
            if ($result === LogLevel::SUCCESS) {
                $lineColor = LogColor::GREEN;
            }
            if ($result === LogLevel::INFO) {
                $lineColor = LogColor::BLUE;
            } elseif ($result === 'OK') {
                $lineColor = LogLevel::WARNING0;
            } elseif ($result === LogLevel::ERROR) {
                $lineColor = LogColor::RED;
            }

            $resultTitle = 'OK';
            if ($result === LogLevel::WARNING0)
                $resultTitle = 'Skipped';
            else if ($result === LogLevel::ERROR)
                $resultTitle = 'Failed';
            else if ($result === LogLevel::SUCCESS)
                $resultTitle = 'Success';

            $this->echo(
                sprintf("| %s | %s | %s | %s | %s",
                    str_pad($resultTitle, 15),
                    str_pad($localPort, 15),
                    str_pad($remotePort, 15),
                    str_pad($result->value, 15),
                    $message
                ),
                  $lineColor
            );
        }
        $this->echo(str_repeat('*', 100),  LogColor::BLUE);
    }


}