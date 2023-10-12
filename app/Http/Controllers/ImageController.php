<?php

namespace App\Http\Controllers;

use App\Enums\ClientServiceStatusEnum;
use App\Helpers\FileHelper;
use App\Models\Client;
use App\Models\ClientService;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class ImageController extends Controller
{
    public function all(string $eshopID, string $moduloCheck): JsonResponse
    {
        if ((int)$eshopID%11 !== (int)$moduloCheck) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $client = Client::where('eshop_id', (int) $eshopID)->first();
        if ($client === null) {
            return response()->json(['error' => 'Client not found'], 404);
        }
        $clientId = $client->getAttribute('id');
        $clientService =  ClientService::where('client_id', $clientId)->where('service_id', Service::DYNAMIC_PREVIEW_IMAGES)->where('status', ClientServiceStatusEnum::ACTIVE)->first();
        if ($clientService === null) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $fileContents = FileHelper::clientImagesResponse($client);
        return response()->json(json_decode($fileContents));
    }
}