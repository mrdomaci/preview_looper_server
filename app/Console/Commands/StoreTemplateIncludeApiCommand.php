<?php

namespace App\Console\Commands;

use App\Enums\ClientServiceStatusEnum;
use App\Helpers\ConnectorBodyHelper;
use App\Helpers\ConnectorHelper;
use App\Helpers\LoggerHelper;
use App\Models\ClientService;
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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $success = true;
        for ($i = 0; $i < $this->getMaxIterationCount(); $i++) {
            $clientServices = ClientService::where('status', ClientServiceStatusEnum::ACTIVE)
                ->limit($this->getIterationCount())
                ->offset($this->getOffset($i))
                ->get();
            /** @var ClientService $clientService */
            foreach ($clientServices as $clientService) {
                $client = $clientService->client()->first();
                $service = $clientService->service()->first();
                try {
                    $body = ConnectorBodyHelper::getStringBodyForTemplateInclude($service, $client);
                    $templateIncludeResponse = ConnectorHelper::postTemplateInclude($clientService, $body);
                    if ($templateIncludeResponse->getTemplateIncludes() === []) {
                        LoggerHelper::log('Template include failed for client ' . $client->getAttribute('eshop_id'));
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
