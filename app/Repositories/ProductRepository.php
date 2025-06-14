<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Availability;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProductRepository
{
    public function deleteByClient(Client $client): void
    {
        Product::where('client_id', $client->getId())->delete();
    }

    /**
     * @param Client $client
     * @param array<string> $guids
     * @return Collection<Product>
     */
    public function getParentsByGuids(Client $client, array $guids): Collection
    {
        return Product::where('client_id', $client->getId())
            ->where('active', true)
            ->whereIn('guid', $guids)
            ->distinct('guid')
            ->get();
    }

    public function getByName(Client $client, string $name): Collection
    {
        return Product::where('client_id', $client->getId())
            ->select('guid', 'name')
            ->where('active', true)
            ->where('name', 'like', '%' . $name . '%')
            ->distinct('guid')
            ->limit(5)
            ->get();
    }

    public function getForClient(Client $client, string $guid): Product
    {
        return Product::where('client_id', $client->getId())
            ->where('guid', $guid)
            ->firstOrFail();
    }

    /**
     * @param Client $client
     * @param string $guid
     * @param Collection<Availability> $forbiddenAvailabilities
     * @return Product
     */
    public function getBestVariant(Client $client, string $guid, Collection $forbiddenAvailabilities): Product
    {
        return Product::where('client_id', $client->getId())
            ->where('active', true)
            //->where('availability_level', '<', 3)
            ->where('guid', $guid)
            ->where(function ($query) use ($forbiddenAvailabilities) {
                $query->whereNotIn('availability_id', $forbiddenAvailabilities->pluck('id')->toArray())
                      ->orWhereNull('availability_id');
            })
            ->orderBy('availability_level', 'asc')
            ->orderBy('stock', 'desc')
            ->select(
                'name',
                'code',
                'guid',
                'price',
                'availability_name as availability',
                'image_url',
                'unit',
                'foreign_id as id',
                'availability_color as color',
                DB::raw("CONCAT(REGEXP_REPLACE(url, '/+$', ''), '?utm_content=site-checkout&utm_source=easyupsell&utm_medium=website') as url")
            )
            ->firstOrFail();
    }

    public function getByGuid(Client $client, string $guid): ?Product
    {
        return Product::where('client_id', $client->getId())
            ->where('guid', $guid)
            ->first();
    }

    /**
     * @param array<int<0, max>, array<string, mixed>>$products
     */
    public function bulkCreateOrUpdate(array $products): void
    {
        DB::transaction(function () use ($products) {
            Product::upsert(
                $products,
                ['guid', 'code', 'client_id'],
                [
                    'stock', 'unit', 'price', 'availability_name', 'availability_id',
                    'is_negative_stock_allowed', 'foreign_id', 'image_url', 'updated_at',
                    'created_at', 'active', 'name', 'url', 'images', 'availability_foreign_id',
                ]
            );
        });
    }
}
