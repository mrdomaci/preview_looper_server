<?php

declare(strict_types=1);

namespace App\Connector\Fio;

use DateTime;

class Response
{
    /**
     * @param array{
     *     info?: array{
     *         accountId: string,
     *         bankId: string,
     *         currency: string,
     *         iban: string,
     *         bic: string,
     *         openingBalance: float,
     *         closingBalance: float,
     *         dateStart: string,
     *         dateEnd: string
     *     },
     *     transactionList?: array{
     *         transaction: array<array{
     *             column0: array{value: string},
     *             column1: array{value: float},
     *             column2?: array{value: string},
     *             column3?: array{value: string},
     *             column5: array{value: string},
     *             column7: array{value: string},
     *             column8: array{value: string},
     *             column10: array{value: string|null},
     *             column14: array{value: string},
     *             column17: array{value: string},
     *             column22: array{value: string},
     *             column25: array{value: string}
     *         }>
     *     }
     * } $data
     */
    public function __construct(
        private array $data
    ) {
    }
    public function getLicense(): ?LicenseListResponse
    {
        $licenseListResponse = null;
        if (isset($this->data['info'])) {
            $licenseListResponse = new LicenseListResponse(
                $this->data['info']['accountId'],
                $this->data['info']['bankId'],
                $this->data['info']['currency'],
                $this->data['info']['iban'],
                $this->data['info']['bic'],
                (float) $this->data['info']['openingBalance'],
                (float) $this->data['info']['closingBalance'],
                new DateTime($this->data['info']['dateStart']),
                new DateTime($this->data['info']['dateEnd']),
            );
        }
        if (isset($this->data['transactionList'])) {
            foreach ($this->data['transactionList']['transaction'] as $transaction) {
                $licenseListResponse->addTransaction(new LicenseResponse($transaction));
            }
        }
        return $licenseListResponse;
    }
}
