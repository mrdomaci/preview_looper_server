<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

abstract class AbstractCommand extends Command
{
    private const MAXIMUM_ITERATIONS = 10000;
    private const ITERATION_COUNT = 100;

    protected function getMaxIterationCount(): int
    {
        return self::MAXIMUM_ITERATIONS;
    }

    protected function getIterationCount(): int
    {
        return self::ITERATION_COUNT;
    }

    protected function getOffset(int $iteration): int
    {
        return $iteration * self::ITERATION_COUNT;
    }
}
