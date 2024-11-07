<?php

declare(strict_types=1);

namespace App\Traits;

trait EnumTrait
{
    /**
     * Get an enum instance from a case-insensitive string.
     *
     * @param string $case
     * @return self
     * @throws \InvalidArgumentException If the case does not match any enum case.
     */
    public static function fromCase(string $case): self
    {
        // Loop through all enum cases
        foreach (self::cases() as $enumCase) {
            // Check for case-insensitive match
            if (strcasecmp($enumCase->name, $case) === 0) {
                return $enumCase;
            }
        }

        // Throw an exception if no match was found
        throw new \InvalidArgumentException("Invalid enum case: $case");
    }
}
