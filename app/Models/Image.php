<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'client_id',
        'product_id',
        'name',
        'hash',
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

    public function getName(): string
    {
        return $this->getAttribute('name');
    }

    public function setName(string $name): self
    {
        return $this->setAttribute('name', $name);
    }

    public function getHash(): string
    {
        return $this->getAttribute('hash');
    }

    public function setHash(string $hash): self
    {
        return $this->setAttribute('hash', $hash);
    }

    public function getPriority(): ?int
    {
        return $this->getAttribute('priority');
    }

    public function setPriority(?int $priority): self
    {
        return $this->setAttribute('priority', $priority);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
