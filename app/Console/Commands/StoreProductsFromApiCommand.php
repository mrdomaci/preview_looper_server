<?php

namespace App\Console\Commands;

use App\Enums\ClientStatusEnum;
use App\Helpers\ConnectorHelper;
use App\Helpers\LoggerHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\TokenHelper;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Console\Command;
use Throwable;

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
        $success = true;
        $clients = Client::where('status', ClientStatusEnum::ACTIVE)->whereDate('last_synced_at', '>=', $yesterday)->get();
        /** @var Client $client */
        foreach ($clients as $client) {
            $this->info('Updating products for client id:' . $client->getAttribute('id'));
            $clientId = $client->getAttribute('id');
            
            for ($page = 1; $page < ResponseHelper::MAXIMUM_ITERATIONS; $page++) { 
                try {
                    $productResponses = ConnectorHelper::getProducts($client, $page);
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
                    if (count($productResponses) < ResponseHelper::MAXIMUM_ITEMS_PER_PAGE) {
                        break;
                    }
                } catch (Throwable $t) {
                    $this->error('Error updating products ' . $t->getMessage());
                    LoggerHelper::log('Error updating products ' . $t->getMessage());
                    $success = false;
                    break;
                }
            }
        }
        if ($success === true) {
            return Command::SUCCESS;
        } else {
            return Command::FAILURE;
        }
    }
}
