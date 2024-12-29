<?php

declare(strict_types=1);

namespace App\Businesses;

use App\Models\Client;
use App\Models\Product;
use App\Repositories\AvailabilityRepository;
use App\Repositories\ClientSettingsServiceOptionRepository;
use App\Repositories\OrderProductRepository;
use App\Repositories\ProductCategoryRecommendationRepository;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

class ProductRecommendationBusiness
{
    /** @var array<string, Product> */
    private array $recommendations = [];
    public function __construct(
        private OrderProductRepository $orderProductRepository,
        private ClientSettingsServiceOptionRepository $clientSettingsServiceOptionRepository,
        private ProductRepository $productRepository,
        private ProductCategoryRecommendationRepository $productCategoryRecommendationRepository,
        private AvailabilityRepository $availabilityRepository,
    ) {
    }

    /**
     * @param Collection<Product> $products
     * @param Client $client
     * @return array <string, Product>
     */
    public function recommend(Collection $products, Client $client)
    {
        $maxResults = $this->clientSettingsServiceOptionRepository->getMaxResultsForUpsell($client);
        foreach ($products as $product) {
            $this->recommendations+= $this->getProductsFromOrders($client, $product);
            $this->recommendations+= $this->getProductsFromProductCategoryRecommendations($client, $product, $maxResults);
        }
        arsort($this->recommendations);
        $this->filterProductsInCart($products);
        $loop = $maxResults;
        $forbiddentAvailabilities = $this->availabilityRepository->getForbidden($client);
        foreach ($this->recommendations as $guid => $priority) {
            $guid = (string) $guid;
            try {
                $this->recommendations[$guid] = $this->productRepository->getBestVariant($client, $guid, $forbiddentAvailabilities);
                $loop--;
            } catch (Throwable) {
                unset($this->recommendations[$guid]);
            }
            if ($loop === 0) {
                break;
            }
        }
        return array_slice($this->recommendations, 0, $maxResults);
    }

    /**
     * @param Client $client
     * @param Product $product
     * @return array<int>
     */
    private function getProductsFromOrders(Client $client, Product $product): array
    {
        return $this->orderProductRepository->getByProductClient($client, $product);
    }

    /**
     * @param Client $client
     * @param Product $product
     * @param int $maxResults
     * @return array<int>
     */
    private function getProductsFromProductCategoryRecommendations(Client $client, Product $product, int $maxResults): array
    {
        return $this->productCategoryRecommendationRepository->getByClientProduct($client, $product, $maxResults);
    }

    /**
     * @param Collection<Product> $products
     */
    private function filterProductsInCart(Collection $products): void
    {
        /** @var Product $product */
        foreach ($products as $product) {
            unset($this->recommendations[$product->getGuid()]);
        }
    }
}
