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
        $yesterday = now()->subDay();
        $clients = Client::where('status', ClientStatusEnum::ACTIVE)->whereDate('last_synced_at', '<=', $yesterday)->get();
        /** @var Client $client */
        foreach ($clients as $client) {
            $this->info('Updating images for client ' . $client->getAttribute('eshop_name'));
            $clientId = $client->getAttribute('id');
            $products = Product::where('client_id', $clientId)->where('active', true)->get();
            foreach($products as $product) {
                $productGuid = $product->getAttribute('guid');
                $productId = $product->getAttribute('id');
                $imageResponses = ConnectorHelper::getProductImages($client, $productGuid);
                $images = Image::where('client_id', $clientId)->where('product_id', $productId)->get();
                foreach ($imageResponses as $imageResponse) {
                    $this->info('Updating image ' . $imageResponse->getName() . ' for product ' . $productGuid);
                    $imageExists = false;
                    foreach ($images as $key => $image) {
                        if ($image->getAttribute('name') === $imageResponse->getCdnName()) {
                            unset($images[$key]);
                            $imageExists = true;
                            break;
                        }
                    }
                    if ($imageExists) {
                        continue;
                    }
                    $image = new Image();
                    $image->setAttribute('client_id', $clientId);
                    $image->setAttribute('product_id', $productId);
                    $image->setAttribute('name', $imageResponse->getCdnName());
                    $image->save();
                }
                foreach ($images as $image) {
                    Image::destroy($image->getAttribute('id'));
                }
            }
            $client->setAttribute('last_synced_at', now());
            $client->save();
        }
        return Command::SUCCESS;
    }
}
