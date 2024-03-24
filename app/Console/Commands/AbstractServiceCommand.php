<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Service;

abstract class AbstractServiceCommand extends AbstractCommand
{
    protected function getService(): Service
    {
        return Service::findOrFail((int) $this->option('service'));
    }
}
