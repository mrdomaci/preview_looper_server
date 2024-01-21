<?php

namespace App\Console\Commands;

use App\Businesses\ClientServiceBusiness;
use App\Exceptions\AddonNotInstalledException;
use App\Exceptions\AddonSuspendedException;
use App\Helpers\ConnectorHelper;
use App\Models\ClientService;
use App\Repositories\ClientRepository;
use App\Repositories\ClientServiceRepository;
use Illuminate\Console\Command;
use Throwable;

class StoreClientsFromApiCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:clients {client_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store clients from API';

    public function __construct(
        private readonly ClientServiceRepository $clientServiceRepository,
        private readonly ClientServiceBusiness $clientServiceBusiness,
        private readonly ClientRepository $clientRepository,
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $clientId = $this->argument('client_id');
        if ($clientId !== null) {
            $clientId = (int) $clientId;
        }

        $lastClientId = 0;
        $dateLastSync = now()->subHours(24);
        for ($i = 0; $i < $this->getMaxIterationCount(); $i++) {
            $clientServices = $this->clientServiceRepository->getActive(
                $lastClientId,
                null,
                $clientId,
                $this->getIterationCount(),
            );
            /** @var ClientService $clientService */
            foreach ($clientServices as $clientService) {
                if ($this->clientServiceBusiness->isForbidenToUpdate($clientService, $dateLastSync)) {
                    continue;
                }
                try {
                    $this->clientRepository->updateFromResponse($clientService, ConnectorHelper::getEshop($clientService));
                    $this->info('Updating client id:' . (string) $clientService->getAttribute('client_id'));
                } catch (AddonNotInstalledException $e) {
                    $clientService->setStatusDeleted();
                } catch (AddonSuspendedException $e) {
                    $clientService->setStatusInactive();
                } catch (Throwable $e) {
                    $this->error($e->getMessage());
                } finally {
                    $lastClientId = $clientService->getAttribute('id');
                }
            }

            if (count($clientServices) < $this->getIterationCount()) {
                break;
            }
        }
        return Command::SUCCESS;
    }
}
