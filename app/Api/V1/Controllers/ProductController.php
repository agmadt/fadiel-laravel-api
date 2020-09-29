<?php

namespace App\Api\V1\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        $perPage = request('limit') ? request('limit') : 10;
        $products = Product::orderBy('created_at', 'DESC')->paginate($perPage);
        $productsArr = [];

        foreach ($products->items() as $product) {
            $productsArr[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'description' => $product->description,
                'created_at' => $product->created_at->timestamp,
            ];
        }

        return response()->json([
            'orders' => $productsArr,
            'limit' => (int) $perPage,
            'page' => $products->currentPage(),
            'total' => $products->total()
        ]);
    }

    public function show(Product $product)
    {
        $imagesArr = [];
        $variantsArr = [];

        if ($product->images) {
            foreach ($product->images as $productImage) {
                $imagesArr[] = [
                    'image' => $productImage->image
                ];
            }
        }

        if ($product->variants) {
            foreach ($product->variants as $productVariant) {
                $variantOptions = [];

                if ($productVariant->options) {
                    foreach ($productVariant->options as $productOption) {
                        $variantOptions[] = [
                            'id' => $productOption->id,
                            'name' => $productOption->name
                        ];
                    }
                }

                $variantsArr[] = [
                    'id' => $productVariant->id,
                    'name' => $productVariant->name,
                    'options' => $variantOptions
                ];
            }
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'images' => $imagesArr,
            'variants' => $variantsArr
        ]);
    }
}
