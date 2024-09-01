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
        $licenseList = ConnectorHelper::getLicense($from, $to, 'CZK');

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

        $licenseList = ConnectorHelper::getLicense($from, $to, 'EUR');

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
