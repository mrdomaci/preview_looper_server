<?php

declare(strict_types=1);

namespace App\Enums;

use App\Exceptions\EnumFailException;
use Exception;

trait BaseEnumTrait
{
    public static function fromCase(string $case, ?bool $toUpper = true): self
    {
        if ($toUpper) {
            $case = strtoupper($case);
        }
        foreach (self::cases() as $item) {
            if ($case === $item->name) {
                return $item;
            }
        }
        throw new EnumFailException(new Exception('Invalid enum case: ' . $case));
    }

    public static function fromValue(string $value): self
    {
        foreach (self::cases() as $item) {
            if ($item->value === $value) {
                return $item;
            }
        }
        throw new EnumFailException(new Exception('Invalid enum value: ' . $value));
    }
}
