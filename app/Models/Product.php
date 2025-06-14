<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AvailabilityLevelEnum;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'client_id',
        'guid',
        'active',
        'created_at',
        'updated_at',
        'name',
        'url',
        'producer',
        'images',
        'description'
    ];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'images' => 'array',
    ];

    public function getId(): int
    {
        return $this->getAttribute('id');
    }

    public function getClientId(): int
    {
        return $this->getAttribute('client_id');
    }

    public function getClient(): Client
    {
        return Client::findOrFail($this->getAttribute('client_id'));
    }

    public function setClient(Client $client): self
    {
        return $this->setAttribute('client_id', $client->getId());
    }

    public function getGuid(): string
    {
        return $this->getAttribute('guid');
    }

    public function setGuid(string $guid): self
    {
        return $this->setAttribute('guid', $guid);
    }

    public function isActive(): bool
    {
        return (bool) $this->getAttribute('active');
    }

    public function setActive(bool $active): self
    {
        return $this->setAttribute('active', $active);
    }

    public function getCreatedAt(): DateTime
    {
        return $this->getAttribute('created_at')->toDateTime();
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->getAttribute('updated_at')->toDateTime();
    }

    public function getName(): ?string
    {
        return $this->getAttribute('name');
    }

    public function setName(?string $name): self
    {
        return $this->setAttribute('name', $name);
    }

    public function getPrice(): ?string
    {
        return $this->getAttribute('price');
    }

    public function setPrice(?string $price): self
    {
        return $this->setAttribute('price', $price);
    }

    public function getUrl(): ?string
    {
        return $this->getAttribute('url');
    }

    public function setUrl(?string $url): self
    {
        return $this->setAttribute('url', $url);
    }

    public function getCode(): ?string
    {
        return $this->getAttribute('code');
    }

    public function setCode(?string $code): self
    {
        return $this->setAttribute('code', $code);
    }

    public function getAvailabilityName(): ?string
    {
        return $this->getAttribute('availability_name');
    }

    public function setAvailabilityName(?string $availabilityName): self
    {
        return $this->setAttribute('availability_name', $availabilityName);
    }

    public function getAvailabilityForeignId(): ?string
    {
        return $this->getAttribute('availability_id');
    }

    public function setAvailabilityForeignId(?string $availabilityForeignId): self
    {
        return $this->setAttribute('availability_foreign_id', $availabilityForeignId);
    }

    public function getAvailability(): ?Availability
    {
        return $this->belongsTo(Availability::class, 'availability_id')->first();
    }

    public function setAvailability(?Availability $availability): self
    {
        if ($availability === null) {
            return $this->setAttribute('availability_id', null);
        }
        return $this->setAttribute('availability_id', $availability->getId());
    }

    public function getImageUrl(): ?string
    {
        return $this->getAttribute('image_url');
    }

    public function setImageUrl(?string $imageUrl): self
    {
        return $this->setAttribute('image_url', $imageUrl);
    }

    public function getUnit(): ?string
    {
        return $this->getAttribute('unit');
    }

    public function setUnit(?string $unit): self
    {
        return $this->setAttribute('unit', $unit);
    }

    public function getStock(): ?float
    {
        return $this->getAttribute('stock');
    }

    public function setStock(?float $stock): self
    {
        return $this->setAttribute('stock', $stock);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function getForeignId(): ?string
    {
        return $this->getAttribute('foreign_id');
    }

    public function setForeignId(?string $foreignId): self
    {
        return $this->setAttribute('foreign_id', $foreignId);
    }

    public function isNegativeStockAllowed(): bool
    {
        return (bool) $this->getAttribute('is_negative_stock_allowed');
    }

    public function setNegativeStockAllowed(bool $negativeStockAllowed): self
    {
        return $this->setAttribute('is_negative_stock_allowed', $negativeStockAllowed);
    }

    public function getAvailabilityColor(): ?string
    {
        return $this->getAttribute('availability_color');
    }

    public function setAvailabilityColor(?string $availabilityColor): self
    {
        return $this->setAttribute('availability_color', $availabilityColor);
    }

    public function getAvailabilityLevel(): ?AvailabilityLevelEnum
    {
        if ($this->getAttribute('availability_level') === null) {
            return null;
        }
        return AvailabilityLevelEnum::fromValue($this->getAttribute('availability_level'));
    }

    public function setAvailabilityLevel(?AvailabilityLevelEnum $availabilityLevel): self
    {
        return $this->setAttribute('availability_level', $availabilityLevel?->value);
    }

    public function getDescription(): ?string
    {
        return $this->getAttribute('description');
    }

    public function setDescription(?string $description): self
    {
        return $this->setAttribute('description', $description);
    }

    /**
     * @return array<string>
     */
    public function getImages(): ?array
    {
        return $this->getAttribute('images');
    }
}
