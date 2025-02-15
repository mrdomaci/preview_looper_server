<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Client;
use App\Models\ClientService;
use Illuminate\Support\Facades\Storage;

class FileHelper
{
    public static function clientImagesResponse(Client $client): string
    {
        $clientId = $client->getId();
        $filename = '/cache/' . $clientId . '_response.json';
        $fileContents = Storage::get($filename);
        if ($fileContents === null) {
            CacheHelper::imageResponse($client);
            $filename = '/cache/' . $clientId . '_response.json';
            $fileContents = Storage::get($filename);
        }
        return $fileContents;
    }

    public static function clearFiles(ClientService $clientService): void
    {
        $files = Storage::files('snapshots');
        $clientId = $clientService->getId();
        
        $filesToDelete = array_filter($files, fn($file) =>
            str_ends_with($file, "_{$clientId}_orders.txt") ||
            str_ends_with($file, "_{$clientId}_products.txt") ||
            $file === "snapshots/{$clientId}_orders.gz" ||
            $file === "snapshots/{$clientId}_products.gz");
    
        Storage::delete($filesToDelete);
    }
}
