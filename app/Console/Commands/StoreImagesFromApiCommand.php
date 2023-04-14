<?php

namespace App\Console\Commands;

use App\Enums\ClientStatusEnum;
use App\Helpers\ConnectorHelper;
use App\Helpers\TokenHelper;
use App\Models\Client;
use App\Models\Image;
use App\Models\Product;
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
            $clientId = $client->getAttribute('id');
            $products = Product::where('client_id', $clientId)->where('active', true)->get();
            $apiAccessToken = TokenHelper::getApiAccessToken($client);
            foreach($products as $product) {
                $productGuid = $product->getAttribute('guid');
                $productId = $product->getAttribute('id');
                $imageResponses = ConnectorHelper::getProductImages($apiAccessToken, $productGuid);
                foreach ($imageResponses as $imageResponse) {
                    $this->info('Updating image ' . $imageResponse->getName());
                    $image = Image::where('client_id', $clientId)->where('product_id', $productId)->where('name', $imageResponse->getName())->first();
                    if ($image === null) {
                        $image = new Image();
                        $image->setAttribute('client_id', $clientId);
                        $image->setAttribute('product_id', $productId);
                        $image->setAttribute('name', $imageResponse->getCdnName());
                        $image->save();
                    } else if ($imageResponse->getCdnName() !== $product->getAttribute('name')) {
                        $image->setAttribute('name', $imageResponse->getCdnName());
                        $image->save();
                    }
                }
            }
            $client->setAttribute('last_synced_at', now());
            $client->save();
        }
        return Command::SUCCESS;
    }
}
