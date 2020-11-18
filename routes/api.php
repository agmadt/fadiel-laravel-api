<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Api\V1\Controllers\AuthController;
use App\Api\V1\Controllers\MediaController;
use App\Api\V1\Controllers\OrderController;
use App\Api\V1\Controllers\ProductController;
use App\Api\V1\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('cache.headers:public;max_age=3600;etag')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store'])->middleware('api.auth');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->middleware('api.auth');
    Route::patch('/categories/{category}', [CategoryController::class, 'update'])->middleware('api.auth');
    Route::delete('/categories/{category}', [CategoryController::class, 'delete'])->middleware('api.auth');

    Route::post('/media', [MediaController::class, 'store'])->middleware('api.auth');

    Route::get('/orders', [OrderController::class, 'index'])->middleware('api.auth');
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);

    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store'])->middleware('api.auth');
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::patch('/products/{product}', [ProductController::class, 'update'])->middleware('api.auth');
    Route::delete('/products/{product}', [ProductController::class, 'delete'])->middleware('api.auth');
});
