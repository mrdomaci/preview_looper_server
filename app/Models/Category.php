<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use HasFactory;

    protected $fillable=[
        'client_id',
        'name',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public static function createOrUpdate(int $clientId, string $name): Category
    {
        $category = Category::where('client_id', $clientId)->where('name', $name)->first();
        if ($category === NULL) {
            $category = Category::create([
                'client_id' => $clientId,
                'name' => $name,
            ]);
        }
        return $category;
    }
}
