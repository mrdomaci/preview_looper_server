<?php

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
    ];

    public function getId(): int
    {
        return $this->getAttribute('id');
    }

    public function getClientId(): int
    {
        return $this->getAttribute('client_id');
    }

    public function getProductId(): int
    {
        return $this->getAttribute('product_id');
    }

    public function getName(): string
    {
        return $this->getAttribute('name');
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
