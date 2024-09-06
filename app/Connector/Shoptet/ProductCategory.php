<?php

declare(strict_types=1);

namespace App\Connector\Shoptet;

class ProductCategory
{
    public function __construct(
        private ?string $guid,
        private ?string $name,
        private ?int $id = null,
    ) {
    }

    public function getGuid(): ?string
    {
        return $this->guid;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
