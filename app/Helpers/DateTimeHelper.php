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
        $dateTimeString .= '+0100';
        return urlencode($dateTimeString);
    }

    public static function adjustDateToCurrentMonth(DateTime $date): DateTime
    {
        $currentDate = new DateTime();
        $currentYearMonth = $currentDate->format('Y-m');
        $adjustedDate = DateTime::createFromFormat('Y-m-d', $currentYearMonth . '-' . $date->format('d'));
        if (!$adjustedDate || $adjustedDate > $currentDate) {
            $adjustedDate = (clone $adjustedDate)->modify('-1 month');
        }
        return $adjustedDate < $date ? $date : $adjustedDate;
    }
}
