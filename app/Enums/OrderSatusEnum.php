<?php
namespace App\Enums;

use App\Models\SettingsService;

enum OrderSatusEnum:string {
    case NEW = 'order-status.new';
    case ACCEPTED = 'order-status.accepted';
    case IN_PROGRESS = 'order-status.in_progress';
    case CANCELLED = 'order-status.cancelled';
    case SHIPPED = 'order-status.shipped';
    case READY_FOR_PICKUP = 'order-status.ready_for_pickup';
    case DONE = 'order-status.done';
    case UNKNOWN = 'order-status.unknown';

    public static function getIcon(SettingsService $settingsService): string {
        return match ($settingsService->getAttribute('name')) {
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