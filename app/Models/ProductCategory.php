<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = null; 
    public $incrementing = false;

    protected $fillable=[
        'category_guid',
        'product_guid',
        'client_id',
    ];

    public function getProduct(): Product
    {
        return Product::findOrFail($this->getAttribute('product_guid'));
    }

    public function setProduct(Product $product): self
    {
        return $this->setAttribute('product_guid', $product->getGuid());
    }

    public function getCategory(): Category
    {
        return Category::findOrFail($this->getAttribute('category_guid'));
    }

    public function setCategory(Category $category): self
    {
        return $this->setAttribute('category_guid', $category->getGuid());
    }
}
