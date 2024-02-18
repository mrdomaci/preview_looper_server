<?php

declare(strict_types=1);

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'guid',
        'active',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
        return new DateTime($this->getAttribute('created_at'));
    }

    public function getUpdatedAt(): DateTime
    {
        return new DateTime($this->getAttribute('updated_at'));
    }

    public function getName(): ?string
    {
        return $this->getAttribute('name');
    }

    public function setName(?string $name): self
    {
        return $this->setAttribute('name', $name);
    }

    public function getPerex(): ?string
    {
        return $this->getAttribute('perex');
    }

    public function setPerex(?string $perex): self
    {
        return $this->setAttribute('perex', $perex);
    }

    public function getDescription(): ?string
    {
        return $this->getAttribute('description');
    }

    public function setDescription(?string $description): self
    {
        return $this->setAttribute('description', $description);
    }

    public function getProducer(): ?string
    {
        return $this->getAttribute('producer');
    }

    public function setProducer(?string $producer): self
    {
        return $this->setAttribute('producer', $producer);
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

    public function getParentProduct(): ?Product
    {
        return $this->belongsTo(Product::class, 'parent_product_id')->first();
    }

    public function setParentProduct(?Product $product): self
    {
        if ($product === null) {
            return $this->setAttribute('parent_product_id', null);
        }
        return $this->setAttribute('parent_product_id', $product->getId());
    }

    public function getAvailability(): ?string
    {
        return $this->getAttribute('availability');
    }

    public function setAvailability(?string $availability): self
    {
        return $this->setAttribute('availability', $availability);
    }

    public function getAvailabilityId(): ?string
    {
        return $this->getAttribute('availability_id');
    }

    public function setAvailabilityId(?string $availabilityId): self
    {
        return $this->setAttribute('availability_id', $availabilityId);
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

    function getCategoryName(): ?string
    {
        return $this->getAttribute('category');
    }

    public function setCategoryName(?string $category): self
    {
        return $this->setAttribute('category', $category);
    }

    public function getCategory(): ?Category
    {
        return $this->category()->first();
    }

    public function setCategory(?Category $category): self
    {
        if ($category === null) {
            return $this->setAttribute('category_id', null);
        }
        return $this->setAttribute('category_id', $category->getId());
    }

    public function getStock(): ?float
    {
        return $this->getAttribute('stock');
    }

    public function setStock(?float $stock): self
    {
        return $this->setAttribute('stock', $stock);
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class)->orderBy('priority', 'asc');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public static function clone(Product $product): self
    {
        $clone = new Product();
        $clone->setAttribute('client_id', $product->getClientId());
        $clone->setAttribute('guid', $product->getGuid());
        $clone->setAttribute('active', $product->isActive());
        $clone->setAttribute('created_at', $product->getCreatedAt());
        $clone->setAttribute('updated_at', $product->getUpdatedAt());
        $clone->setAttribute('parent_product_id', $product->getId());
        $clone->setAttribute('code', $product->getCode());
        $clone->save();

        return $clone;
    }
}
