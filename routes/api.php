<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\MerchantProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group([
    'middleware' => 'api',
    'prefix' => 'auth',
], function($router){
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);
    Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:api');
    Route::post('/refresh',[AuthController::class,'refresh'])->middleware('auth:api');
    Route::get('/profile',[AuthController::class,'profile'])->middleware('auth:api');

    #Merchants
    Route::get('/merchants', [MerchantController::class, 'index']);
    Route::get('/merchants/{id}', [MerchantController::class, 'show']);
    Route::post('/merchants', [MerchantController::class, 'store']);
    Route::put('/merchants/{id}', [MerchantController::class, 'update']);
    Route::delete('/merchants/{id}', [MerchantController::class, 'destroy']);
    Route::get('/nearby-merchants', [MerchantController::class, 'getNearbyMerchants']);

    Route::get('/merchant-products', [MerchantProductController::class, 'index']);
    Route::get('/merchant-products/{id}', [MerchantProductController::class, 'show']);
    Route::post('/merchant-products', [MerchantProductController::class, 'store']);
    Route::put('/merchant-products/{id}', [MerchantProductController::class, 'update']);
    Route::delete('/merchant-products/{id}', [MerchantProductController::class, 'destroy']);

    Route::apiResource('products', ProductController::class);
    Route::apiResource('purchases', PurchaseController::class);
    Route::apiResource('customers', CustomerController::class);
});

