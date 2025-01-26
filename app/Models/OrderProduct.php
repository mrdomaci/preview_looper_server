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
        'order_guid',
        'product_guid',
    ];

    public function getClientId(): int
    {
        return $this->getAttribute('client_id');
    }

    public function setClient(Client $client): self
    {
        return $this->setAttribute('client_id', $client->getId());
    }

    public function getOrderGuid(): string
    {
        return $this->getAttribute('order_guid');
    }

    public function setOrderGuid(string $orderGuid): self
    {
        return $this->setAttribute('order_guid', $orderGuid);
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
        return $this->belongsTo(Order::class, 'order_guid', 'guid');
    }
}
