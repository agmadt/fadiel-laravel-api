<?php

namespace App\Api\V1\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
}
