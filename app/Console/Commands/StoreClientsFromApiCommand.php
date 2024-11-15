<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ClientServiceQueueStatusEnum;
use App\Exceptions\AddonNotInstalledException;
use App\Exceptions\AddonSuspendedException;
use App\Helpers\ConnectorHelper;
use App\Helpers\LoggerHelper;
use App\Repositories\ClientRepository;
use App\Repositories\ClientServiceQueueRepository;
use Illuminate\Console\Command;
use Throwable;

class StoreClientsFromApiCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:clients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store clients from API';

    public function __construct(
        private readonly ClientRepository $clientRepository,
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
        $clientServiceStatus = ClientServiceQueueStatusEnum::CLIENTS;
        $clientServiceQueue = $this->clientServiceQueueRepository->getNext($clientServiceStatus);
        if ($clientServiceQueue === null) {
            $this->info('No client service in client queue');
            return Command::SUCCESS;
        }
        $clientService = $clientServiceQueue->clientService()->first();
        $clientService->setUpdateInProgress(true);
        try {
            $this->clientRepository->updateFromResponse($clientService, ConnectorHelper::getEshop($clientService));
            $clientServiceQueue->next();
            $this->info('Client service ' . $clientService->getId() . ' client data updated');
        } catch (AddonNotInstalledException $e) {
            $clientService->setStatusDeleted();
        } catch (AddonSuspendedException $e) {
            $clientService->setStatusInactive();
        } catch (Throwable $e) {
            $this->error($e->getMessage());
            LoggerHelper::log('Error updating client for client service id: ' . $clientService->getId() . ' ' . $e->getMessage());
            return Command::FAILURE;
        }

        $clientService->setUpdateInProgress(false);
        $this->info('Client service ' . $clientService->getId() . ' client updated');
        return Command::SUCCESS;
    }
}
