<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Service;

abstract class AbstractClientServiceCommand extends AbstractCommand
{
    protected function findClient(): ?Client
    {
        if ($this->option('client') === null) {
            return null;
        }

        return Client::find((int) $this->option('client'));
    }

    protected function findService(): ?Service
    {
        if ($this->option('service') === null) {
            return null;
        }

        return Service::find((int) $this->option('service'));
    }
}
