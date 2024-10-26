<?php
/**
 * User: Mirataollahi ( @Mirataollahi124 )
 * Date: 10/26/24  Time: 1:10 PM
 */

namespace App\Logger;

enum LoggerOption: int
{
    case WITHOUT_DATE = 2;
    case WITHOUT_LEVEL = 4;
    case END_SECTION = 8;
    case EXPLODE_WITH_LINE = 16;
    case SLEEP = 32;
    case PASS_LINE_BEFORE = 64;
}