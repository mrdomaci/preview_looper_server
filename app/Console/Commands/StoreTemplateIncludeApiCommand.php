<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ClientServiceQueueStatusEnum;
use App\Exceptions\ApiRequestNonExistingResourceException;
use App\Helpers\ConnectorHelper;
use App\Helpers\LoggerHelper;
use App\Repositories\ClientServiceQueueRepository;
use Illuminate\Console\Command;
use Throwable;

class StoreTemplateIncludeApiCommand extends AbstractClientServiceCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:template-include {--client=} {--service=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store template includes to API';

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
        $clientServiceStatus = ClientServiceQueueStatusEnum::TEMPLATES;
        $clientServiceQueues = $this->clientServiceQueueRepository->getNext($clientServiceStatus, 5);
        if ($clientServiceQueues->isEmpty()) {
            $this->info('No client service in template queue');
            return Command::SUCCESS;
        }
        $success = true;
        foreach ($clientServiceQueues as $clientServiceQueue) {
            $clientService = $clientServiceQueue->clientService()->first();
            $clientService->setUpdateInProgress(true);
    
            try {
                // $body = ConnectorBodyHelper::getStringBodyForTemplateInclude(
                //     $clientService->service()->first(),
                //     $clientService->client()->first()
                // );
               // $templateIncludeResponse = ConnectorHelper::postTemplateInclude($clientService, $body);
                // if ($templateIncludeResponse->getTemplateIncludes() === []) {
                //     LoggerHelper::log('Template include failed for client service' . $clientService->getId());
                // }
                ConnectorHelper::deleteTemplateInclude($clientService);
                $clientServiceQueue->next();
            } catch (ApiRequestNonExistingResourceException) {
                $clientServiceQueue->next();
            } catch (Throwable $t) {
                $this->error('Error updating template for client service id: ' . $clientService->getId() . ' ' . $t->getMessage());
                LoggerHelper::log('Error updating template for client service id: ' . $clientService->getId() . ' ' . $t->getMessage());
                $clientService->setUpdateInProgress(false);
                $success = false;
            } finally {
                $clientService->setUpdateInProgress(false);
            }
            $this->info('Client service ' . $clientService->getId() . ' templates');
        }
       
        if ($success) {
            return Command::SUCCESS;
        }
        return Command::FAILURE;
    }
}
