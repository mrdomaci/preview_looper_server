<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AvailabilityLevelEnum;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    use HasFactory;

    protected $fillable=[
        'client_id',
        'name',
        'foreign_id',
        'is_system',
        'is_on_stock',
        'is_sold_out_negative_stock_allowed',
        'is_sold_out_negative_stock_forbidden',
    ];

    public function getId(): int
    {
        return $this->getAttribute('id');
    }

    public function getClientId(): int
    {
        return $this->getAttribute('client_id');
    }

    public function setClient(Client $client): self
    {
        return $this->setAttribute('client_id', $client->getId());
    }
    public function getName(): string
    {
        return $this->getAttribute('name');
    }

    public function setName(string $name): self
    {
        return $this->setAttribute('name', $name);
    }

    public function getForeignId(): string
    {
        return $this->getAttribute('foreign_id');
    }

    public function setForeignId(string $foreignId): self
    {
        return $this->setAttribute('foreign_id', $foreignId);
    }

    public function getDescription(): ?string
    {
        return $this->getAttribute('description');
    }

    public function setDescription(?string $description): self
    {
        return $this->setAttribute('description', $description);
    }

    public function getColor(): ?string
    {
        return $this->getAttribute('color');
    }

    public function setColor(?string $color): self
    {
        return $this->setAttribute('color', $color);
    }

    public function isSystem(): bool
    {
        return (bool) $this->getAttribute('is_system');
    }

    public function setIsSystem(bool $isSystem): self
    {
        return $this->setAttribute('is_system', $isSystem);
    }

    public function isOnStock(): bool
    {
        return (bool) $this->getAttribute('is_on_stock');
    }

    public function setIsOnStock(bool $isOnStock): self
    {
        return $this->setAttribute('is_on_stock', $isOnStock);
    }

    public function isSoldOutNegativeStockAllowed(): bool
    {
        return (bool) $this->getAttribute('is_sold_out_negative_stock_allowed');
    }

    public function setIsSoldOutNegativeStockAllowed(bool $isSoldOutNegativeStockAllowed): self
    {
        return $this->setAttribute('is_sold_out_negative_stock_allowed', $isSoldOutNegativeStockAllowed);
    }

    public function isSoldOutNegativeStockForbidden(): bool
    {
        return (bool) $this->getAttribute('is_sold_out_negative_stock_forbidden');
    }

    public function setIsSoldOutNegativeStockForbidden(bool $isSoldOutNegativeStockForbidden): self
    {
        return $this->setAttribute('is_sold_out_negative_stock_forbidden', $isSoldOutNegativeStockForbidden);
    }

    public function getOnStockInHours(): ?string
    {
        return $this->getAttribute('on_stock_in_hours');
    }

    public function setOnStockInHours(?string $onStockInHours): self
    {
        return $this->setAttribute('on_stock_in_hours', $onStockInHours);
    }

    public function getDeliveryInHours(): ?string
    {
        return $this->getAttribute('delivery_in_hours');
    }

    public function setDeliveryInHours(?string $deliveryInHours): self
    {
        return $this->setAttribute('delivery_in_hours', $deliveryInHours);
    }

    public function getGoogleAvailabilityId(): ?string
    {
        return $this->getAttribute('google_availability_id');
    }

    public function setGoogleAvailabilityId(?string $googleAvailabilityId): self
    {
        return $this->setAttribute('google_availability_id', $googleAvailabilityId);
    }

    public function getGoogleAvailabilityName(): ?string
    {
        return $this->getAttribute('google_availability_name');
    }

    public function setGoogleAvailabilityName(?string $googleAvailabilityName): self
    {
        return $this->setAttribute('google_availability_name', $googleAvailabilityName);
    }

    public function getLevel(): AvailabilityLevelEnum
    {
        return AvailabilityLevelEnum::fromValue($this->getAttribute('level'));
    }

    public function setLevel(AvailabilityLevelEnum $availabilityLevel): self
    {
        return $this->setAttribute('level', $availabilityLevel->value);
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->getAttribute('created_at');
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->getAttribute('updated_at');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function isForbidden(): bool
    {
        return (bool) $this->getAttribute('is_forbidden');
    }

    public function setIsForbidden(bool $isForbidden): self
    {
        return $this->setAttribute('is_forbidden', $isForbidden);
    }
}
