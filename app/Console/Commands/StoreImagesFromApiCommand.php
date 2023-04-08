<?php

namespace App\Console\Commands;

use App\Enums\ClientStatusEnum;
use App\Models\Client;
use Illuminate\Console\Command;

class StoreImagesFromApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:images';

    /**
     *
     * @var string
     */
    protected $description = 'Store images from API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $clients = Client::where('status', ClientStatusEnum::ACTIVE)->get();
        foreach ($clients as $client) {
            $this->info('Updating images for client ' . $client->getAttribute('eshop_name'));
        }
        return Command::SUCCESS;
    }
}
