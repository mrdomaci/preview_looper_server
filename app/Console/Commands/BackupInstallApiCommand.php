<?php

namespace App\Console\Commands;

use App\Enums\ClientServiceStatusEnum;
use App\Exceptions\AddonNotInstalledException;
use App\Exceptions\AddonSuspendedException;
use App\Helpers\ConnectorHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\WebHookHelper;
use App\Models\Client;
use App\Models\ClientService;
use Illuminate\Console\Command;
use Throwable;

class BackupInstallApiCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup install';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $clientServiceId = 0;
        for ($i = 0; $i < $this->getMaxIterationCount(); $i++) {
            $clientServices = ClientService::where('status', ClientServiceStatusEnum::ACTIVE)
                ->where('id', '>', $clientServiceId)
                ->limit($this->getIterationCount())
                ->get();
            foreach($clientServices as $clientService) {
                $client = $clientService->client()->first();
                $images = $client->images()->get();
                if (count($images) === 0) {
                    $clientService
                        ->setAttribute('date_last_synced', null)
                        ->setAttribute('update_in_process', false)
                        ->save();
                    
                    WebHookHelper::jenkinsWebhookClient($client->getAttribute('id'));
                    $this->info('Client ' . (string) $client->getAttribute('id') . ' webhooked to be updated');
                }
                $clientServiceId = $clientService->getAttribute('id');
            }
            if (count($clientServices) < $this->getIterationCount()) {
                break;
            }
        }
        return Command::SUCCESS;
    }
}
