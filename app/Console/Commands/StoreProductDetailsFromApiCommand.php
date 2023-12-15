<?php

namespace App\Console\Commands;

use App\Connector\ProductDetailResponse;
use App\Enums\ClientServiceStatusEnum;
use App\Exceptions\AddonNotInstalledException;
use App\Exceptions\ApiRequestNonExistingResourceException;
use App\Exceptions\ApiRequestTooManyRequestsException;
use App\Helpers\GeneratorHelper;
use App\Helpers\LoggerHelper;
use App\Helpers\PriceHelper;
use App\Models\ClientService;
use App\Models\Image;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Console\Command;
use Throwable;

class StoreProductDetailsFromApiCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:product:details {client_id?}';

    /**
     *
     * @var string
     */
    protected $description = 'Store product details from API';

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
                if ($clientService->getAttribute('date_last_synced') !== null &&
                    $clientService->getAttribute('date_last_synced') >= now()->subHours(12)) {
                    continue;
                }
                if ($clientService->getAttribute('update_in_process') === true) {
                    continue;
                }
                $clientService->setUpdateInProgress(true);
                $clientService->save();
                $client = $clientService->client()->first(['id']);
                $currentClientId = $client->getAttribute('id');
                $this->info('Updating images for client id:' . (string)$currentClientId);
                $productOffsetId = 0;
                for ($j = 0; $j < $this->getMaxIterationCount(); $j++) {
                    $products = Product::where('client_id', $currentClientId)->where('active', true)->where('id', '>', $productOffsetId)->limit(10)->get(['id', 'guid']);
                    for($k = 0; $k < count($products); $k++) {
                        $product = $products[$k];
                        $productGuid = $product->getAttribute('guid');
                        $productId = $product->getAttribute('id');
                        $productOffsetId = $productId;
                        try {
                            $this->info('Updating details for product ' . $productGuid);
                            Image::where('client_id', $clientId)->where('product_id', $productId)->delete();
                            /** @var ?ProductDetailResponse $productDetailResponse */
                            $productDetailResponse = GeneratorHelper::fetchProductDetail($clientService, $productGuid);
                            if ($productDetailResponse === null) {
                                $this->info('Product ' . $productGuid . ' not found');
                                continue;
                            }
                            $product->setAttribute('name', $productDetailResponse->getName());
                            $product->setAttribute('perex', $productDetailResponse->getPerex());
                            $product->setAttribute('category', $productDetailResponse->getDefaultCategory()?->getName());
                            $product->setAttribute('producer', $productDetailResponse->getBrand()?->getName());
                            $product->setAttribute('url', $productDetailResponse->getUrl());
                            $product->setAttribute('price', PriceHelper::getUnfiedPriceString($productDetailResponse->getVariants()));

                            foreach ($productDetailResponse->getImages() as $imageResponse) {
                                $image = new Image();
                                $hash = $clientId . '-' . $productId;
                                if ($imageResponse->getPriority() !== null) {
                                    $hash .= '-' . $imageResponse->getPriority();
                                }
                                $image->setAttribute('hash', $hash);
                                $image->setAttribute('client_id', $currentClientId);
                                $image->setAttribute('product_id', $productId);
                                $image->setAttribute('name', $imageResponse->getSeoName());
                                $image->setAttribute('priority', $imageResponse->getPriority());
                                $image->save();
                            }
                        } catch (ApiRequestNonExistingResourceException $t) {
                            Product::destroy($productId);
                            Image::where('client_id', $clientId)->where('product_id', $productId)->delete();
                            $this->error('Product ' . $productGuid . ' not found');
                        } catch (AddonNotInstalledException) {
                            $clientService->setAttribute('status', ClientServiceStatusEnum::INACTIVE);
                            $clientService->setUpdateInProgress(false);
                            $clientService->save();
                            break;
                        } catch (ApiRequestTooManyRequestsException) {
                            sleep(10);
                            $k--;
                            continue;
                        } catch (Throwable $t) {
                            $this->error('Error updating images ' . $t->getMessage());
                            LoggerHelper::log('Error updating images ' . $t->getMessage());
                            $success = false;
                            break;
                        }
                    }
                    unset($products);
                }
                $client->save();
                $clientService->setUpdateInProgress(false);
                $clientService->setAttribute('date_last_synced', now());
                $clientService->save();
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