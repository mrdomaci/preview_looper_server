<?php
declare(strict_types=1);

namespace App\Helpers;

use DateTime;

class DateTimeHelper
{
    public static function getDateTimeString(DateTime $dateTime): string
    {
        $dateTimeString = $dateTime->format('Y-m-d');
        $dateTimeString .= 'T';
        $dateTimeString .= $dateTime->format('H:i:s');
        $dateTimeString .= '%2B0100';
        return $dateTimeString;
    }
}