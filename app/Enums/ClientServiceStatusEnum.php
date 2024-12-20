<?php

declare(strict_types=1);

namespace App\Enums;

enum ClientServiceStatusEnum: string
{
    use BaseEnumTrait;

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case DELETED = 'deleted';
}
