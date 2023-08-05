<?php
namespace App\Exceptions;

use Throwable;

class AddonNotInstalledException extends SlackException
{
    public function __construct(Throwable $t)
    {
        $this->report($t);
    }
}
