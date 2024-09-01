<?php

declare(strict_types=1);

namespace App\Connector\Fio;

use DateTime;

class LicenseListResponse
{
    public function __construct(
        public string $accountNumber,
        public string $bankCode,
        public string $currency,
        public string $iban,
        public string $bic,
        public float $openingBalance,
        public float $closingBalance,
        public DateTime $dateStart,
        public DateTime $dateEnd,
        public int $yearList,
        public int $idList,
        public int $idFrom,
        public int $idTo,
        public int $idLastDownload,
        public array $transactions,
    ) {
    }
}
