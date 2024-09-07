<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Connector\Shoptet\ProductBrand;
use App\Connector\Shoptet\ProductCategory;
use App\Connector\Shoptet\ProductResponse;
use App\Connector\Shoptet\ProductVariantResponse;
use App\Helpers\ArrayHelper;
use App\Helpers\CacheHelper;
use App\Helpers\StringHelper;
use App\Models\Category;
use App\Models\Client;
use App\Models\ClientService;
use App\Repositories\AvailabilityRepository;
use App\Repositories\ClientServiceRepository;
use App\Repositories\ProductRepository;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SnapshotProductToDBCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snapshot:product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Snapshot product to DB and jsonn cache';

    public function __construct(
        private readonly ClientServiceRepository $clientServiceRepository,
        private readonly ProductRepository $productRepository,
        private readonly AvailabilityRepository $availabilityRepository
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
        $success = true;

        // Get all files in the 'snapshots' directory
        $files = Storage::files('snapshots');

        // Filter and get the latest '_products.gz' file
        $latestFile = collect($files)
            ->filter(fn($file) => str_ends_with($file, '_products.gz'))
            ->sortByDesc(fn($file) => Storage::lastModified($file))
            ->first();

        if ($latestFile) {
            $clientService = $this->getClientService($latestFile);
            $client = $clientService->client()->first();

            $onStockAvailability = $this->availabilityRepository->getIsOnStockAvailability($client);
            $soldOutNegativeStockForbidden = $this->availabilityRepository->getSoldOutNegativeStockForbiddenkAvailability($client);
            $soldOutNegativeStockAllowed = $this->availabilityRepository->getSoldOutNegativeStockAllowedAvailability($client);

            // Unzip the file from .gz to .txt
            $gzFile = gzopen(Storage::path($latestFile), 'rb');
            $txtFilePath = str_replace('.gz', '.txt', $latestFile);
            $txtFile = fopen(Storage::path($txtFilePath), 'wb');

            while (!gzeof($gzFile)) {
                fwrite($txtFile, gzread($gzFile, 4096));
            }

            gzclose($gzFile);
            fclose($txtFile);

            // Loop through the file row by row
            $txtFile = fopen(Storage::path($txtFilePath), 'r');
            DB::beginTransaction();
            try {
                while (($line = fgets($txtFile)) !== false) {
                    $productData = json_decode($line, true);

                    $images = null;
                    if (isset($productData['images'])) {
                        $images = [];
                        usort($productData['images'], function ($a, $b) {
                            return $a['priority'] <=> $b['priority'];
                        });
                        foreach ($productData['images'] as $image) {
                            $images[] = $image['seoName'];
                        }
                    }
                    $productResponse = new ProductResponse(
                        $productData['guid'],
                        (isset($productData['creationTime']) ? new DateTime($productData['creationTime']) : null),
                        (isset($productData['changeTime']) ? new DateTime($productData['changeTime']) : null),
                        ($productData['name'] ?? null),
                        (isset($productData['voteAverageScore']) ? (float) $productData['voteAverageScore'] : null),
                        (isset($productData['voteCount']) ? (int) $productData['voteCount'] : null),
                        ($productData['type'] ?? null),
                        ($productData['visibility'] ?? null),
                        (isset($productData['defaultCategory']) ?
                            new ProductCategory(
                                $productData['defaultCategory']['guid'],
                                $productData['defaultCategory']['name'],
                                $this->getCategoryId($client, $productData['defaultCategory']['name']),
                            ) : null
                        ),
                        ($productData['url'] ?? null),
                        ($productData['supplier'] ?? null),
                        (isset($productData['brand']) ? new ProductBrand($productData['brand']['code'], $productData['brand']['name']) : null),
                        ($productData['shortDescription'] ?? null),
                        $images,
                    );

                    $product = $this->productRepository->createOrUpdateFromResponse($client, $productResponse);

                    foreach ($productData['variants'] as $variant) {
                        $availabilityName = null;
                        $availabilityId = null;
                        $isNegativeStockAllowed = false;
                        $stock = (float) $variant['stock'];
                        if (ArrayHelper::containsKey($variant, 'availability') === true) {
                            if ($variant['availability'] !== null) {
                                $availability = $variant['availability']['name'];
                                $availabilityId = (string) $variant['availability']['id'];
                            }
                        }
                        if ($availabilityName === null && $stock <= 0) {
                            if (ArrayHelper::containsKey($variant, 'availabilityWhenSoldOut') === true) {
                                if ($variant['availabilityWhenSoldOut'] !== null) {
                                    $availabilityName = $variant['availabilityWhenSoldOut']['name'];
                                    $availabilityId = (string) $variant['availabilityWhenSoldOut']['id'];
                                }
                            }
                        }
                        $variantName = $productResponse->getName();
                        if (ArrayHelper::containsKey($variant, 'name')) {
                            $variantName .= ' ' . $variant['name'];
                        }

                        if (ArrayHelper::containsKey($variant, 'negativeStockAllowed')) {
                            if ($variant['negativeStockAllowed'] === 'yes') {
                                $isNegativeStockAllowed = true;
                            } elseif ($variant['negativeStockAllowed'] === 'yes-global') {
                                $isNegativeStockAllowed = true;
                            }
                        }
                        $image = StringHelper::removeParameter($variant['image']);
                        $foreignId = StringHelper::getIdFromImage($image);

                        $productVariantResponse = new ProductVariantResponse(
                            $variant['code'],
                            $variant['ean'],
                            $stock,
                            $variant['unit'],
                            (float) $variant['weight'],
                            (float) $variant['width'],
                            (float) $variant['height'],
                            (float) $variant['depth'],
                            $variant['visible'],
                            (int) $variant['amountDecimalPlaces'],
                            (float) $variant['price'],
                            $variant['includingVat'],
                            (float) $variant['vatRate'],
                            $variant['currencyCode'],
                            (float) $variant['actionPrice'],
                            (float) $variant['commonPrice'],
                            $availabilityName,
                            $variantName,
                            $availabilityId,
                            $image,
                            $foreignId,
                            $isNegativeStockAllowed,
                        );
                        if ($availabilityId !== null) {
                            $availability = $this->availabilityRepository->getByForeignId($client, $availabilityId);
                        } elseif ($productVariantResponse->getStock() > 0) {
                            $availability = $onStockAvailability;
                        } elseif ($productVariantResponse->isNegativeStockAllowed() === true) {
                            $availability = $soldOutNegativeStockAllowed;
                        } else {
                            $availability = $soldOutNegativeStockForbidden;
                        }
                        
                        $this->productRepository->createOrUpdateVariantFromResponse($productVariantResponse, $product, $availability);
                    }
                }
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->error("Error processing the snapshot file: {$e->getMessage()}");
                $success = false;
            }

            fclose($txtFile);
            Storage::delete($txtFilePath);
            Storage::delete($latestFile);
            CacheHelper::imageResponse($client);
        } else {
            $this->info('No product snapshot file found.');
        }

        return $success ? Command::SUCCESS : Command::FAILURE;
    }


    
    /**
     * @param Client $client
     * @param string|null $name
     * @return int|null
     */
    private function getCategoryId(Client $client, ?string $name): ?int
    {
        if (!$name) {
            return null;
        }

        $category = Category::where('name', $name)
                                ->where('client_id', $client->getId())
                                ->first();

        return $category ? $category->id : null;
    }

    private function getClientService(string $filePath): ClientService
    {
        $clientServiceId = (int) explode('_', explode('/', $filePath)[1])[0];
        return $this->clientServiceRepository->get($clientServiceId);
    }
}
