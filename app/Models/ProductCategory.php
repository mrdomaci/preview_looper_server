<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $fillable=[
        'product_id',
        'category_id',
    ];

    public function getProduct(): Product
    {
        return Product::findOrFail($this->getAttribute('product_id'));
    }

    public function setProduct(Product $product): self
    {
        return $this->setAttribute('product_id', $product->getId());
    }

    public function getCategory(): Category
    {
        return Category::findOrFail($this->getAttribute('category_id'));
    }

    public function setCategory(Category $category): self
    {
        return $this->setAttribute('category_id', $category->getId());
    }
}
