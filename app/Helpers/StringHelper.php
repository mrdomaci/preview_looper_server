<?php
declare(strict_types=1);

namespace App\Helpers;

class StringHelper
{
    public static function getBool(string $value): bool
    {
        return match ($value) {
            'true' => true,
            'false' => false,
            '1' => true,
            '0' => false,
            default => false,
        };
    }

    public static function upper(string $value): string
    {
        return mb_strtoupper($value);
    }
}