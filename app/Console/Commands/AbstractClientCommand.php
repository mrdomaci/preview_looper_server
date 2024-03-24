<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Client;

abstract class AbstractClientCommand extends AbstractCommand
{
    protected function getClient(): Client
    {
        return Client::findOrFail((int) $this->option('client'));
    }

    protected function findClient(): ?Client
    {
        if ($this->option('client') === null) {
            return null;
        }

        return Client::find((int) $this->option('client'));
    }
}
