<?php
namespace App\Exceptions;

use Throwable;

class ApiResponsePaginatorFailException extends SlackException
{
    public function __construct(Throwable $t)
    {
        $this->report($t);
    }
}
