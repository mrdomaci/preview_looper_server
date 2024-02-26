<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Exceptions\DataNotFoundException;
use App\Models\Service;
use Exception;

class ServiceRepository
{
    public function get(int $id): Service
    {
        $client = Service::find($id);
        if ($client === null) {
            throw new DataNotFoundException(new Exception('Service not found id: ' . $id));
        }
        return $client;
    }
    public function getByUrlPath(string $urlPath): Service
    {
        return Service::where('url-path', $urlPath)->firstOrFail();
    }
}
