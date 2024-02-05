<?php
declare(strict_types=1);

namespace App\Businesses;

use App\Dtos\ProductRecommendationDto;
use App\Helpers\ArrayHelper;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductCategoryRecommendation;
use App\Repositories\ProductCategoryRecommendationRepository;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

class ProductRecommendationBusiness {

    private const PRODUCT_HTML_TEMPLATE = '<tr class="removeable" data-micro="cartItem" data-micro-identifier="%s" data-micro-sku="0011" data-testid="productItem_%s"><td class="cart-p-image"><a target="blank" href="%s?utm_source=upsale;utm_medium=cart"><img src="%s" data-src="%s" alt="%s"></a></td><td class="p-name" data-testid="cartProductName"><a target="blank" href="%s?utm_source=upsale;utm_medium=cart" class="main-link" data-testid="cartWidgetProductName">%s</a></td><td class="p-availability p-cell"><span class="p-label">Dostupnost</span><strong class="availability-label" style="color: #009901">Skladem</strong></td><td class="p-total"><span class="p-label">Součet</span><strong class="price-final" data-testid="cartPrice">%s</strong></td></tr>';

    public function __construct(
        private ProductRepository $productRepository,
        private ProductCategoryRecommendationRepository $productCategoryRecommendationRepository)
    {
        
    }
    public function getResponseForProduct(Product $product): string {
        return self::PRODUCT_HTML_TEMPLATE;
    }

    /**
     * @param Collection<Product> $products
     * @return array<int, ProductRecommendationDto>
     */
    public function getForProductCategoryRecommendation(Collection $products): array
    {
        $excludeProductIds = [];
        foreach ($products as $product) {
            $excludeProductIds[] = $product->getAttribute('id');
        }
        $productRecommendations = [];
        foreach ($products as $product) {
            $productCategoryRecommendations = $this->productCategoryRecommendationRepository->get($product);
            if ($productCategoryRecommendations === null) {
                continue;
            }
            if (count($productCategoryRecommendations) === 0) {
                continue;
            }
            /** @var ProductCategoryRecommendation $productCategoryRecommendation */
            foreach ($productCategoryRecommendations as $productCategoryRecommendation) {
                $product = $productCategoryRecommendation->product()->first();
                $stockedChild = $product->stockedChild();
                if ($stockedChild === null) {
                    continue;
                }
                $url = $product->getAttribute('url');
                try {
                    $productRecommendations[(int)$productCategoryRecommendation->getAttribute('id')] = new ProductRecommendationDto(
                        $stockedChild->getAttribute('name'),
                        $stockedChild->getAttribute('price'),
                        $url,
                        $stockedChild->getAttribute('image_url'),
                        $stockedChild->getAttribute('availability'),
                        $stockedChild->getAttribute('code'),
                        $stockedChild->getAttribute('unit'),
                    );
                } catch (Throwable) {
                    continue;
                }
            }
        }
        return $productRecommendations;
    }

    /**
     * @param Collection<Product> $products
     * @param array<int, ProductRecommendationDto> $productRecommendations
     * @return array<int, ProductRecommendationDto>
     */
    public function getForOrders(Collection $products, array $productRecommendations): array
    {
        $orderIds = [];
        foreach ($products as $product) {
            $orderProducts = OrderProduct::where('product_id', $product->getAttribute('id'))->get();
            foreach ($orderProducts as $orderProduct) {
                $orderIds[$orderProduct->getAttribute('order_id')] = $orderProduct->getAttribute('order_id');
            }
        }

        if (count($orderIds) === 0) {
            return $productRecommendations;
        }

        $excludeProductIds = [];
        foreach ($products as $product) {
            $excludeProductIds[] = $product->getAttribute('id');
        }
        
        $orderProducts = OrderProduct::whereIn('order_id', $orderIds)->get();
        $orderProducts = $orderProducts->filter(function (OrderProduct $orderProduct) use ($excludeProductIds) {
            return !in_array($orderProduct->getAttribute('product_id'), $excludeProductIds);
        });

        $productIds = [];
        foreach ($orderProducts as $orderProduct) {
            $count = 0;
            if (isset($productIds[$orderProduct->getAttribute('product_id')])) {
                $count = $productIds[$orderProduct->getAttribute('product_id')];
            }
            $productIds[$orderProduct->getAttribute('product_id')] = $count + 1;
        }

        if (count($productIds) > 0) {
            $productIds = ArrayHelper::sort($productIds);
            if (count($productRecommendations) > 2) {
                if (count($productIds) > 2) {
                	$productIds = array_slice($productIds, 0, 2);
                }
            }
            $products = $this->productRepository->getParentsInIds(array_keys($productIds), 4);
            $productRecommendations = array_slice($productRecommendations, count($productRecommendations) - count($products));
            foreach ($products as $product) {
                $stockedChild = $product->stockedChild();
                if ($stockedChild === null) {
                    continue;
                }
                $url = $product->getAttribute('url');
                $productRecommendations[] = new ProductRecommendationDto(
                    $stockedChild->getAttribute('name'),
                    $stockedChild->getAttribute('price'),
                    $url,
                    $stockedChild->getAttribute('image_url'),
                    (string) $stockedChild->getAttribute('attribute'),
                    $stockedChild->getAttribute('code'),
                    $stockedChild->getAttribute('unit'),
                );
            }
        }
        return $productRecommendations;
    }

    /**
     * @param array<int, ProductRecommendationDto> $productRecommendations
     * @return array<int, array<string, string>>
     */
    public function formatResponse(array $productRecommendations): array
    {
        $response = [];
        foreach ($productRecommendations as $productRecommendation) {
            $response[] = $productRecommendation->toArray();
        }
        return $response;
    }
}