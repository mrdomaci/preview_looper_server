<?php

declare(strict_types=1);

namespace App\Console\Commands;

abstract class AbstractClientCommand extends AbstractCommand
{
    protected function getClientId(): ?int
    {
        if ($this->argument('client_id') === null) {
            return null;
        }

        return (int) $this->argument('client_id');
    }
}
