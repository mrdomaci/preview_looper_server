<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Exceptions\AddonNotInstalledException;
use App\Exceptions\AddonSuspendedException;
use App\Helpers\ConnectorHelper;
use App\Models\ClientService;
use App\Repositories\ClientRepository;
use App\Repositories\ClientServiceRepository;
use Illuminate\Console\Command;
use Throwable;

class StoreClientsFromApiCommand extends AbstractClientServiceCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:clients {--client=} {--service=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store clients from API';

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
        $lastClientId = 0;
        for ($i = 0; $i < $this->getMaxIterationCount(); $i++) {
            $clientServices = $this->clientServiceRepository->list(
                $lastClientId,
                $this->findService(),
                $this->findClient(),
                $this->getIterationCount(),
            );
            /** @var ClientService $clientService */
            foreach ($clientServices as $clientService) {
                try {
                    $lastClientId = $clientService->getId();
                    $this->clientRepository->updateFromResponse($clientService, ConnectorHelper::getEshop($clientService));
                    $this->info('Updating client id:' . (string) $clientService->getClientId());
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
