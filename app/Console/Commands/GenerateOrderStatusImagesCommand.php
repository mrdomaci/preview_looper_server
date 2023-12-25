<?php

namespace App\Console\Commands;

use App\Helpers\CacheHelper;
use App\Helpers\ImageHelper;
use App\Models\Client;
use App\Models\ClientSettingsServiceOption;
use Illuminate\Console\Command;

class GenerateOrderStatusImagesCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:order-status:images {client_id?}';

    /**
     *
     * @var string
     */
    protected $description = 'Generate order status images';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $clientId = $this->argument('client_id');
        for ($i = 0; $i < $this->getMaxIterationCount(); $i++) {
            if ($clientId !== null) {
                $clients = Client::where('id', $clientId)->get();
            } else {
                $clients = Client::limit($this->getIterationCount())
                    ->offset($this->getOffset($i))
                    ->get();
            }
            foreach ($clients as $client) {
                $clientSettingsServiceOptions = ClientSettingsServiceOption::where('client_id', (int)$client->getAttribute('id'))
                    ->whereIn('settings_service_id', [7,8,9,10,11,12,13])
                    ->get();
                foreach ($clientSettingsServiceOptions as $clientSettingsServiceOption) {
                    if ($clientSettingsServiceOption->getAttribute('settings_service_option_id') === null) {
                        continue;
                    }
                    $result = ImageHelper::orderStatus($client, $clientSettingsServiceOption);
                    if ($result === false) {
                        $this->error('Client ' . $client->getAttribute('id') . ' order icon generation failed for ' . $clientSettingsServiceOption->getAttribute('settings_service_id'));
                    } else {
                        $this->info('Client ' . $client->getAttribute('id') . ' order icon generatedfor ' . $clientSettingsServiceOption->getAttribute('settings_service_id'));
                    }
                }
            }
            if (count($clients) < $this->getIterationCount()) {
                break;
            }
        }
        return Command::SUCCESS;
    }
}
