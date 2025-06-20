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
    private array $recommendationsByCategory = [];
    /** @var array<string, Product> */
    private array $recommendationsFromOrders = [];
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
     * @return array <int, Product>
     */
    public function recommend(Collection $products, Client $client)
    {
        $maxResults = $this->clientSettingsServiceOptionRepository->getMaxResultsForUpsell($client);
        $type = $this->clientSettingsServiceOptionRepository->getEasyUpsellRecommendationType($client);
        $recommendationsByCategory = [];
        $recommendationsFromOrders = [];
        foreach ($products as $product) {
            if ($type === 'mixed' || $type === 'categories_only') {
                $recommendationsByCategory+= $this->getProductsFromProductCategoryRecommendations($client, $product, $maxResults);
            }
            if ($type === 'mixed' || $type === 'orders_only') {
                $recommendationsFromOrders+= $this->getProductsFromOrders($client, $product);
            }
        }
        arsort($recommendationsFromOrders);
        $recommendationsByCategory = $this->filterProductsInCart($products, $recommendationsByCategory);
        $recommendationsFromOrders = $this->filterProductsInCart($products, $recommendationsFromOrders);
        $loop = $maxResults;
        $forbiddentAvailabilities = $this->availabilityRepository->getForbidden($client);
        foreach ($recommendationsByCategory as $guid => $description) {
            $guid = (string) $guid;
            try {
                /** @var Product $product */
                $product = $this->productRepository->getBestVariant($client, $guid, $forbiddentAvailabilities);
                $product->setDescription($description);
                $this->recommendationsByCategory[$guid] = $product;
                $loop--;
            } catch (Throwable) {
                unset($this->recommendationsByCategory[$guid]);
            }
            if ($loop === 0) {
                break;
            }
        }
        $loop = $maxResults;
        /** @var array<int> $priority */
        foreach ($recommendationsFromOrders as $guid => $priority) {
            $guid = (string) $guid;
            try {
                $this->recommendationsFromOrders[$guid] = $this->productRepository->getBestVariant($client, $guid, $forbiddentAvailabilities);
                $this->recommendationsFromOrders[$guid]->description = '';
                $loop--;
            } catch (Throwable) {
                unset($this->recommendationsFromOrders[$guid]);
            }
            if ($loop === 0) {
                break;
            }
        }
        $result = [];
        $recommendationsByCategory = array_values($this->recommendationsByCategory);
        $recommendationsFromOrders = array_values($this->recommendationsFromOrders);
        for ($i = 0; $i < $maxResults; $i++) {
            $increment = 0;
            if (isset($recommendationsByCategory[$i])) {
                $result[] = $recommendationsByCategory[$i];
                $increment++;
            }
            if (isset($recommendationsFromOrders[$i])) {
                $result[] = $recommendationsFromOrders[$i];
                $increment++;
            }
            if ($increment === 2) {
                $i++;
            }
        }
        return array_slice($result, 0, $maxResults);
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
     * @return array<string, string>
     */
    private function getProductsFromProductCategoryRecommendations(Client $client, Product $product, int $maxResults): array
    {
        return $this->productCategoryRecommendationRepository->getByClientProduct($client, $product, $maxResults);
    }

    /**
     * @param Collection<Product> $products
     * @param array<string, string|int> $recommendations
     * @return array<string, string|int>
     */
    private function filterProductsInCart(Collection $products, array $recommendations): array
    {
        /** @var Product $product */
        foreach ($products as $product) {
            $guid = $product->getGuid();
            if (isset($recommendations[$guid])) {
                unset($recommendations[$guid]);
            }
        }
        return $recommendations;
    }
}
