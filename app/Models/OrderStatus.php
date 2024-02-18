<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'foreign_id',
        'name',
        'system',
        'order',
        'mark_as_paid',
        'color',
        'background_color',
        'change_order_items',
        'stock_claim_resolved',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
