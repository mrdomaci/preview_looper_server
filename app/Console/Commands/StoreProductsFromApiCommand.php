<?php

namespace App\Console\Commands;

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
        $yesterday = now()->subDay();
        $clients = Client::where('status', ClientStatusEnum::ACTIVE)->whereDate('last_synced_at', '<=', $yesterday)->get();
        foreach ($clients as $client) {
            $this->info('Updating products for client ' . $client->getAttribute('eshop_name'));
            $clientId = $client->getAttribute('id');
            $apiAccessToken = $client->getAccessToken();
            $productResponses = ConnectorHelper::getProducts($apiAccessToken);
            $products = Product::where('client_id', $clientId)->where('active', true)->get();
            foreach ($productResponses as $productResponse) {
                $this->info('Updating product ' . $productResponse->getGuid());
                $productExists = false;
                foreach ($products as $key => $product) {
                    if ($product->getAttribute('guid') === $productResponse->getGuid()) {
                        unset($products[$key]);
                        $productExists = true;
                        break;
                    }
                }
                if ($productExists) {
                    continue;
                }
                $product = Product::where('guid', $productResponse->getGuid())->first();
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
            foreach ($products as $product) {
                $product->setAttribute('active', false);
                $product->save();
            }
        }
        return Command::SUCCESS;
    }
}
