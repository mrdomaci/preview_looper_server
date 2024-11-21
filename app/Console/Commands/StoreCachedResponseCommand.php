<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ClientServiceQueueStatusEnum;
use App\Helpers\CacheHelper;
use App\Helpers\LoggerHelper;
use App\Repositories\ClientServiceQueueRepository;
use Illuminate\Console\Command;
use Throwable;

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
        $clientServiceStatus = ClientServiceQueueStatusEnum::CACHE;
        $clientServiceQueues = $this->clientServiceQueueRepository->getNext($clientServiceStatus, 5);
        if ($clientServiceQueues->isEmpty()) {
            $this->info('No client service in cache queue');
            return Command::SUCCESS;
        }
        $success = true;
        foreach ($clientServiceQueues as $clientServiceQueue) {
            $clientService = $clientServiceQueue->clientService()->first();
            $clientService->setUpdateInProgress(true);
            $this->info('Client service ' . $clientService->getId() . ' cache product data started');
            try {
                CacheHelper::imageResponse($clientService->client()->first());
                $clientServiceQueue->next();
            } catch (Throwable $t) {
                $this->info('Error caching products for client service id: ' . $clientService->getId() . ' ' . $t->getMessage());
                LoggerHelper::log('Error caching products for client service id: ' . $clientService->getId() . ' ' . $t->getMessage());
                $clientService->setUpdateInProgress(false);
                $success = false;
            } finally {
                $clientService->setUpdateInProgress(false);
            }
            $this->info('Client service ' . $clientService->getId() . ' cached');
        }
        if ($success) {
            return Command::SUCCESS;
        }
        return Command::FAILURE;
    }
}
