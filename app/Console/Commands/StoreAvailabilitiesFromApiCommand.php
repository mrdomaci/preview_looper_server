<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Businesses\AvailabilityBusiness;
use App\Exceptions\AddonNotInstalledException;
use App\Exceptions\AddonSuspendedException;
use App\Helpers\ConnectorHelper;
use App\Models\ClientService;
use App\Repositories\ClientServiceRepository;
use Illuminate\Console\Command;
use Throwable;

class StoreAvailabilitiesFromApiCommand extends AbstractClientCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:availabilities {--client=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store availabilities from API';

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
        $lastClientId = 0;
        for ($i = 0; $i < $this->getMaxIterationCount(); $i++) {
            $clientServices = $this->clientServiceRepository->getActive(
                $lastClientId,
                null,
                $this->findClient(),
                $this->getIterationCount(),
            );
            /** @var ClientService $clientService */
            foreach ($clientServices as $clientService) {
                try {
                    $clientService->setUpdateInProgress(true);
                    $lastClientId = $clientService->getId();
                    $this->availabilityBusiness->createOrUpdateFromResponse($clientService, ConnectorHelper::getAvailabilities($clientService));
                    $this->info('Updating availabilities for client id:' . (string) $clientService->getClientId());
                    $clientService->setUpdateInProgress(false);
                } catch (AddonNotInstalledException $e) {
                    $clientService->setStatusDeleted();
                } catch (AddonSuspendedException $e) {
                    $clientService->setStatusInactive();
                } catch (Throwable $e) {
                    $this->error($e->getMessage());
                }
            }

            if (count($clientServices) < $this->getIterationCount()) {
                break;
            }
        }
        return Command::SUCCESS;
    }
}
