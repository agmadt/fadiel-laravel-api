<?php

namespace App\Api\V1\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\StoreProductRequest;
use App\Api\V1\Requests\UpdateProductRequest;
use App\Repositories\ProductVariantsRepository;

class ProductController extends Controller
{
    private $productVariantsRepository;

    public function __construct(ProductVariantsRepository $productVariantsRepository)
    {
        $this->productVariantsRepository = $productVariantsRepository;
    }

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

        $product = Product::create($request->all());

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

    public function update(UpdateProductRequest $request, Product $product)
    {
        DB::beginTransaction();

        if ($request->images) {

            $product->images()->delete();

            foreach ($request->images as $itemImage) {
                $product->images()->create([
                    'image' => $itemImage['image']
                ]);
            }
        }

        if ($request->variants) {

            $this->productVariantsRepository->deleteAllVariantsFromProduct($product);

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

            $product->categories()->delete();

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
