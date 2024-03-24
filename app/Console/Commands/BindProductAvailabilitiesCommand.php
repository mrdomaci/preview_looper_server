<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Businesses\AvailabilityBusiness;
use App\Models\ClientService;
use App\Models\Service;
use App\Repositories\ClientServiceRepository;
use Illuminate\Console\Command;

class BindProductAvailabilitiesCommand extends AbstractClientCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bind:product-availabilities {--client=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bind products to availabilities';

    public function __construct(
        private readonly ClientServiceRepository $clientServiceRepository,
        private readonly AvailabilityBusiness $availabilityBusiness,
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
        $lastClientServiceId = 0;
        for ($i = 0; $i < $this->getMaxIterationCount(); $i++) {
            $clientServices = $this->clientServiceRepository->getActive(
                $lastClientServiceId,
                Service::getUpsell(),
                $this->findClient(),
                $this->getIterationCount(),
            );

            /** @var ClientService $clientService */
            foreach ($clientServices as $clientService) {
                $this->availabilityBusiness->bindProductAvailabilities($clientService);
            }
        }

        return Command::SUCCESS;
    }
}
