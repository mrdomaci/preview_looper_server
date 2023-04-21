<?php
namespace App\Exceptions;

use Throwable;

class NumbersException extends SlackException
{
    public function __construct(Throwable $t)
    {
        $this->report($t);
    }
}
