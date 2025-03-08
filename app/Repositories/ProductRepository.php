<?php

namespace App\Repositories;

use App\Models\Product;
use App\BO\ProductBO;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAll($categoryId = null, $searchKeyword = null)
    {
        $query = Product::query();
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($searchKeyword) {
            $query->where(function ($q) use ($searchKeyword) {
                $q->where('name', 'like', '%' . $searchKeyword . '%')
                  ->orWhere('description', 'like', '%' . $searchKeyword . '%');
            });
        }

        return $query->paginate(10);
    }

    public function getById($id)
    {
        return Product::findOrFail($id);
    }

    public function create(ProductBO $productBO)
    {
        return Product::create([
            'name' => $productBO->name,
            'description' => $productBO->description,
            'sku' => $productBO->sku,
            'price' => $productBO->price,
            'category_id' => $productBO->category_id,
        ]);
    }

    public function update($id, ProductBO $productBO)
    {
        $product = Product::findOrFail($id);
        $product->update([
            'name' => $productBO->name ?? $product->name,
            'description' => $productBO->description ?? $product->description,
            'sku' => $productBO->sku ?? $product->sku,
            'price' => $productBO->price ?? $product->price,
            'category_id' => $productBO->category_id ?? $product->category_id,
        ]);
        return $product;
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
    }
}