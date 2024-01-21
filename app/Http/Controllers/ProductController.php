<?php

namespace App\Http\Controllers;

use App\Helpers\NumbersHelper;
use App\Repositories\ClientRepository;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(private readonly ClientRepository $clientRepository)
    {
    }
    public function recommend(string $eshopID, string $moduloCheck): JsonResponse
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

        $recommendation = "";

        return response()->json(json_decode($recommendation));
    }
}
