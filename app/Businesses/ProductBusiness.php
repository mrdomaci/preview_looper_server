<?php
declare(strict_types=1);

namespace App\Businesses;

use App\Models\Client;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;

class ProductBusiness {
    public function __construct(private ProductRepository $productRepository) {}

    /**
     * @param Client $client
     * @param string $guids
     * @return Collection<Product>
     */
    public function getProductsByGuids(Client $client, string $guids): Collection
    {
        if ($guids === '') {
            // @phpstan-ignore-next-line
            return new Collection();
        }
        $guids = explode(',', $guids);
        return $this->productRepository->getParentsByGuids($client, $guids);
    }
}