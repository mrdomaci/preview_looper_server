<?php

namespace App\Console\Commands;

use App\Helpers\CacheHelper;
use App\Models\ClientService;
use App\Models\Service;
use App\Repositories\ClientServiceRepository;
use Illuminate\Console\Command;

class StoreCachedResponseCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:cached:response {client_id?}';

    /**
     *
     * @var string
     */
    protected $description = 'Store cached response';

    public function __construct(
        private readonly ClientServiceRepository $clientServiceRepository,
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
        $lastClientServiceId = 0;
        for ($i = 0; $i < $this->getMaxIterationCount(); $i++) {
            $clientServices = $this->clientServiceRepository->getActive(
                $lastClientServiceId,
                Service::getDynamicPreviewImages(),
                $clientId,
                $this->getIterationCount(),
            );
            /** @var ClientService $clientService  */
            foreach ($clientServices as $clientService) {
                $client = $clientService->client()->first();
                CacheHelper::imageResponse($client);
                $lastClientServiceId = $clientService->getId();
                $this->info('Client ' . $client->getId() . ' updated');
            }
            if (count($clientServices) < $this->getIterationCount()) {
                break;
            }
        }
        return Command::SUCCESS;
    }
}
