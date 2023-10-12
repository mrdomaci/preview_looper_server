<?php

namespace App\Console\Commands;

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
        $client = Client::find($clientId);
        if($client === null) {
            return Command::FAILURE;
        }
        if (CacheHelper::imageResponse($client)) {
            return Command::SUCCESS;
        } else {
            return Command::FAILURE;
        }
    }
}
