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

    public function getOrderId(): int
    {
        return $this->getAttribute('order_id');
    }

    public function getOrderGuid(): string
    {
        return $this->getAttribute('order_guid');
    }

    public function getProductId(): int
    {
        return $this->getAttribute('product_id');
    }

    public function getProductGuid(): string
    {
        return $this->getAttribute('product_guid');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
