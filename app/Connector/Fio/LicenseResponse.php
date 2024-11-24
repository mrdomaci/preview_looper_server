<?php

declare(strict_types=1);

namespace App\Connector\Fio;

use DateTime;

class LicenseResponse
{
    public ?string $foreignId = null;
    public DateTime $date;
    public float $amount = 0;
    public string $currency = 'CZK';
    public ?string $accountNumber = null;
    public ?string $accountName = null;
    public ?string $bankCode = null;
    public ?string $variableSymbol = null;
    public ?string $userIdentification = null;
    public ?string $type = null;
    public ?string $comment = null;
    public ?string $instructionId = null;

    /**
     * @param array{
     *     column0?: array{value: string|null},
     *     column1?: array{value: float|null},
     *     column2?: array{value: string|null},
     *     column3?: array{value: string|null},
     *     column5?: array{value: string|null},
     *     column7?: array{value: string|null},
     *     column8?: array{value: string|null},
     *     column10?: array{value: string|null},
     *     column14?: array{value: string|null},
     *     column17?: array{value: int|null},
     *     column22?: array{value: int|null},
     *     column25?: array{value: string|null},
     * } $data
     */
    public function __construct(array $data)
    {
        if (isset($data['column22']) && $data['column22'] !== null) {
            $this->foreignId = (string) $data['column22']['value'];
        }
        $this->date = new DateTime();
        if (isset($data['column0']) && $data['column0'] !== null) {
            $this->date = new DateTime($data['column0']['value']);
        }
        if (isset($data['column1']) && $data['column1'] !== null) {
            $this->amount = $data['column1']['value'];
        }
        $this->currency = $data['column14']['value'];
        if (isset($data['column2']) && $data['column2'] !== null) {
            $this->accountNumber = $data['column2']['value'];
        }
        $this->accountName = $data['column10']['value'];
        if (isset($data['column3']) && $data['column3'] !== null) {
            $this->bankCode = $data['column3']['value'];
        }
        $this->variableSymbol = $data['column5']['value'];
        if (isset($data['column7']) && isset($data['column7']['value'])) {
            $this->userIdentification = $data['column7']['value'];
        }
        $this->type = $data['column8']['value'];
        if (isset($data['column25']) && isset($data['column25']['value'])) {
            $this->comment = $data['column25']['value'];
        }
        if (isset($data['column17']) && isset($data['column17']['value'])) {
            $this->instructionId = (string) $data['column17']['value'];
        }
    }
}
