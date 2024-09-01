<?php

declare(strict_types=1);

namespace App\Enums;

enum QueueStatusEnum: string
{
    use BaseEnumTrait;

    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case PENDING = 'pending';
    case RUNNING = 'running';
    case EXPIRED = 'expired';
    case KILLED = 'killed';
    case DONE = 'done';
}

