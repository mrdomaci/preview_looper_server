<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class License extends Model
{
    use HasFactory;

    protected $table = 'license';

    protected $fillable = [
        'client_service_id',
        'value',
        'currency',
        'valid_to',
        'is_active',
        'foreign_id',
        'account_number',
        'bank_code',
        'account_name',
        'bank_name',
        'bic',
        'variable_symbol',
        'specific_symbol',
        'constant_symbol',
        'note',
    ];

    protected $casts = [
        'valid_to' => 'datetime:Y-m-d',
        'is_active' => 'boolean',
    ];

    public function clientService()
    {
        return $this->belongsTo(ClientService::class);
    }

    public function getId(): int
    {
        return $this->getAttribute('id');
    }

    public function getClientServiceId(): int
    {
        return $this->getAttribute('client_service_id');
    }

    public function getValue(): float
    {
        return $this->getAttribute('value');
    }

    public function getCurrency(): string
    {
        return $this->getAttribute('currency');
    }

    public function getValidTo(): DateTime
    {
        return new DateTime($this->getAttribute('valid_to'));
    }

    public function getIsActive(): bool
    {
        return $this->getAttribute('is_active');
    }

    public function getForeignId(): string
    {
        return $this->getAttribute('foreign_id');
    }

    public function getVariableSymbol(): ?string
    {
        return $this->getAttribute('variable_symbol');
    }

    public function getSpecificSymbol(): ?string
    {
        return $this->getAttribute('specific_symbol');
    }

    public function getConstantSymbol(): ?string
    {
        return $this->getAttribute('constant_symbol');
    }

    public function getNote(): ?string
    {
        return $this->getAttribute('note');
    }

    public function getAccountNumber(): string
    {
        return $this->getAttribute('account_number');
    }

    public function getBankCode(): string
    {
        return $this->getAttribute('bank_code');
    }

    public function getAccountName(): string
    {
        return $this->getAttribute('account_name');
    }

    public function getBankName(): string
    {
        return $this->getAttribute('bank_name');
    }

    public function getBic(): string
    {
        return $this->getAttribute('bic');
    }

    public static function createOrSkip(
        string $foreignId,
        DateTime $applyDate,
        float $amount,
        string $currency,
        string $accountNumber,
        string $accountName,
        string $bankCode,
        string $bankName,
        string $constantSymbol,
        string $variableSymbol,
        string $specificSymbol,
        string $messageForRecipient,
        string $bic,
    ): ?self
    {
        try {
            $license = Self::where('foreign_id', $foreignId)->firstOrFail();
            return null;
        } catch (Throwable) {
            return Self::create([
                'client_service_id' => 1,
                'value' => $amount,
                'currency' => $currency,
                'valid_to' => $applyDate->format('Y-m-d'),
                'is_active' => true,
                'foreign_id' => $foreignId,
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
                'account_name' => $accountName,
                'bank_name' => $bankName,
                'bic' => $bic,
                'variable_symbol' => $variableSymbol,
                'specific_symbol' => $specificSymbol,
                'constant_symbol' => $constantSymbol,
                'note' => $messageForRecipient,
            ]);
        }
    }
}
