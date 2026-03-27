<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    // Display Products
    public function getAll()
    {
        return Product::with('category')->latest()->paginate(10);
    }

    // Create Product
    public function create(array $data)
    {
        if (isset($data['image'])) {
            $data['image'] = $data['image']->store('products', 'public');
        }

        return Product::create($data);
    }

    // Update Product
    public function update(Product $product, array $data)
    {
        if (isset($data['image'])) {

            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $data['image']->store('products', 'public');
        }

        $product->update($data);
        return $product;
    }

    // Delete Product
    public function delete(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        return $product->delete();
    }
}
