<?php
namespace App\Exceptions;

use Throwable;

class DataInsertFailException extends SlackException
{
    public function __construct(Throwable $t)
    {
        $this->report($t);
    }
}
