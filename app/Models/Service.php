<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    public $timestamps = false;
    public const DYNAMIC_PREVIEW_IMAGES = 1;
    public const UPSELL = 2;

    protected $fillable = [
        'name',
        'hash',
        'url-path',
        'view-name',
    ];

    public function getId(): int
    {
        return $this->getAttribute('id');
    }

    public function getName(): string
    {
        return $this->getAttribute('name');
    }

    public function getHash(): string
    {
        return $this->getAttribute('hash');
    }

    public function getUrlPath(): string
    {
        return $this->getAttribute('url-path');
    }

    public function getViewName(): string
    {
        return $this->getAttribute('view-name');
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function isDynamicPreviewImages(): bool
    {
        return $this->id === self::DYNAMIC_PREVIEW_IMAGES;
    }

    public function isUpsell(): bool
    {
        return $this->id === self::UPSELL;
    }

    public static function getDynamicPreviewImages(): self
    {
        return self::find(self::DYNAMIC_PREVIEW_IMAGES);
    }

    public static function getUpsell(): self
    {
        return self::find(self::UPSELL);
    }
}
