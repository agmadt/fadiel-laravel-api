<?php

namespace App\Api\V1\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $perPage = request('limit') ? request('limit') : 10;
        $categories = Category::orderBy('created_at', 'DESC')->paginate($perPage);
        $categoriesArr = [];

        foreach ($categories->items() as $item) {
            $categoriesArr[] = [
                'id' => $item->id,
                'name' => $item->name
            ];
        }

        return response()->json([
            'categories' => $categoriesArr,
            'limit' => (int) $perPage,
            'page' => $categories->currentPage(),
            'total' => $categories->total()
        ]);
    }

    public function show(Category $category)
    {
        return response()->json([
            'id' => $category->id,
            'name' => $category->name
        ]);
    }
}
