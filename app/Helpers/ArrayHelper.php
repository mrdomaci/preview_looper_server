<?php

declare(strict_types=1);

namespace App\Helpers;

class ArrayHelper
{
    /**
     * @param ?array<mixed> $array
     * @param string $key
     * @return bool
     */
    public static function containsKey(?array $array, string $key): bool
    {
        if ($array === null) {
            return false;
        }
        return array_key_exists($key, $array);
    }

    /**
     * @param array<mixed> $array
     * @param mixed $value
     * @return bool
     */
    public static function containsValue(array $array, mixed $value): bool
    {
        return in_array($value, $array, true);
    }

    public static function isArray(mixed $value): bool
    {
        return is_array($value);
    }

    /**
     * @param array<int,int> $array
     * @return array<int,int>
     */
    public static function sort(array $array): array
    {
        $keys = array_keys($array);
        rsort($array);
        $array = array_combine($keys, $array);
        return $array;
    }
}
