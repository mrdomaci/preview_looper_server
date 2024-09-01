<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Connector\Fio\LicenseResponse;
use App\Helpers\ConnectorHelper;
use App\Repositories\ClientRepository;
use App\Repositories\ClientServiceRepository;
use DateTime;
use Illuminate\Console\Command;

class StoreLicenseFromBankApiCommand extends AbstractClientServiceCommand
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
        private readonly ClientRepository $clientRepository,
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
        /** @var LicenseResponse $transaction */
        foreach ($licenseList->transactions as $transaction) {
            
        }
        return Command::SUCCESS;
    }
}
