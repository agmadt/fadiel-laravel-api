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
}
