<?php

namespace App\Console\Commands;

use App\Enums\ClientServiceStatusEnum;
use App\Helpers\ConnectorHelper;
use App\Helpers\LoggerHelper;
use App\Helpers\ResponseHelper;
use App\Models\ClientService;
use App\Models\Image;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Throwable;

class StoreImagesFromApiCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:images {client_id?}';

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
        $clientId = $this->argument('client_id');
        $success = true;
        $service = Service::find(Service::DYNAMIC_PREVIEW_IMAGES);

        for($i = 0; $i < $this->getMaxIterationCount(); $i++) {

            if ($clientId !== null) {
                $clientServices = ClientService::where('service_id', $service->getAttribute('id'))
                    ->where('status', ClientServiceStatusEnum::ACTIVE)
                    ->where('client_id', $clientId)
                    ->limit($this->getIterationCount())
                    ->offset($this->getOffset($i))
                    ->get();
            } else {
                $clientServices = ClientService::where('service_id', $service->getAttribute('id'))
                    ->where('status', ClientServiceStatusEnum::ACTIVE)
                    ->limit($this->getIterationCount())
                    ->offset($this->getOffset($i))
                    ->get();
            }

            /** @var ClientService $clientService */
            foreach ($clientServices as $clientService) {
                $client = $clientService->client()->first();
                $currentClientId = $client->getAttribute('id');
                $this->info('Updating images for client id:' . (string)$currentClientId);
                $eshopName = $client->getAttribute('eshop_name');
                $products = Product::where('client_id', $currentClientId)->where('active', true)->get();
                foreach($products as $product) {
                    $productGuid = $product->getAttribute('guid');
                    $productId = $product->getAttribute('id');
                    try {
                        $imageResponses = ConnectorHelper::getProductImages($clientService, $productGuid);
                        $images = Image::where('client_id', $clientId)->where('product_id', $productId)->get();
                        foreach ($imageResponses as $imageResponse) {
                            // $imageUrl = ResponseHelper::getUImageURL($eshopName, $imageResponse->getName());
                            // $request = Http::get($imageUrl);
                            // if ($request->status() !== 200) {
                            //     continue;
                            // }
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
                            $image->setAttribute('client_id', $currentClientId);
                            $image->setAttribute('product_id', $productId);
                            $image->setAttribute('name', $imageResponse->getSeoName());
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
            
            if ($clientServices->count() < $this->getIterationCount()) {
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
