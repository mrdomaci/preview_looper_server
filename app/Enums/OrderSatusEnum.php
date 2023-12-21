<?php
namespace App\Enums;

enum OrderSatusEnum:string {
    case NEW = 'new';
    case ACCEPTED = 'accepted';
    case IN_PROGRESS = 'in_progress';
    case CANCELLED = 'cancelled';
    case SHIPPED = 'shipped';
    case READY_FOR_PICKUP = 'ready_for_pickup';
    case DONE = 'done';

    public static function getIcon(string $status): string {
        return match ($status) {
            self::NEW->value => 'plus.png',
            self::ACCEPTED->value => 'inbox.png',
            self::IN_PROGRESS->value => 'gears.png',
            self::CANCELLED->value => 'stop.png',
            self::SHIPPED->value => 'fast-truck.png',
            self::READY_FOR_PICKUP->value => 'two-people-carry-box.png',
            self::DONE->value => 'check.png',
            default => 'plus.png',
        };
    }
}