<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Helpers\WebHookHelper;
use App\Repositories\ClientServiceRepository;
use Illuminate\Console\Command;
use Throwable;

class WebhookUpdateCommand extends AbstractServiceCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:update {service}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send webhook to update client for service if needed';

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
        try {
            $clientService = $this->clientServiceRepository->getNextForUpdate($this->getService(), now()->subHours(12));
        } catch (Throwable) {
            $this->info('No clients to update for service ' . $this->getService()->getName());
            return Command::SUCCESS;
        }
        WebHookHelper::webhookResolver($clientService);
        $this->info('Client ' . (string) $clientService->client->first()->getId() . ' webhooked to be updated for service ' . $this->getService()->getName());
        return Command::SUCCESS;
    }
}
