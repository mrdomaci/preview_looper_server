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

    public static function formatPrice(string $value, string $currencyCode): string
    {
        $currency = self::where('code', StringHelper::upper($currencyCode))->first();
        if ($currency === null) {
            return $value . ' ' . $currencyCode;
        }

        return str_replace('{price}', $value, $currency->getAttribute('format'));
    }

}
