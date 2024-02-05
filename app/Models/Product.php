<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'guid',
        'active',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(Image::class)->orderBy('priority', 'asc');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function stockedChild(): ?Product
    {
        $stockedChildren = Product::where('parent_product_id', $this->getAttribute('id'))
            ->where('active', true)
            ->where('stock', '>', 0)
            ->get();
        if ($stockedChildren->isEmpty()) {
            return null;
        }
        $childWithHighestStock = null;
        $highestStock = 0;
        foreach ($stockedChildren as $stockedChild) {
            if ($stockedChild->getAttribute('stock') > $highestStock) {
                $childWithHighestStock = $stockedChild;
            }
        }
        return $childWithHighestStock;
    }

    public static function clone(Product $product): self
    {
        $clone = new Product();
        $clone->setAttribute('client_id', $product->getAttribute('client_id'));
        $clone->setAttribute('guid', $product->getAttribute('guid'));
        $clone->setAttribute('active', $product->getAttribute('active'));
        $clone->setAttribute('created_at', $product->getAttribute('created_at'));
        $clone->setAttribute('updated_at', $product->getAttribute('updated_at'));
        $clone->setAttribute('parent_product_id', $product->getAttribute('id'));
        $clone->setAttribute('code', $product->getAttribute('code'));
        $clone->save();

        return $clone;
    }
}
