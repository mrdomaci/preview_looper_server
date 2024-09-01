<?php

declare(strict_types=1);

namespace App\Connector\Fio;

class Response
{
    public function __construct(public array $data)
    {
    }
    public function getLicense(): ?LicenseListResponse
    {
        return null;
    }
}
