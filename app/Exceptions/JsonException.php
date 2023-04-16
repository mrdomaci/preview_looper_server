<?php
namespace App\Exceptions;

use Throwable;

class JsonException extends SlackException
{
    public function __construct(Throwable $t)
    {
        $this->report($t);
    }
}
