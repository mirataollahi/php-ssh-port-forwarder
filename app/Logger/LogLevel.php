<?php
/**
 * User: Mirataollahi ( @Mirataollahi124 )
 * Date: 10/24/24  Time: 2:53 PM
 */

namespace App\Logger;

enum LogLevel: int
{
    case INFO = 2;
    case SUCCESS = 4;
    case WARNING0 = 8;
    case ERROR = 16;
}