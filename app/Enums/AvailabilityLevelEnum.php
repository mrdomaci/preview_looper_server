<?php

declare(strict_types=1);

namespace App\Enums;

enum AvailabilityLevelEnum: int
{
    case IN_STOCK = 1;
    case SELLABLE = 2;
    case OUT_OF_STOCK = 3;
    case UNKNOWN = 4;

    public static function fromValue(int $value): self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }
        return self::UNKNOWN;
    }
}
