<?php

declare(strict_types=1);

namespace App\Connector\Fio;

use DateTime;

class LicenseListResponse
{

    /**
     * @param string $accountId
     * @param string $bankId
     * @param string $currency
     * @param string $iban
     * @param string $bic
     * @param float $openingBalance
     * @param float $closingBalance
     * @param DateTime $dateStart
     * @param DateTime $dateEnd
     * @param array<LicenseResponse> $transactions
     */
    public function __construct(
        public string $accountId,
        public string $bankId,
        public string $currency,
        public string $iban,
        public string $bic,
        public float $openingBalance,
        public float $closingBalance,
        public DateTime $dateStart,
        public DateTime $dateEnd,
        public array $transactions = [],
    ) {
    }

    public function addTransaction(LicenseResponse $transaction): void
    {
        $this->transactions[] = $transaction;
    }
}
