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
            self::NEW->value => 'plus.gif',
            self::ACCEPTED->value => 'inbox.gif',
            self::IN_PROGRESS->value => 'gears.gif',
            self::CANCELLED->value => 'stop.gif',
            self::SHIPPED->value => 'fast-truck.gif',
            self::READY_FOR_PICKUP->value => 'two-people-carry-box.gif',
            self::DONE->value => 'check.gif',
            default => 'plus.gif',
        };
    }
}