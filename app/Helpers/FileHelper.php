<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Models\Client;
use Illuminate\Support\Facades\Storage;

class FileHelper
{
    public static function clientImagesResponse(Client $client): string
    {
        $clientId = $client->getAttribute('id');
        $filename = '/cache/' . $clientId . '_response.json';
        $fileContents = Storage::get($filename);
        if ($fileContents === null) {
            CacheHelper::imageResponse($client);
            $filename = '/cache/' . $clientId . '_response.json';
            $fileContents = Storage::get($filename);
        }
        return $fileContents;
    }
}