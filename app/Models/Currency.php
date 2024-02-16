<?php

namespace App\Models;

use App\Helpers\StringHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'symbol',
        'code',
        'format',
    ];

    public function getSymbol(): string
    {
        return $this->getAttribute('symbol');
    }

    public function getCode(): string
    {
        return $this->getAttribute('code');
    }

    public function getFormat(): string
    {
        return $this->getAttribute('format');
    }

    public static function formatPrice(string $value, string $currencyCode): string
    {
        $currency = self::where('code', StringHelper::upper($currencyCode))->first();
        if ($currency === null) {
            return $value . ' ' . $currencyCode;
        }

        return str_replace('{price}', $value, $currency->getFormat());
    }

}
