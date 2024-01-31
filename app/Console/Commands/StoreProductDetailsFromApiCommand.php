<?php

namespace App\Console\Commands;

use App\Businesses\ClientServiceBusiness;
use App\Connector\ProductDetailResponse;
use App\Enums\SyncEnum;
use App\Exceptions\AddonNotInstalledException;
use App\Exceptions\ApiRequestNonExistingResourceException;
use App\Exceptions\ApiRequestTooManyRequestsException;
use App\Helpers\GeneratorHelper;
use App\Helpers\LoggerHelper;
use App\Models\ClientService;
use App\Models\Product;
use App\Models\Service;
use App\Repositories\ClientServiceRepository;
use App\Repositories\ImageRepository;
use App\Repositories\ProductRepository;
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

    public function __construct(
        private readonly ClientServiceRepository $clientServiceRepository,
        private readonly ClientServiceBusiness $clientServiceBusiness,
        private readonly ProductRepository $productRepository,
        private readonly ImageRepository $imageRepository
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $clientId = $this->argument('client_id');
        $success = true;
        if ($clientId !== null) {
            $clientId = (int) $clientId;
        }
        $lastClientServiceId = 0;
        for($i = 0; $i < $this->getMaxIterationCount(); $i++) {

            $clientServices = $this->clientServiceRepository->getActive(
                $lastClientServiceId,
                Service::getDynamicPreviewImages(),
                $clientId,
                $this->getIterationCount(),
            );

            /** @var ClientService $clientService */
            foreach ($clientServices as $clientService) {
                $lastClientServiceId = $clientService->getAttribute('id');
                if ($this->clientServiceBusiness->isForbidenToUpdate($clientService) === true) {
                    continue;
                }

                $clientService->setUpdateInProgress(true);

                $client = $clientService->client()->first();
                $this->info('Updating images for client id:' . $client->getAttribute('id'));
                $productOffsetId = 0;
                for ($j = 0; $j < $this->getMaxIterationCount(); $j++) {
                    $products = $this->productRepository->getPastId($client, $productOffsetId);
                    for($k = 0; $k < count($products); $k++) {
                        /** @var Product $product */
                        $product = $products[$k];
                        $productGuid = $product->getAttribute('guid');
                        $productId = $product->getAttribute('id');
                        $productOffsetId = $productId;
                        try {
                            $this->info('Updating details for product ' . $productGuid);
                            $this->imageRepository->deleteByClientAndProduct($client, $product);
                            /** @var ?ProductDetailResponse $productDetailResponse */
                            $productDetailResponse = GeneratorHelper::fetchProductDetail($clientService, $productGuid);
                            if ($productDetailResponse === null) {
                                $this->info('Product ' . $productGuid . ' not found');
                                continue;
                            }
                            $this->productRepository->updateDetailFromResponse($product, $productDetailResponse);

                            //Product::where('parent_product_id', $product->getAttribute('id'))->update(['active' => false]);
                            foreach ($productDetailResponse->getVariants() as $variantResponse) {
                                $this->productRepository->createOrUpdateVariantFromResponse($variantResponse, $product);
                            }

                            foreach ($productDetailResponse->getImages() as $imageResponse) {
                                $this->imageRepository->createOrUpdateFromResponse($imageResponse, $client, $product);
                            }
                        } catch (ApiRequestNonExistingResourceException $t) {
                            $this->productRepository->deleteByClient($client);
                            $this->imageRepository->deleteByClient($client);
                            $this->error('Product ' . $productGuid . ' not found');
                        } catch (AddonNotInstalledException) {
                            $clientService->setStatusInactive();
                            $success = false;
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
                    if ($success === false) {
                        break;
                    }
                }
                $client->save();
                $clientService->setUpdateInProgress(false, SyncEnum::PRODUCT);
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
