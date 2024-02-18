<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Helpers\WebHookHelper;
use App\Models\Service;
use App\Repositories\ClientServiceRepository;
use Illuminate\Console\Command;
use Throwable;

class WebhookUpdateByClientsCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:update:clients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update clients by webhook';

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
        $dateLastSync = now()->subHours(12);
        try {
            $clientService = $this->clientServiceRepository->getNextForUpdate(Service::getDynamicPreviewImages(), $dateLastSync);
        } catch (Throwable) {
            $this->info('No clients to update');
            return Command::SUCCESS;
        }
        if ($clientService === null) {
            $this->info('No clients to update');
            return Command::SUCCESS;
        }
        WebHookHelper::jenkinsWebhookUpdateClient($clientService->getClientId());
        $this->info('Client ' . (string) $clientService->getClientId() . ' webhooked to be updated');
        return Command::SUCCESS;
    }
}
