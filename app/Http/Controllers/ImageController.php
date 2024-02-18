<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\FileHelper;
use App\Helpers\NumbersHelper;
use App\Repositories\ClientRepository;
use Illuminate\Http\JsonResponse;
use Throwable;

class ImageController extends Controller
{
    public function __construct(private readonly ClientRepository $clientRepository)
    {
    }
    public function all(string $eshopID, string $moduloCheck): JsonResponse
    {
        if (NumbersHelper::isModuloCheck((int)$eshopID, (int)$moduloCheck) === false) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $client = $this->clientRepository->getByEshopId((int) $eshopID);
        } catch (Throwable) {
            return response()->json(['error' => 'Client not found'], 404);
        }
        $clientService =  $client->dynamicPreviewImages();
        if ($clientService === null) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $fileContents = FileHelper::clientImagesResponse($client);
        return response()->json(json_decode($fileContents));
    }
}
