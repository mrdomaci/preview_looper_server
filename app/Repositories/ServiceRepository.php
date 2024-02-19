<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Service;

class ServiceRepository
{
    public function getByUrlPath(string $urlPath): Service
    {
        return Service::where('url-path', $urlPath)->firstOrFail();
    }
}
