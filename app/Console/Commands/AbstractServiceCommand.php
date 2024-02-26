<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Service;
use Illuminate\Console\Command;

abstract class AbstractServiceCommand extends Command
{
    protected function getService(): Service
    {
        return Service::findOrFail((int) $this->argument('service'));
    }
}
