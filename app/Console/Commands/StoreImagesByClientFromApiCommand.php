<?php

namespace App\Console\Commands;

use App\Enums\ClientServiceStatusEnum;
use App\Exceptions\AddonNotInstalledException;
use App\Exceptions\ApiRequestNonExistingResourceException;
use App\Helpers\ConnectorHelper;
use App\Helpers\LoggerHelper;
use App\Models\ClientService;
use App\Models\Image;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Console\Command;
use Throwable;

class StoreImagesByClientFromApiCommand extends AbstractCommand
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
                $client = $clientService->client()->first(['id']);
                $currentClientId = $client->getAttribute('id');
                $this->info('Updating images for client id:' . (string)$currentClientId);
                $productOffsetId = 0;
                for ($j = 0; $j < $this->getMaxIterationCount(); $j++) {
                    $products = Product::where('client_id', $currentClientId)->where('active', true)->where('id', '>', $productOffsetId)->limit(10)->get(['id', 'guid']);
                    foreach($products as $product) {
                        $productGuid = $product->getAttribute('guid');
                        $productId = $product->getAttribute('id');
                        $productOffsetId = $productId;
                        try {
                            $imageResponses = ConnectorHelper::getProductImages($clientService, $productGuid);
                            $images = Image::where('client_id', $clientId)->where('product_id', $productId)->get(['id', 'name']);
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
                                $image->setAttribute('client_id', $currentClientId);
                                $image->setAttribute('product_id', $productId);
                                $image->setAttribute('name', $imageResponse->getSeoName());
                                $image->save();
                            }
                            foreach ($images as $image) {
                                Image::destroy($image->getAttribute('id'));
                            }
                        } catch (ApiRequestNonExistingResourceException $t) {
                            Product::destroy($productId);
                            $images = Image::where('client_id', $clientId)->where('product_id', $productId)->get(['id', 'name']);
                            foreach ($images as $image) {
                                Image::destroy($image->getAttribute('id'));
                            }
                            $this->error('Product ' . $productGuid . ' not found');
                        } catch (AddonNotInstalledException) {
                            $clientService->setAttribute('status', ClientServiceStatusEnum::INACTIVE);
                            $clientService->save();
                            break;
                        } catch (Throwable $t) {
                            $this->error('Error updating images ' . $t->getMessage());
                            LoggerHelper::log('Error updating images ' . $t->getMessage());
                            $success = false;
                            break;
                        }
                    }
                    unset($products);
                    unset($imageResponses);
                    unset($images);
                }
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
