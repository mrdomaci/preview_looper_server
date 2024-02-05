<?php

namespace App\Http\Controllers;

use App\Businesses\ProductBusiness;
use App\Businesses\ProductRecommendationBusiness;
use App\Helpers\NumbersHelper;
use App\Models\Product;
use App\Repositories\ClientRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        private readonly ClientRepository $clientRepository,
        private readonly ProductRecommendationBusiness $productRecommendationBusiness,
        private readonly ProductBusiness $productBusiness,
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

        /** @var Collection<Product> $products */
        $products = $this->productBusiness->getProductsByGuids($client, $guids);

        if ($products->isEmpty()) {
            return response()->json([]);
        }

        $productRecommendations = $this->productRecommendationBusiness->getForProductCategoryRecommendation($products);
        $productRecommendations = $this->productRecommendationBusiness->getForOrders($products, $productRecommendations);
        $productRecommendations = $this->productRecommendationBusiness->formatResponse($productRecommendations);

        return response()->json($productRecommendations);
    }
}
