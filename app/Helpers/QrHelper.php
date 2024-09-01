<?php

declare(strict_types=1);

namespace App\Helpers;

class QrHelper
{
    public static function requestPayment(int $size, string $iban, string $amount, string $variableSymbol, string $currency): string {
        $data = sprintf(
            'SPD*1.0*ACC:%s*AM:%s*CC:%s*X-VS:%s',
            urlencode($iban),
            urlencode($amount),
            urlencode($currency),
            urlencode($variableSymbol)
        );
    
        $baseUrl = 'https://api.qrserver.com/v1/create-qr-code/';
        
        $queryParams = http_build_query([
            'size' => $size . 'x' . $size,
            'data' => $data
        ]);
    
        $qrCodeUrl = $baseUrl . '?' . $queryParams;
    
        return $qrCodeUrl;
    }
}
