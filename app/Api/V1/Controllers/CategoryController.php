<?php

namespace App\Api\V1\Controllers;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\StoreCategoryRequest;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
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

    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'id' => $category->id,
            'name' => $category->name
        ]);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::create([
            'name' => $request->name
        ]);

        return response()->json([
            'id' => $category->id,
            'name' => $category->name
        ]);
    }

    public function update(StoreCategoryRequest $request, Category $category): JsonResponse
    {
        $category->update([
            'name' => $request->name
        ]);

        return response()->json([
            'id' => $category->id,
            'name' => $category->name
        ]);
    }

    public function delete(Category $category): JsonResponse
    {
        if ($category->products->count() > 0) {
            return response()->json([
                'message' => 'Category is still being used by products'
            ], 403);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category successfully deleted'
        ]);
    }
}
