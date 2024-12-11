<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Helpers\DateTimeHelper;
use App\Helpers\EmailHelper;
use App\Models\ClientService;
use App\Models\SettingsService;
use App\Repositories\ClientServiceRepository;
use App\Repositories\ClientSettingsServiceOptionRepository;
use App\Repositories\LicenseRepository;
use App\Repositories\OrderRepository;
use DateTime;
use Illuminate\Console\Command;

class LicenseValidateCommand extends AbstractClientServiceCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'validate:license {--client=} {--service=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set is_license_active flag in client service';

    public function __construct(
        private readonly ClientServiceRepository $clientServiceRepository,
        private readonly LicenseRepository $licenseRepository,
        private readonly OrderRepository $orderRepository,
        private readonly ClientSettingsServiceOptionRepository $clientSettingsServiceOptionRepository,
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
        $lastClientId = 0;
        $settigService = SettingsService::where('id', SettingsService::UPSELL_ORDERS)->first();
        for ($i = 0; $i < $this->getMaxIterationCount(); $i++) {
            $clientServices = $this->clientServiceRepository->getActive(
                $lastClientId,
                $this->findService(),
                $this->findClient(),
                $this->getIterationCount(),
            );
            /** @var ClientService $clientService */
            foreach ($clientServices as $clientService) {
                $client = $clientService->client()->first();
                $licenseActive = true;
                $license = $this->licenseRepository->getValidByClientService($clientService);
                if ($license === null) {
                    $date = DateTimeHelper::adjustDateToCurrentMonth(new DateTime($clientService->getCreatedAt()->format('Y-m-d')));
                    $orders = $this->orderRepository->getFromDate($client, $date);
                    $orderCount = $orders->count();
                    $this->clientSettingsServiceOptionRepository->updateOrCreate(
                        $client,
                        $settigService,
                        null,
                        (string) $orderCount
                    );
                    if ($orderCount > 50) {
                        $licenseActive = false;
                    }
                }
                if ($clientService->isLicenseActive() === true
                    && $licenseActive === false
                    && $this->clientSettingsServiceOptionRepository->getEasyUpsellSubscribed($client) === true
                    ) {
                    EmailHelper::licenseEasyUpsell($clientService);
                }
                $clientService->setLicenseActive($licenseActive);
                $lastClientId = $client->getId();
            }
            if ($clientServices->count() < $this->getIterationCount()) {
                break;
            }
        }
        return Command::SUCCESS;
    }
}
