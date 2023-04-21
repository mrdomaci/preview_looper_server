<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Exceptions\NumbersException;
use Exception;

class NumbersHelper
{
    public static function isInteger(mixed $value): bool
    {
        return is_int($value);
    }

    public static function intToBool(int $int): bool
    {
        if ($int === 0) {
            return false;
        }
        if ($int === 1) {
            return true;
        }
        throw new NumbersException(new Exception('Invalid integer value: ' . $int . ' for conversion to boolean'));
    }
}