<?php

use App\Http\Controllers\Voyager\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Voyager\UnitPriceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::put('/update-order/{id}', [OrderController::class, 'update']);
Route::put('/update-unit-price/{id}', [UnitPriceController::class, 'update']);
Route::post('/add-unit-price', [UnitPriceController::class, 'store']);

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
