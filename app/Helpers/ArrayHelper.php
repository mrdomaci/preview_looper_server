<?php
declare(strict_types=1);

namespace App\Helpers;

class ArrayHelper
{
    /**
     * @param array<mixed> $array
     * @param string $key
     * @return bool
     */
    public static function containsKey(array $array, string $key): bool
    {
        return array_key_exists($key, $array);
    }

    public static function isArray(mixed $value): bool
    {
        return is_array($value);
    }
}