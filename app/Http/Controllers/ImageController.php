<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\FileHelper;
use App\Helpers\NumbersHelper;
use App\Repositories\ClientRepository;
use App\Repositories\ClientSettingsServiceOptionRepository;
use Illuminate\Http\JsonResponse;
use Throwable;

class ImageController extends Controller
{
    public function __construct(
        private readonly ClientRepository $clientRepository,
        private readonly ClientSettingsServiceOptionRepository $clientSettingsServiceOptionRepository,
    ) {
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
        return response()->json([
            'infinite_repeat' => $this->clientSettingsServiceOptionRepository->getDynamicPreviewImagesInfiniteRepeat($client) ?? '0',
            'return_to_default' => $this->clientSettingsServiceOptionRepository->getDynamicPreviewImagesReturnToDefault($client) ?? '0',
            'show_time' => $this->clientSettingsServiceOptionRepository->getDynamicPreviewImagesShowTime($client) ?? 1500,
            'initial_loop' => $this->clientSettingsServiceOptionRepository->getDynamicPreviewImagesInitialLoop($client) ?? 500,
            'apply_to' => $this->clientSettingsServiceOptionRepository->getDynamicPreviewImagesApplyTo($client) ?? 'all',
            'mobile_icons' => $this->clientSettingsServiceOptionRepository->getDynamicPreviewImagesMobileIcons($client) ?? 'circles',
            'data' => json_decode($fileContents)
        ]);
    }
}
