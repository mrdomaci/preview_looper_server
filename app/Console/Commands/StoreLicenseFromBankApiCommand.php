<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Connector\Fio\LicenseResponse;
use App\Exceptions\BankVariableSymbolNotValidException;
use App\Helpers\ConnectorHelper;
use App\Repositories\ClientServiceRepository;
use App\Repositories\LicenseRepository;
use DateTime;
use Exception;
use Illuminate\Console\Command;

class StoreLicenseFromBankApiCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:license {--from=} {--to=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store license from bank API';

    public function __construct(
        private readonly ClientServiceRepository $clientServiceRepository,
        private readonly LicenseRepository $licenseRepository,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $from = new DateTime($this->option('from') . ' 00:00:00');
        $to = new DateTime($this->option('to') . ' 23:59:59');
        $licenseList = ConnectorHelper::getLicense($from, $to);
        $data = [
            "column22" => [
                "value" => 23774034977,
                "name" => "ID pohybu",
                "id" => 22,
            ],
            "column0" => [
                "value" => "2021-06-04+0200",
                "name" => "Datum",
                "id" => 0,
            ],
            "column1" => [
                "value" => 490.0,
                "name" => "Objem",
                "id" => 1,
            ],
            "column14" => [
                "value" => "CZK",
                "name" => "Měna",
                "id" => 14,
            ],
            "column2" => [
                "value" => "1021432544",
                "name" => "Protiúčet",
                "id" => 2,
            ],
            "column10" => [
                "value" => "Jan Marek Slabihoud",
                "name" => "Název protiúčtu",
                "id" => 10,
            ],
            "column3" => [
                "value" => "6100",
                "name" => "Kód banky",
                "id" => 3,
            ],
            "column12" => null,
            "column4" => null,
            "column5" => [
                "value" => "23000183",
                "name" => "VS",
                "id" => 5,
            ],
            "column6" => null,
            "column7" => [
                "value" => "Jan Marek Slabihoud",
                "name" => "Uživatelská identifikace",
                "id" => 7,
            ],
            "column16" => null,
            "column8" => [
                "value" => "Bezhotovostní příjem",
                "name" => "Typ",
                "id" => 8,
            ],
            "column9" => null,
            "column18" => null,
            "column25" => [
                "value" => "Jan Marek Slabihoud",
                "name" => "Komentář",
                "id" => 25,
            ],
            "column26" => null,
            "column17" => [
                "value" => 29209193489,
                "name" => "ID pokynu",
                "id" => 17,
            ],
            "column27" => null
        ];
        
        $licenseList->addTransaction(new LicenseResponse($data));
        /** @var LicenseResponse $transaction */
        foreach ($licenseList->transactions as $transaction) {
            $clientService = $this->clientServiceRepository->getByVariableSymbol($transaction->variableSymbol);
            if ($clientService === null) {
                throw new BankVariableSymbolNotValidException(new Exception('Variable ' . $transaction->variableSymbol . ' not valid for client service pairing'));
            }
            $this->licenseRepository->updateOrCreate(
                $transaction->foreignId,
                $clientService,
                $transaction->amount,
                $transaction->currency,
                $transaction->accountNumber,
                $transaction->bankCode,
                $transaction->variableSymbol,
                null,
                null,
                $transaction->comment,
            );
        }
        return Command::SUCCESS;
    }
}
