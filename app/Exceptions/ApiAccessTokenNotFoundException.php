<?php
namespace App\Exceptions;

use Throwable;

class ApiAccessTokenNotFoundException extends SlackException
{
    public function __construct(Throwable $t)
    {
        $this->report($t);
    }
}
