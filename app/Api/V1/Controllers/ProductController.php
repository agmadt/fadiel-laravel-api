<?php

namespace App\Api\V1\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\StoreProductRequest;

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

    public function store(StoreProductRequest $request)
    {
        DB::beginTransaction();

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
        ]);

        if ($request->images) {
            foreach ($request->images as $itemImage) {
                $product->images()->create([
                    'image' => $itemImage['image']
                ]);
            }
        }

        if ($request->variants) {
            foreach ($request->variants as $itemVariant) {
                $variant = $product->variants()->create([
                    'name' => $itemVariant['name']
                ]);

                foreach ($itemVariant['options'] as $itemVariantOption) {
                    $variant->options()->create([
                        'name' => $itemVariantOption['name']
                    ]);
                }
            }
        }

        if ($request->categories) {
            foreach ($request->categories as $itemCategory) {
                $variant = $product->categories()->create([
                    'category_id' => $itemCategory['id']
                ]);
            }
        }

        DB::commit();

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'description' => $product->description,
        ]);
    }
}
