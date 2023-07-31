<?php

namespace App\Console\Commands;

use App\Enums\ClientServiceStatusEnum;
use App\Models\Client;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Console\Command;

class DeleteProductsApiCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:products {client_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete products for inactive clients';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $success = true;
        $clientId = $this->argument('client_id');

        for ($i = 0; $i < $this->getMaxIterationCount(); $i++) {
            if ($clientId !== null) {
                $client = Client::where('id', $clientId)->first();
                $clients = [$client];
            } else {
                $clients = Client::limit($this->getIterationCount())
                    ->offset($this->getOffset($i))
                    ->get();
            }
            /** @var Client $client */
            foreach ($clients as $client) {
                $delete = false;
                $clientService = null;
                $clientServices = $client->services();
                foreach ($clientServices->get() as $clientService) {
                    if ($clientService->getAttribute('status') === ClientServiceStatusEnum::INACTIVE) {
                        $delete = true;
                        break;
                    }
                }
                if ($delete === false) {
                    continue;
                }
                if ($clientService === null) {
                    continue;
                }
                Product::where('client_id', $client->getAttribute('id'))->delete();
                Image::where('client_id', $client->getAttribute('id'))->delete();
            }

            if (count($clients) < $this->getIterationCount()) {
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
