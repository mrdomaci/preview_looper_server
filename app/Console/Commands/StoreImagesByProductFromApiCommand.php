<?php

namespace App\Console\Commands;

use App\Enums\ClientServiceStatusEnum;
use App\Exceptions\ApiRequestNonExistingResourceException;
use App\Helpers\ConnectorHelper;
use App\Models\Client;
use App\Models\ClientService;
use App\Models\Image;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Console\Command;
use Throwable;

class StoreImagesByProductFromApiCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:images:guids {client_id?} {guids?}';

    /**
     *
     * @var string
     */
    protected $description = 'Store images by products GUID from API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $clientId = $this->argument('client_id');
        if ($clientId === null) {
            $this->error('Client ID not set');
            return Command::FAILURE;
        }
        $client = Client::where('id', $clientId)->first();
        if ($client === null) {
            $this->error('Client not found');
            return Command::FAILURE;
        }
        $productGUIDs = $this->argument('guids');
        if ($productGUIDs === null) {
            $this->error('Product GUIDs not set');
            return Command::FAILURE;
        }

        $service = Service::find(Service::DYNAMIC_PREVIEW_IMAGES);
        $clientService = ClientService::where('service_id', $service->getAttribute('id'))
            ->where('status', ClientServiceStatusEnum::ACTIVE)
            ->where('client_id', $clientId)
            ->first();

        if ($clientService === null) {
            $this->error('Client service not found');
            return Command::FAILURE;
        }

        $guids = explode('|', $productGUIDs);
        $products = Product::whereIn('guid', $guids)->where('client_id', $client->getAttribute('id'))->where('active', true)->get();
        if (count($products) === 0) {
            $this->error('Products not found');
            return Command::FAILURE;
        }

        $success = true;
        foreach($products as $product) {
            $productGuid = $product->getAttribute('guid');
            $productId = $product->getAttribute('id');
            try {
                $imageResponses = ConnectorHelper::getProductImages($clientService, $productGuid);
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
                    $image->setAttribute('name', $imageResponse->getSeoName());
                    $image->save();
                }
                foreach ($images as $image) {
                    Image::destroy($image->getAttribute('id'));
                }
            } catch (ApiRequestNonExistingResourceException $t) {
                $this->error('Product ' . $productGuid . ' not found');
            } catch (Throwable $t) {
                $this->error('Error updating images ' . $t->getMessage());
                $success = false;
            }
        }

        if ($success === true) {
            return Command::SUCCESS;
        } else {
            return Command::FAILURE;
        }
    }
}
