<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ClientServiceQueueStatusEnum;
use App\Helpers\FileHelper;
use App\Models\ClientService;
use App\Repositories\ClientServiceRepository;
use App\Repositories\QueueRepository;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class FixClientQueueCommand extends AbstractClientServiceCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue-fix:client-service {--client=} {--service=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix stuck client service in queue';
    
    public function __construct(
        private readonly ClientServiceRepository $clientServiceRepository,
        private readonly QueueRepository $queueRepository,
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
        if ($this->findClient() !== null && $this->findService() !== null) {
            $clientServices = new Collection();
            $clientServices->add($this->clientServiceRepository->getByClientAndService($this->findClient(), $this->findService()));
        } else {
            $from = new DateTime((now()->subHours(12))->format('Y-m-d H:i:s'));
            $clientServices = $this->clientServiceRepository->getNextForUpdate($from, $this->findService(), 5);
        }
        /** @var Collection<ClientService> $clientServices */
        if ($clientServices->isEmpty()) {
            $this->info('No client services to fix');
            return Command::SUCCESS;
        }
        /** @var Collection<ClientService> $clientServices */
        foreach ($clientServices as $clientService) {
            $clientService->setQueueStatus(ClientServiceQueueStatusEnum::CLIENTS)
                ->setWebhookedAt(new DateTime())
                ->setUpdateInProgress(false);
            $this->queueRepository->deleteForClientService($clientService);
            FileHelper::clearFiles($clientService);
            $this->info('Client service ' . $clientService->getId() . ' added to queue');
        }
        return Command::SUCCESS;
    }
}
