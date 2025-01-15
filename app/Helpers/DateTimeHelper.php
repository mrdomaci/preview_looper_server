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

    public static function adjustDateToCurrentMonth(DateTime $inputDate): DateTime
    {
        $date = clone $inputDate;
        $today = new \DateTime('today');
        $oneMonthAgo = (clone $today)->modify('-1 month');
        if ($date >= $oneMonthAgo) {
            return $date;
        }
        $newDate = new \DateTime();
        $newDate->setDate((int)$today->format('Y'), (int)$today->format('m'), (int)$date->format('d'));
        if ($newDate->format('m') !== $today->format('m')) {
            $currentYear = (int)$today->format('Y');
            $currentMonth = (int)$today->format('m');

            $newMonth = $currentMonth + 1;
            if ($newMonth > 12) {
                $newMonth = 1;
                $currentYear++;
            }
            $newDate->setDate($currentYear, $newMonth, 1);
        }

        return $newDate;
    }
}
