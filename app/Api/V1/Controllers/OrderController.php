<?php

namespace App\Api\V1\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ProductVariantOption;
use App\Api\V1\Requests\StoreOrderRequest;

class OrderController extends Controller
{
    public function index()
    {
        $perPage = request('limit') ? request('limit') : 10;
        $orders = Order::orderBy('created_at', 'DESC')->paginate($perPage);
        $orderArr = [];

        foreach ($orders->items() as $item) {
            $orderArr[] = [
                'id' => $item->id,
                'buyer_name' => $item->buyer_name,
                'buyer_email' => $item->buyer_email,
                'total' => $item->total,
                'message' => $item->message,
                'created_at' => $item->created_at->timestamp,
            ];
        }

        return response()->json([
            'orders' => $orderArr,
            'limit' => (int) $perPage,
            'page' => $orders->currentPage(),
            'total' => $orders->total()
        ]);
    }

    public function show(Order $order)
    {
        $products = [];

        foreach ($order->products as $item) {
            $product = $item->productJSON;
            $products[] = $product;
        }

        return response()->json([
            'id' => $order->id,
            'buyer_name' => $order->buyer_name,
            'buyer_email' => $order->buyer_email,
            'total' => $order->total,
            'message' => $order->message,
            'created_at' => $order->created_at->timestamp,
            'products' => $products
        ]);
    }

    public function store(StoreOrderRequest $request)
    {
        $total = 0;
        $productsArr = [];
        foreach ($request->products as $item) {
            $imagesArr = [];
            $variantsArr = [];
            $categoriesArr = [];
            $product = Product::find($item['id']);

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found'
                ], 404);
            }

            if (count($item['variants']) > 0) {
                foreach ($item['variants'] as $itemVariant) {
                    $productVariantOption = ProductVariantOption::where([
                        'id' => $itemVariant['option_id'],
                        'product_variant_id' => $itemVariant['variant_id'],
                    ])->first();

                    if (!$productVariantOption) {
                        return response()->json([
                            'message' => 'Product variant not found'
                        ], 404);
                    }

                    $variantsArr[] = [
                        'variant_id' => $productVariantOption->product_variant_id,
                        'variant_name' => $productVariantOption->variant->name,
                        'variant_option_id' => $productVariantOption->id,
                        'variant_option_name' => $productVariantOption->name,
                    ];
                }
            }

            if ($product->images) {
                foreach ($product->images as $image) {
                    $imagesArr[] = [
                        'image' => $image->image
                    ];
                }
            }

            if ($product->categories) {
                foreach ($product->categories as $category) {

                    if (empty($category->category)) {
                        continue;
                    }

                    $categoriesArr[] = [
                        'name' => $category->category->name
                    ];
                }
            }

            $productsArr[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'description' => $product->description,
                'quantity' => $item['quantity'],
                'subtotal' => $product->price * $item['quantity'],
                'images' => $imagesArr,
                'variants' => $variantsArr,
                'categories' => $categoriesArr
            ];

            $total += $product->price * $item['quantity'];
        }

        DB::beginTransaction();

        $order = Order::create([
            'buyer_name' => $request->buyer_name,
            'buyer_email' => $request->buyer_email,
            'message' => $request->buyer_message,
            'total' => $total
        ]);

        foreach ($productsArr as $item) {
            $order->products()->create([
                'product' => json_encode($item)
            ]);
        }

        DB::commit();

        return response()->json([
            'id' => $order->id,
            'buyer_name' => $order->buyer_name,
            'buyer_email' => $order->buyer_email,
            'message' => $order->message,
            'created_at' => $order->created_at->timestamp,
        ]);
    }
}
