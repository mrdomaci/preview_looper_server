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
        $adjustedDate = new DateTime($currentDate->format('Y') . '-' . $currentDate->format('m') . '-' . $date->format('d'));
        if ($adjustedDate > $currentDate) {
            $adjustedDate->modify('-1 month');
        }
        if ($adjustedDate < $date) {
            return $date;
        }
        return $adjustedDate;
    }
}
