<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\EnumTrait;

enum EmailStatusEnum: string
{
    use EnumTrait;
    case NEW = 'new';
    case SENT = 'sent';
    case ERROR = 'error';
}
