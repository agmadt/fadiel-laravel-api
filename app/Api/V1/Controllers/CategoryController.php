<?php

namespace App\Api\V1\Controllers;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\StoreCategoryRequest;

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

    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create([
            'name' => $request->name
        ]);

        return response()->json([
            'id' => $category->id,
            'name' => $category->name
        ]);
    }
}
