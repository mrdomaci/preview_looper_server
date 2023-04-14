<?php

namespace App\Console\Commands;

use App\Connector\Response;
use App\Enums\ClientStatusEnum;
use App\Helpers\ConnectorHelper;
use App\Helpers\TokenHelper;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Console\Command;

class StoreProductsFromApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store products from API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $clients = Client::where('status', ClientStatusEnum::ACTIVE)->get();
        foreach ($clients as $client) {
            $this->info('Updating products for client ' . $client->getAttribute('eshop_name'));
            $clientId = $client->getAttribute('id');
            $apiAccessToken = TokenHelper::getApiAccessToken($client);
            $productResponses = ConnectorHelper::getProducts($apiAccessToken);
            foreach ($productResponses as $productResponse) {
                $this->info('Updating product ' . $productResponse->getGuid());
                $product = Product::where('guid', $productResponse->getGuid())->where('client_id', $clientId)->first();
                if ($product === null) {
                    $product = new Product();
                    $product->setAttribute('guid', $productResponse->getGuid());
                    $product->setAttribute('client_id', $clientId);
                    $product->setAttribute('active', true);
                    $product->save();
                } else if ($product->getAttribute('active') === false) {
                    $product->setAttribute('active', true);
                    $product->save();
                }
            }
        }
        return Command::SUCCESS;
    }
}
