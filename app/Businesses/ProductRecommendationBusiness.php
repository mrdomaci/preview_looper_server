<?php

declare(strict_types=1);

namespace App\Businesses;

use App\Models\Client;
use App\Models\Product;
use App\Repositories\ClientSettingsServiceOptionRepository;
use App\Repositories\OrderProductRepository;
use App\Repositories\ProductCategoryRecommendationRepository;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

class ProductRecommendationBusiness
{
    /** @var array<int, Product> */
    private array $recommendations = [];
    public function __construct(
        private OrderProductRepository $orderProductRepository,
        private ClientSettingsServiceOptionRepository $clientSettingsServiceOptionRepository,
        private ProductRepository $productRepository,
        private ProductCategoryRecommendationRepository $productCategoryRecommendationRepository
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
        foreach ($products as $product) {
            $this->recommendations+= $this->getProductsFromOrders($client, $product);
            $this->recommendations+= $this->getProductsFromProductCategoryRecommendations($client, $product, $maxResults);
            unset($this->recommendations[$product->getId()]);
        }
        arsort($this->recommendations);
        $loop = $maxResults;
        foreach ($this->recommendations as $productId => $priority) {
            try {
                $this->recommendations[$productId] = $this->productRepository->getBestVariant($client, $productId);
                $loop--;
            } catch (Throwable) {
                unset($this->recommendations[$productId]);
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
}
