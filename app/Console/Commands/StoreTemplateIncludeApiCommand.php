<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Helpers\ConnectorBodyHelper;
use App\Helpers\ConnectorHelper;
use App\Helpers\LoggerHelper;
use App\Models\ClientService;
use App\Repositories\ClientServiceRepository;
use Illuminate\Console\Command;
use Throwable;

class StoreTemplateIncludeApiCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:template-include';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store template includes to API';

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
        $success = true;
        $lastClientServiceId = 0;
        for ($i = 0; $i < $this->getMaxIterationCount(); $i++) {
            $clientServices = $this->clientServiceRepository->getActive(
                $lastClientServiceId,
                null,
                null,
                $this->getIterationCount(),
            );
            /** @var ClientService $clientService */
            foreach ($clientServices as $clientService) {
                $lastClientServiceId = $clientService->getId();
                $client = $clientService->client()->first();
                $this->info('Updating templates for client ' . $client->getId());
                $service = $clientService->service()->first();
                try {
                    $body = ConnectorBodyHelper::getStringBodyForTemplateInclude($service, $client);
                    $this->info('Template include body: ' . $body);
                    $templateIncludeResponse = ConnectorHelper::postTemplateInclude($clientService, $body);
                    if ($templateIncludeResponse->getTemplateIncludes() === []) {
                        LoggerHelper::log('Template include failed for client ' . $client->getEshopId());
                    }
                } catch (Throwable $t) {
                    $this->error('Error updating client ' . $t->getMessage());
                    LoggerHelper::log('Error updating client ' . $t->getMessage());
                    $success = false;
                }
            }

            if (count($clientServices) < $this->getIterationCount()) {
                break;
            }
        }
        if ($success === true) {
            return Command::SUCCESS;
        } else {
            return Command::FAILURE;
        }
    }
}
