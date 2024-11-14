<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

class CategoryRepository
{
    public function createOrUpdate(Client $client, string $name, string $guid): Category
    {
        try {
            $category = Category::where('client_id', $client->getId())->where('guid', $guid)->firstOrFail();
        } catch (Throwable) {
            $category = Category::create([
                'client_id' => $client->getId(),
                'name' => $name,
                'guid' => $guid,
            ]);
            $category->save();
        }
        return $category;
    }

    public function getForClient(Client $client, ?string $guid = null): ?Category
    {
        if ($guid === null) {
            return null;
        }
        return Category::where('client_id', $client->getId())->where('guid', $guid)->firstOrFail();
    }

    public function getAllForClient(Client $client): Collection
    {
        return $client->categories;
    }

    /**
     * @param array<int<0, max>, array<string, mixed>>$categories
     */
    public function bulkCreateOrUpdate(array $categories): void
    {
        Category::upsert(
            $categories,
            ['guid', 'client_id'],
            [
                'name',
            ]
        );
    }
}
