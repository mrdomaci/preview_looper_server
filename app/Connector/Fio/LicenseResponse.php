<?php

declare(strict_types=1);

namespace App\Connector\Fio;

use DateTime;

class LicenseResponse
{
    public function __construct(
        public string $foreignId,
        public DateTime $applyDate,
        public float $amount,
        public string $currency,
        public string $accountNumber,
        public string $accountName,
        public string $bankCode,
        public string $bankName,
        public string $constantSymbol,
        public string $variableSymbol,
        public string $specificSymbol,
        public string $messageForRecipient,
        public string $bic,
    ) {
    }
}
