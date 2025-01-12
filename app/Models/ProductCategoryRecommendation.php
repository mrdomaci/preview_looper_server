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
            ->where('client_id', $this->getAttribute('client_id'));
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_guid', 'guid')
            ->where('client_id', $this->getAttribute('client_id'));
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function setProduct(string $product_guid): self
    {
        return $this->setAttribute('product_guid', $product_guid);
    }

    public function setCategory(string $category_guid): self
    {
        return $this->setAttribute('category_guid', $category_guid);
    }

    public function getProduct(): Product
    {
        return Product::where('guid', $this->getAttribute('product_guid'))->where('client_id', $this->getAttribute('client_id'))->first();
    }

    public function getCategory(): Category
    {
        return Category::where('guid', $this->getAttribute('category_guid'))->where('client_id', $this->getAttribute('client_id'))->first();
    }
}
