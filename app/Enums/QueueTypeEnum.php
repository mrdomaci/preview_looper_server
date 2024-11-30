<?php

declare(strict_types=1);

namespace App\Enums;

enum QueueTypeEnum: string
{
    use BaseEnumTrait;

    case PRODUCT = 'product';
    case ORDER = 'order';
}
