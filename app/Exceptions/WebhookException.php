<?php

declare(strict_types=1);

namespace App\Exceptions;

use Throwable;

class WebhookException extends SlackException
{
    public function __construct(Throwable $t)
    {
        $this->report($t);
    }
}
