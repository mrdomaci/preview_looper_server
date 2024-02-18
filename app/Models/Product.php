<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\NumbersHelper;
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

    public function getGuid(): string
    {
        return $this->getAttribute('guid');
    }

    public function isActive(): bool
    {
        return NumbersHelper::intToBool($this->getAttribute('active'));
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

    public function getDescription(): ?string
    {
        return $this->getAttribute('description');
    }

    public function getProducer(): ?string
    {
        return $this->getAttribute('producer');
    }

    public function getPrice(): ?string
    {
        return $this->getAttribute('price');
    }

    public function getUrl(): ?string
    {
        return $this->getAttribute('url');
    }

    public function getCode(): ?string
    {
        return $this->getAttribute('code');
    }

    public function getParentProduct(): ?Product
    {
        return $this->belongsTo(Product::class, 'parent_product_id')->first();
    }

    public function getAvailability(): ?string
    {
        return $this->getAttribute('availability');
    }

    public function getAvailabilityId(): ?string
    {
        return $this->getAttribute('availability_id');
    }

    public function getImageUrl(): ?string
    {
        return $this->getAttribute('image_url');
    }

    public function getUnit(): ?string
    {
        return $this->getAttribute('unit');
    }

    function getCategoryName(): ?string
    {
        return $this->getAttribute('category');
    }

    public function getCategory(): ?Category
    {
        return $this->category()->first();
    }

    public function getStock(): ?int
    {
        return $this->getAttribute('stock');
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
