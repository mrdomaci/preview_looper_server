<?php

declare(strict_types=1);

namespace App\Connector\Fio;

class Transaction
{
    public string $foreignId;
    public string $date;
    public float $amount;
    public string $currency;
    public string $accountNumber;
    public string $accountName;
    public string $bankCode;
    public string $variableSymbol;
    public string $userIdentification;
    public string $type;
    public string $comment;
    public string $instructionId;

    /**
     * @param array{
     *     column0: array{value: string},
     *     column1: array{value: float},
     *     column2: array{value: string},
     *     column3: array{value: string},
     *     column5: array{value: string},
     *     column7: array{value: string},
     *     column8: array{value: string},
     *     column10: array{value: string},
     *     column14: array{value: string},
     *     column17: array{value: string},
     *     column22: array{value: string},
     *     column25: array{value: string}
     * } $data
     */
    public function __construct(array $data)
    {
        $this->foreignId = $data['column22']['value'];
        $this->date = $data['column0']['value'];
        $this->amount = $data['column1']['value'];
        $this->currency = $data['column14']['value'];
        $this->accountNumber = $data['column2']['value'];
        $this->accountName = $data['column10']['value'];
        $this->bankCode = $data['column3']['value'];
        $this->variableSymbol = $data['column5']['value'];
        $this->userIdentification = $data['column7']['value'];
        $this->type = $data['column8']['value'];
        $this->comment = $data['column25']['value'];
        $this->instructionId = $data['column17']['value'];
    }
}
