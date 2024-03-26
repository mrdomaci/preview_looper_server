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

    public static function removeParameter(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = explode('?', $value);
        return $value[0];
    }

    public static function getSingelRegexResult(string $pattern, string $subject): string
    {
        preg_match($pattern, $subject, $matches);
        return $matches[0];
    }

    public static function getIdFromImage(?string $image): ?string
    {
        if ($image === null) {
            return null;
        }
        return self::getSingelRegexResult('/(\d+)(?=[^\d])/', $image);
    }

    public static function hash(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        return hash('xxh32', $value);
    }
}
