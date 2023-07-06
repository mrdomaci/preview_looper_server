<?php

namespace App\Console\Commands;

use App\Enums\ClientStatusEnum;
use App\Helpers\ConnectorHelper;
use App\Helpers\LoggerHelper;
use App\Models\Client;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Console\Command;
use Throwable;

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
        $success = true;
        $clients = Client::where('status', ClientStatusEnum::ACTIVE)->whereDate('last_synced_at', '>=', $yesterday)->get();
        /** @var Client $client */
        foreach ($clients as $client) {
            $this->info('Updating images for client id:' . (string)$client->getAttribute('id'));
            $clientId = $client->getAttribute('id');
            $products = Product::where('client_id', $clientId)->where('active', true)->get();
            foreach($products as $product) {
                $productGuid = $product->getAttribute('guid');
                $productId = $product->getAttribute('id');
                try {
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
                } catch (Throwable $t) {
                    $this->error('Error updating images ' . $t->getMessage());
                    LoggerHelper::log('Error updating images ' . $t->getMessage());
                    $success = false;
                    break;
                }
            }
            $client->setAttribute('last_synced_at', now());
            $client->save();
        }
        if ($success === true) {
            return Command::SUCCESS;
        } else {
            return Command::FAILURE;
        }
    }
}
