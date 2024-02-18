<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderProduct extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'client_id',
        'order_id',
        'order_guid',
        'product_id',
        'product_guid',
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

    public function getOrderId(): int
    {
        return $this->getAttribute('order_id');
    }

    public function setOrder(Order $order): self
    {
        return $this->setAttribute('order_id', $order->getId());
    }

    public function getOrderGuid(): string
    {
        return $this->getAttribute('order_guid');
    }

    public function setOrderGuid(string $orderGuid): self
    {
        return $this->setAttribute('order_guid', $orderGuid);
    }

    public function getProductId(): ?int
    {
        return $this->getAttribute('product_id');
    }

    public function setProduct(?Product $product): self
    {
        if ($product === null) {
            return $this->setAttribute('product_id', null);
        }
        return $this->setAttribute('product_id', $product->getId());
    }

    public function getProductGuid(): string
    {
        return $this->getAttribute('product_guid');
    }

    public function setProductGuid(string $productGuid): self
    {
        return $this->setAttribute('product_guid', $productGuid);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
