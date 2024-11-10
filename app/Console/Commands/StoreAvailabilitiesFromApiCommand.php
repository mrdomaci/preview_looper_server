<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Businesses\AvailabilityBusiness;
use App\Enums\ClientServiceQueueStatusEnum;
use App\Exceptions\AddonNotInstalledException;
use App\Exceptions\AddonSuspendedException;
use App\Helpers\ConnectorHelper;
use App\Helpers\LoggerHelper;
use App\Repositories\ClientServiceQueueRepository;
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
        private readonly AvailabilityBusiness $availabilityBusiness,
        private readonly ClientServiceQueueRepository $clientServiceQueueRepository,
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
        $clientServiceStatus = ClientServiceQueueStatusEnum::AVAILABILITIES;
        $clientServiceQueue = $this->clientServiceQueueRepository->getNext($clientServiceStatus);
        if ($clientServiceQueue === null) {
            $this->info('No client service in availability queue');
            return Command::SUCCESS;
        }
        $clientService = $clientServiceQueue->clientService()->first();
        $clientService->setUpdateInProgress(true);

        try {
            $this->availabilityBusiness->createOrUpdateFromResponse($clientService, ConnectorHelper::getAvailabilities($clientService));
            $clientServiceQueue->next();
            $this->info('Client service ' . $clientService->getId() . ' availability data updated');
        } catch (AddonNotInstalledException $e) {
            $clientService->setStatusDeleted();
        } catch (AddonSuspendedException $e) {
            $clientService->setStatusInactive();
        } catch (Throwable $e) {
            $this->error($e->getMessage());
            LoggerHelper::log('Error updating availabilities for client service id: ' . $clientService->getId() . ' ' . $e->getMessage());
            return Command::FAILURE;
        }
        $clientService->setUpdateInProgress(false);
        return Command::SUCCESS;
    }
}
