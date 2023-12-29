<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'guid',
        'code',
        'status',
        'fullName',
        'company',
        'email',
        'phone',
        'remark',
        'cashDeskOrder',
        'customerGuid',
        'paid',
        'foreignStatusId',
        'source',
        'vat',
        'toPay',
        'currencyCode',
        'withVat',
        'withoutVat',
        'exchangeRate',
        'paymentMethod',
        'shipping',
        'adminUrl',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
