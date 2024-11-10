<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Repositories\ClientServiceQueueRepository;
use App\Repositories\ClientServiceRepository;
use DateTime;
use Illuminate\Console\Command;
use Throwable;

class CreateClientServiceQueueCommand extends AbstractClientServiceCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:client-service {--client=} {--service=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add client service to queue';
    
    public function __construct(
        private readonly ClientServiceRepository $clientServiceRepository,
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
        if ($this->findClient() !== null && $this->findService() !== null) {
            $clientService = $this->clientServiceRepository->getByClientAndService($this->findClient(), $this->findService());
        } else {
            try {
                $from = new DateTime((now()->subHours(12))->format('Y-m-d H:i:s'));
                $clientService = $this->clientServiceRepository->getNextForUpdate($from, $this->findService());
                $this->clientServiceQueueRepository->createOrIgnore($clientService);
                $this->info('Client service ' . $clientService->getId() . ' added to queue');
            } catch (Throwable) {
                $this->info('No clients to update for service ' . $this->findService()->getName());
                return Command::SUCCESS;
            }
        }
        $clientService->setWebhoodAt(new DateTime());
        $clientService->save();
        return Command::SUCCESS;
    }
}
