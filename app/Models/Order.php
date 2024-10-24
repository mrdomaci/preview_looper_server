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

    public function setClient(Client $client): self
    {
        return $this->setAttribute('client_id', $client->getId());
    }

    public function getGuid(): string
    {
        return $this->getAttribute('guid');
    }

    public function setGuid(string $guid): self
    {
        return $this->setAttribute('guid', $guid);
    }

    public function getCode(): string
    {
        return $this->getAttribute('code');
    }

    public function setCode(string $code): self
    {
        return $this->setAttribute('code', $code);
    }

    public function getFullName(): string
    {
        return $this->getAttribute('full_fame');
    }

    public function setFullName(string $fullName): self
    {
        return $this->setAttribute('full_name', $fullName);
    }

    public function getCompany(): ?string
    {
        return $this->getAttribute('company');
    }

    public function setCompany(?string $company): self
    {
        return $this->setAttribute('company', $company);
    }

    public function getEmail(): string
    {
        return $this->getAttribute('email');
    }

    public function setEmail(?string $email): self
    {
        return $this->setAttribute('email', $email);
    }

    public function getPhone(): ?string
    {
        return $this->getAttribute('phone');
    }

    public function setPhone(?string $phone): self
    {
        return $this->setAttribute('phone', $phone);
    }

    public function getRemark(): ?string
    {
        return $this->getAttribute('remark');
    }

    public function setRemark(?string $remark): self
    {
        return $this->setAttribute('remark', $remark);
    }

    public function getCashDeskOrder(): bool
    {
        return (bool) $this->getAttribute('cash_desk_order');
    }

    public function setCashDeskOrder(bool $cashDeskOrder): self
    {
        return $this->setAttribute('cash_desk_order', $cashDeskOrder);
    }

    public function getCustomerGuid(): ?string
    {
        return $this->getAttribute('customer_guid');
    }

    public function setCustomerGuid(?string $customerGuid): self
    {
        return $this->setAttribute('customer_guid', $customerGuid);
    }

    public function isPaid(): bool
    {
        return (bool) $this->getAttribute('paid');
    }

    public function setPaid(bool $paid): self
    {
        return $this->setAttribute('paid', $paid);
    }

    public function getForeignStatusId(): string
    {
        return $this->getAttribute('foreign_status_id');
    }

    public function setForeignStatusId(string $foreignStatusId): self
    {
        return $this->setAttribute('foreign_status_id', $foreignStatusId);
    }

    public function getSource(): string
    {
        return $this->getAttribute('source');
    }

    public function setSource(string $source): self
    {
        return $this->setAttribute('source', $source);
    }

    public function getVat(): float
    {
        return $this->getAttribute('vat');
    }

    public function setVat(float $vat): self
    {
        return $this->setAttribute('vat', $vat);
    }

    public function getToPay(): float
    {
        return $this->getAttribute('to_pay');
    }

    public function setToPay(float $toPay): self
    {
        return $this->setAttribute('to_pay', $toPay);
    }

    public function getCurrencyCode(): string
    {
        return $this->getAttribute('currency_code');
    }

    public function setCurrencyCode(string $currencyCode): self
    {
        return $this->setAttribute('currency_code', $currencyCode);
    }

    public function getWithVat(): float
    {
        return $this->getAttribute('with_vat');
    }

    public function setWithVat(float $withVat): self
    {
        return $this->setAttribute('with_vat', $withVat);
    }

    public function getWithoutVat(): float
    {
        return $this->getAttribute('without_vat');
    }

    public function setWithoutVat(float $withoutVat): self
    {
        return $this->setAttribute('without_vat', $withoutVat);
    }

    public function getExchangeRate(): float
    {
        return $this->getAttribute('exchange_rate');
    }

    public function setExchangeRate(float $exchangeRate): self
    {
        return $this->setAttribute('exchange_rate', $exchangeRate);
    }

    public function getPaymentMethod(): ?string
    {
        return $this->getAttribute('payment_method');
    }

    public function setPaymentMethod(?string $paymentMethod): self
    {
        return $this->setAttribute('payment_method', $paymentMethod);
    }

    public function getShipping(): ?string
    {
        return $this->getAttribute('shipping');
    }

    public function setShipping(?string $shipping): self
    {
        return $this->setAttribute('shipping', $shipping);
    }

    public function getAdminUrl(): string
    {
        return $this->getAttribute('admin_url');
    }

    public function setAdminUrl(string $adminUrl): self
    {
        return $this->setAttribute('admin_url', $adminUrl);
    }
}
