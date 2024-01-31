<?php

namespace App\Http\Controllers;

use App\Dtos\ProductRecommendationDto;
use App\Helpers\ArrayHelper;
use App\Helpers\NumbersHelper;
use App\Models\OrderProduct;
use App\Models\ProductCategoryRecommendation;
use App\Repositories\ClientRepository;
use App\Repositories\ProductCategoryRecommendationRepository;
use App\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use Throwable;

class ProductController extends Controller
{
    public function __construct(
        private readonly ClientRepository $clientRepository,
        private readonly ProductRepository $productRepository,
        private readonly ProductCategoryRecommendationRepository $productCategoryRecommendationRepository,
        )
    {
    }
    public function recommend(string $eshopID, string $moduloCheck, string $guids): JsonResponse
    {
        if (NumbersHelper::isModuloCheck((int)$eshopID, (int)$moduloCheck) === false) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $client = $this->clientRepository->getByEshopId((int) $eshopID);
        if ($client === null) {
            return response()->json(['error' => 'Client not found'], 404);
        }
        $clientService =  $client->dynamicPreviewImages();
        if ($clientService === null) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $guids = explode(',', $guids);

        $products = $this->productRepository->getParentsByGuids($client, $guids);
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
                try {
                    $productRecommendations[$productCategoryRecommendation->getAttribute('id')] = (new ProductRecommendationDto(
                        $product->getAttribute('name'),
                        $product->getAttribute('price'),
                        $product->getAttribute('url'),
                        $product->getAttribute('image_url'),
                        'to do'
                    ))->toArray();
                } catch (Throwable) {
                    continue;
                }
            }
        }

        $orderIds = [];
        foreach ($products as $product) {
            $orderProducts = OrderProduct::where('product_id', $product->getAttribute('id'))->get();
            foreach ($orderProducts as $orderProduct) {
                $orderIds[$orderProduct->getAttribute('order_id')] = $orderProduct->getAttribute('order_id');
            }
        }

        if (count($orderIds) === 0) {
            return response()->json($productRecommendations);
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
            $products = $this->productRepository->getInIds($client, array_keys($productIds), 4);
            $productRecommendations = array_slice($productRecommendations, count($productRecommendations) - count($products));
            foreach ($products as $product) {
                $productRecommendations[] = (new ProductRecommendationDto(
                    $product->getAttribute('name'),
                    $product->getAttribute('price'),
                    $product->getAttribute('url'),
                    $product->getAttribute('image_url'),
                    'to do'
                ))->toArray();
            }
        }
        return response()->json($productRecommendations);
    }
}
