<?php

namespace App\Api\V1\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\StoreProductRequest;
use App\Api\V1\Requests\UpdateProductRequest;
use App\Repositories\ProductVariantsRepository;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    private $productVariantsRepository;

    public function __construct(ProductVariantsRepository $productVariantsRepository)
    {
        $this->productVariantsRepository = $productVariantsRepository;
    }

    public function index(): JsonResponse
    {
        $perPage = request('limit') ? request('limit') : 10;
        $products = Product::orderBy('created_at', 'DESC')->paginate($perPage);
        $productsArr = [];

        foreach ($products->items() as $product) {

            $imagesArr = [];
            $variantsArr = [];
            $categoriesArr = [];

            if ($product->images) {
                foreach ($product->images as $productImage) {
                    $imagesArr[] = [
                        'id' => $productImage->id,
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

            if ($product->categories) {
                foreach ($product->categories as $productCategory) {
                    $categoriesArr[] = [
                        'id' => $productCategory->category_id,
                        'name' => $productCategory->category->name,
                    ];
                }
            }

            $productsArr[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'description' => $product->description,
                'images' => $imagesArr,
                'variants' => $variantsArr,
                'categories' => $categoriesArr,
            ];
        }

        return response()->json([
            'products' => $productsArr,
            'limit' => (int) $perPage,
            'page' => $products->currentPage(),
            'total' => $products->total()
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        $imagesArr = [];
        $variantsArr = [];
        $categoriesArr = [];

        if ($product->images) {
            foreach ($product->images as $productImage) {
                $imagesArr[] = [
                    'id' => $productImage->id,
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

        if ($product->categories) {
            foreach ($product->categories as $productCategory) {
                $categoriesArr[] = [
                    'id' => $productCategory->category_id,
                    'name' => $productCategory->category->name,
                ];
            }
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'images' => $imagesArr,
            'variants' => $variantsArr,
            'categories' => $categoriesArr
        ]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        if ($request->categories) {
            foreach ($request->categories as $itemCategory) {
                $category = Category::where(['id' => $itemCategory['id']])->first();
                if (!$category) {
                    return response()->json([
                        'message' => 'Category not found'
                    ])->setStatusCode(404);
                }
            }
        }

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

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        if ($request->categories) {
            foreach ($request->categories as $itemCategory) {
                $category = Category::where(['id' => $itemCategory['id']])->first();
                if (!$category) {
                    return response()->json([
                        'message' => 'Category not found'
                    ])->setStatusCode(404);
                }
            }
        }

        DB::beginTransaction();

        $product->update($request->all());

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
            'price' => (float) $product->price,
            'description' => $product->description,
        ]);
    }

    public function delete(Product $product): JsonResponse
    {
        DB::beginTransaction();

        $product->images()->delete();
        $product->categories()->delete();
        $this->productVariantsRepository->deleteAllVariantsFromProduct($product);
        $product->delete();

        DB::commit();

        return response()->json([
            'message' => 'Product successfully deleted'
        ]);
    }
}
