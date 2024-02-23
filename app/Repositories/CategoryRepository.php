<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

class CategoryRepository
{
    public function createOrUpdate(Client $client, string $name): Category
    {
        try {
            $category = Category::where('client_id', $client->getId())->where('name', $name)->firstOrFail();
        } catch (Throwable) {
            $category = Category::create([
                'client_id' => $client->getId(),
                'name' => $name,
            ]);
        }
        return $category;
    }

    public function getForClient(Client $client, int $id): Category
    {
        return Category::where('client_id', $client->getId())->where('id', $id)->firstOrFail();
    }

    public function getAllForClient(Client $client): Collection
    {
        return $client->categories;
    }
}
