<?php

declare(strict_types=1);

namespace App\Exceptions;

use Throwable;

class DropboxFailException extends SlackException
{
    public function __construct(Throwable $t)
    {
        $this->report($t);
    }
}
