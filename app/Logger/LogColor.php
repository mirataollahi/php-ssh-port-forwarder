<?php
/**
 * User: Mirataollahi ( @Mirataollahi124 )
 * Date: 10/26/24  Time: 12:47 PM
 */

namespace App\Logger;

enum LogColor: string
{
    case BLUE = "\033[0;34m";
    case GREEN = "\033[0;32m";
    case RED = "\033[0;31m";
    case YELLOW = "\033[1;33m";
    case CYAN = "\033[0;36m";
    case PURPLE = "\033[0;35m";
    case RESET = "\033[0m";
}