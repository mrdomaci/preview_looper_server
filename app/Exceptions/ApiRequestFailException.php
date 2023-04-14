<?php
namespace App\Exceptions;

use Throwable;

class ApiRequestFailException extends SlackException
{
    public function __construct(Throwable $t)
    {
        $this->report($t);
    }
}
