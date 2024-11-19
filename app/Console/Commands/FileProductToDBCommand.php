<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ClientServiceQueueStatusEnum;
use App\Helpers\ArrayHelper;
use App\Helpers\StringHelper;
use App\Models\Currency;
use App\Repositories\AvailabilityRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\ClientServiceQueueRepository;
use App\Repositories\ProductCategoryRepository;
use App\Repositories\ProductRepository;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FileProductToDBCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'file:product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert or update products from txt files to DB';

    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly AvailabilityRepository $availabilityRepository,
        private readonly ClientServiceQueueRepository $clientServiceQueueRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly ProductCategoryRepository $productCategoryRepository,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $clientServiceStatus = ClientServiceQueueStatusEnum::DB_PRODUCTS;
        $clientServiceQueue = $this->clientServiceQueueRepository->getNext($clientServiceStatus);

        if ($clientServiceQueue === null) {
            $this->info('No client service in product snapshot queue');
            return Command::SUCCESS;
        }
        $clientService = $clientServiceQueue->clientService()->first();
        $client = $clientService->client()->first();

        $onStockAvailability = $this->availabilityRepository->getIsOnStockAvailability($client);
        $soldOutNegativeStockForbidden = $this->availabilityRepository->getSoldOutNegativeStockForbiddenkAvailability($client);
        $soldOutNegativeStockAllowed = $this->availabilityRepository->getSoldOutNegativeStockAllowedAvailability($client);

        $txtFilePath = collect(Storage::files('snapshots'))->first(function ($files) use ($clientServiceQueue) {
            return preg_match('/' . $clientServiceQueue->client_service_id . '_products\.txt$/', $files);
        });
        if ($txtFilePath) {
            $txtFile = fopen(Storage::path($txtFilePath), 'r');
            $products = [];
            $categories = [];
            $productCategories = [];
            try {
                $clientService->setUpdateInProgress(true);
                $count = 0;
                $guids = [];
                while (($line = fgets($txtFile)) !== false) {
                    $productData = json_decode($line, true);
                    if ($productData === null) {
                        continue;
                    }

                    $images = null;
                    if (isset($productData['images']) && is_array($productData['images'])) {
                        $images = [];
                        usort($productData['images'], function ($a, $b) {
                            return $a['priority'] <=> $b['priority'];
                        });
                        foreach ($productData['images'] as $image) {
                            if (!isset($image['seoName'])) {
                                continue;
                            }
                            $images[] = $image['seoName'];
                        }
                        $images = json_encode($images);
                    }
                    if (!isset($productData['guid'])) {
                        continue;
                    }

                    $product = [
                        'client_id' => $client->getId(),
                        'guid' => $productData['guid'],
                        'active' => 1,
                        'created_at' => (isset($productData['creationTime']) ? new DateTime($productData['creationTime']) : null),
                        'updated_at' => (isset($productData['changeTime']) ? new DateTime($productData['changeTime']) : null),
                        'name' => ($productData['name'] ?? ''),
                        'url' => ($productData['url'] ?? ''),
                        'images' => $images,
                        'perex' => ($productData['shortDescription'] ?? ''),
                        'producer' => ($productData['brand']['name'] ?? null),
                    ];
                    $guids[] = ($productData['guid'] ?? '');
                    if (isset($productData['variants']) && is_array($productData['variants'])) {
                        foreach ($productData['variants'] as $variant) {
                            $productVariant = $product;

                            $availabilityName = null;
                            $availabilityId = null;
                            $isNegativeStockAllowed = false;
                            $stock = (isset($variant['stock']) ? (float) $variant['stock'] : 0);
                            $image = StringHelper::removeParameter($variant['image'] ?? '');

                            if (is_array($variant) && ArrayHelper::containsKey($variant, 'availability') === true) {
                                if ($variant['availability'] !== null) {
                                    if (is_array($variant['availability'])) {
                                        $availabilityName = $variant['availability']['name'] ?? null;
                                        $availabilityId = isset($variant['availability']['id']) ? (string) $variant['availability']['id'] : '';
                                    }
                                }
                            }
                            if ($availabilityName === null && $stock <= 0) {
                                if (is_array($variant) && ArrayHelper::containsKey($variant, 'availabilityWhenSoldOut') === true) {
                                    if ($variant['availabilityWhenSoldOut'] !== null) {
                                        if (is_array($variant['availabilityWhenSoldOut'])) {
                                            $availabilityName = ($variant['availabilityWhenSoldOut']['name'] ?? null);
                                            $availabilityId = (isset($variant['availabilityWhenSoldOut']['id']) ? (string) $variant['availabilityWhenSoldOut']['id'] : '');
                                        }
                                    }
                                }
                            }
                            $variantName = $productVariant['name'];
                            if (is_array($variant) && ArrayHelper::containsKey($variant, 'name') && $variant['name'] !== null) {
                                $variantName .= ' ' . $variant['name'];
                            }

                            if (is_array($variant) && ArrayHelper::containsKey($variant, 'negativeStockAllowed')) {
                                if ($variant['negativeStockAllowed'] === 'yes') {
                                    $isNegativeStockAllowed = true;
                                } elseif ($variant['negativeStockAllowed'] === 'yes-global') {
                                    $isNegativeStockAllowed = true;
                                }
                            }

                            if ($availabilityId === null) {
                                if ($stock > 0 && $onStockAvailability !== null) {
                                    $availabilityName = $onStockAvailability->getName();
                                    $availabilityId = (string) $onStockAvailability->getId();
                                } else if ($isNegativeStockAllowed === true && $soldOutNegativeStockAllowed !== null) {
                                    $availabilityName = $soldOutNegativeStockAllowed->getName();
                                    $availabilityId = (string) $soldOutNegativeStockAllowed->getId();
                                } else if ($soldOutNegativeStockForbidden !== null) {
                                    $availabilityName = $soldOutNegativeStockForbidden->getName();
                                    $availabilityId = (string) $soldOutNegativeStockForbidden->getId();
                                }
                            }
                            $price = '0';
                            if (is_array($variant) && ArrayHelper::containsKey($variant, 'price') && $variant['price'] !== null) {
                                $price = $variant['price'];
                            }
                            $productVariant['code'] = $variant['code'] ?? '';
                            $productVariant['name'] .=  isset($variant['name']) ? ' ' . $variant['name'] : '';
                            $productVariant['stock'] = $stock;
                            $productVariant['unit'] = $variant['unit'] ?? '';
                            $productVariant['price'] = Currency::formatPrice($price, $variant['currencyCode'] ?? 'CZK');
                            $productVariant['availability_name'] = $availabilityName;
                            //$productVariant['availability_id'] = $availabilityId;
                            $productVariant['is_negative_stock_allowed'] = $isNegativeStockAllowed;
                            $productVariant['foreign_id'] = StringHelper::getIdFromImage($image);
                            $productVariant['image_url'] = StringHelper::removeParameter($image);

                            $products[] = $productVariant;
                        }
                    }
                    if (isset($productData['categories']) && is_array($productData['categories'])) {
                        foreach ($productData['categories'] as $category) {
                            if (!isset($category['guid'])) {
                                continue;
                            }
                            if (!isset($category['name'])) {
                                continue;
                            }
                            $categories[$category['guid']] = [
                                'client_id' => $client->getId(),
                                'guid' => $category['guid'] ?? '',
                                'name' => $category['name'] ?? '',
                            ];
                            $productCategories[] = [
                                'product_guid' => $productData['guid'] ?? '',
                                'category_guid' => $category['guid'] ?? '',
                                'client_id' => $client->getId(),
                            ];
                        }
                    }
                    $count++;
                    if ($count % 100 === 0) {
                        $this->productRepository->bulkCreateOrUpdate($products);
                        $this->categoryRepository->bulkCreateOrUpdate($categories);
                        $this->productCategoryRepository->dropForProducts($guids, $client);
                        $this->productCategoryRepository->bulkCreateOrUpdate($productCategories);

                        $products = [];
                        $categories = [];
                        $productCategories = [];
                        $guids = [];
                    }
                }
                if (count($products) > 0) {
                    $this->productRepository->bulkCreateOrUpdate($products);
                    $this->categoryRepository->bulkCreateOrUpdate($categories);
                    $this->productCategoryRepository->dropForProducts($guids, $client);
                    $this->productCategoryRepository->bulkCreateOrUpdate($productCategories);
                }
                $this->info('Client service ' . $clientService->getId() . ' file product');
            } catch (\Throwable $e) {
                $this->error("Error processing the product snapshot file: {$e->getMessage()}");
                $clientService->setUpdateInProgress(false);
                return Command::FAILURE;
            } finally {
                $clientService->setUpdateInProgress(false);
            }
            fclose($txtFile);
            Storage::delete($txtFilePath);
        } else {
            $clientServiceQueue = $clientServiceQueue->next();
            $clientService->setProductsLastSyncedAt(new DateTime());
            $clientService->save();
            if ($clientServiceQueue->getStatus()->name === ClientServiceQueueStatusEnum::DONE->name) {
                $this->clientServiceQueueRepository->createOrIgnore($clientService);
            }
            $this->info('Client service ' . $clientService->getId() . ' file product next');
        }
        return Command::SUCCESS;
    }
}
