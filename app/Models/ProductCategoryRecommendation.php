<?php

declare(strict_types=1);

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCategoryRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'category_id',
        'client_id',
        'is_forbidden',
        'product_guid',
        'category_guid',
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

    public function getProductId(): int
    {
        return $this->getAttribute('product_id');
    }

    public function setProduct(Product $product): self
    {
        return $this->setAttribute('product_id', $product->getId());
    }

    public function getCategoryId(): int
    {
        return $this->getAttribute('category_id');
    }

    public function setCategory(Category $category): self
    {
        return $this->setAttribute('category_id', $category->getId());
    }

    public function getCreatedAt(): DateTime
    {
        return $this->getAttribute('created_at');
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->getAttribute('updated_at');
    }

    public function isForbidden(): bool
    {
        return $this->getAttribute('is_forbidden');
    }

    public function setForbidden(bool $isForbidden): self
    {
        return $this->setAttribute('is_forbidden', $isForbidden);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_guid', 'guid')
                    ->where('client_id', $this->client_id);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_guid', 'guid')
                    ->where('client_id', $this->client_id); 
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
