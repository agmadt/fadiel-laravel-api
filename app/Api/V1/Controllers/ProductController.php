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
}
