<?php

namespace App\Http\Controllers;

use App\Businesses\ProductBusiness;
use App\Businesses\ProductRecommendationBusiness;
use App\Helpers\NumbersHelper;
use App\Models\Product;
use App\Repositories\ClientRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Throwable;

class ProductController extends Controller
{
    public function __construct(
        private readonly ClientRepository $clientRepository,
        private readonly ProductBusiness $productBusiness,
        private readonly ProductRecommendationBusiness $productRecommendationBusiness,
        )
    {
    }
    public function recommend(string $eshopID, string $moduloCheck, string $guids): JsonResponse
    {
        if (NumbersHelper::isModuloCheck((int)$eshopID, (int)$moduloCheck) === false) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $client = $this->clientRepository->getByEshopId((int) $eshopID);
        } catch (Throwable) {
            return response()->json(['error' => 'Client not found'], 404);
        }

        $clientService =  $client->upsell();
        if ($clientService === null) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        /** @var Collection<Product> $products */
        $products = $this->productBusiness->getByGuids($client, $guids);

        if ($products->isEmpty()) {
            return response()->json([]);
        }

        $productRecommendations = $this->productRecommendationBusiness->recommend($products, $client);

        return response()->json($productRecommendations);
    }

    public function getData(int $clientId, string $name): Collection
    {
        $client = $this->clientRepository->get($clientId);
        return $this->productBusiness->getByName($client, $name);
    }
}
