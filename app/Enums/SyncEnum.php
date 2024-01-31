<?php
namespace App\Enums;

enum SyncEnum:string {
    case PRODUCT = 'product';
    case ORDER = 'order';

    public function isProduct(): bool {
        return $this->value === self::PRODUCT->value;
    }

    public function isOrder(): bool {
        return $this->value === self::ORDER->value;
    }
}