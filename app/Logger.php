<?php

namespace App;
class Logger
{

    /**
     * Show terminal message log
     *
     * @param string|int|null $message Text message to show in terminal
     * @param string $type Error log level or type
     * @param int|float|null $processDuration Time need to complete background process
     * @return void
     */
    public function log(string|int|null $message, string $type = 'light_blue', int|float|null $processDuration = null): void
    {
        $colors = [
            'info' => "\033[0;34m",
            'success' => "\033[0;32m",
            'warning' => "\033[1;33m",
            'error' => "\033[0;31m",
            'light_blue' => "\033[0;36m",
            'purple' => "\033[0;35m",
        ];
        $color = $colors[$type] ?? $colors['light_blue'];
        echo $color . $message . "\033[0m" . PHP_EOL;
        if ($processDuration)
            Application::sleep($processDuration);
    }

}
