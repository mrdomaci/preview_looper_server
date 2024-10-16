<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Helpers\CacheHelper;
use App\Models\ClientService;
use App\Models\Service;
use App\Repositories\ClientServiceRepository;
use DateTime;
use Illuminate\Console\Command;

class StoreCachedResponseCommand extends AbstractClientServiceCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:cached:response {--client=} {--service=}';

    /** @var string */
    protected $description = 'Store cached response';

    public function __construct(
        private readonly ClientServiceRepository $clientServiceRepository,
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
        $now = new DateTime();
        for ($i = 0; $i < $this->getMaxIterationCount(); $i++) {
            $clientServices = $this->clientServiceRepository->getActive(
                $lastClientServiceId,
                Service::getDynamicPreviewImages(),
                $this->findClient(),
                $this->getIterationCount(),
            );
            /** @var ClientService $clientService  */
            foreach ($clientServices as $clientService) {
                $client = $clientService->client()->first();
                CacheHelper::imageResponse($client);
                $lastClientServiceId = $clientService->getId();
                $clientService->setSyncedAt($now);
                $clientService->save();
                $this->info('Client ' . $client->getId() . ' updated');
            }
            if (count($clientServices) < $this->getIterationCount()) {
                break;
            }
        }
        return Command::SUCCESS;
    }
}
