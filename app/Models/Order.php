<?php

declare(strict_types=1);

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

    public function getCode(): string
    {
        return $this->getAttribute('code');
    }

    public function getStatus(): string
    {
        return $this->getAttribute('status');
    }

    public function getFullName(): string
    {
        return $this->getAttribute('fullName');
    }

    public function getCompany(): ?string
    {
        return $this->getAttribute('company');
    }

    public function getEmail(): string
    {
        return $this->getAttribute('email');
    }

    public function getPhone(): ?string
    {
        return $this->getAttribute('phone');
    }

    public function getRemark(): ?string
    {
        return $this->getAttribute('remark');
    }

    public function getCashDeskOrder(): bool
    {
        return $this->getAttribute('cashDeskOrder');
    }

    public function getCustomerGuid(): ?string
    {
        return $this->getAttribute('customerGuid');
    }

    public function isPaid(): bool
    {
        return $this->getAttribute('paid');
    }

    public function getForeignStatusId(): string
    {
        return $this->getAttribute('foreignStatusId');
    }

    public function getSource(): string
    {
        return $this->getAttribute('source');
    }

    public function getVat(): float
    {
        return $this->getAttribute('vat');
    }

    public function getToPay(): float
    {
        return $this->getAttribute('toPay');
    }

    public function getCurrencyCode(): string
    {
        return $this->getAttribute('currencyCode');
    }

    public function getWithVat(): float
    {
        return $this->getAttribute('withVat');
    }

    public function getWithoutVat(): float
    {
        return $this->getAttribute('withoutVat');
    }

    public function getExchangeRate(): float
    {
        return $this->getAttribute('exchangeRate');
    }

    public function getPaymentMethod(): ?string
    {
        return $this->getAttribute('paymentMethod');
    }

    public function getShipping(): ?string
    {
        return $this->getAttribute('shipping');
    }

    public function getAdminUrl(): string
    {
        return $this->getAttribute('adminUrl');
    }
}
