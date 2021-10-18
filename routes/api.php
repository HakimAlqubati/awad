<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UnitPriceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth:api'])->group(function () {
    Route::post('/add-order', [OrderController::class, 'store']);
    Route::get('get-units', [UnitController::class, 'index']);
    Route::get('/get-unit-prices', [UnitPriceController::class, 'index']);
    Route::get('/products',  [ProductController::class, 'index']);
});





Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
