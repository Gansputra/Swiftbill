<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductService
{
    public function getAllProducts()
    {
        return Product::with(['category', 'supplier'])->latest()->get();
    }

    public function createProduct(array $data)
    {
        if (empty($data['sku'])) {
            $data['sku'] = strtoupper(Str::random(10));
        }
        return Product::create($data);
    }

    public function updateProduct(Product $product, array $data)
    {
        $product->update($data);
        return $product;
    }

    public function deleteProduct(Product $product)
    {
        return $product->delete();
    }
}
