<?php

namespace App\Console\Commands;

use App\Enums\ClientServiceStatusEnum;
use App\Helpers\CacheHelper;
use App\Models\Client;
use Illuminate\Console\Command;

class StoreCachedResponseCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:cached:response {client_id?}';

    /**
     *
     * @var string
     */
    protected $description = 'Store cached response';

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
                $update = false;
                $clientServices = $client->services()->first();
                if ($clientServices->getAttribute('update_in_process') === true) {
                    continue;
                }
                foreach ($clientServices->get() as $clientService) {
                    if($clientService->getAttribute('status') === ClientServiceStatusEnum::ACTIVE) {
                        $update = true;
                    }
                }
                if ($update === false) {
                    continue;
                }
                CacheHelper::imageResponse($client);
                $this->info('Client ' . $client->getAttribute('id') . ' updated');
            }
        }
        return Command::SUCCESS;
    }
}
