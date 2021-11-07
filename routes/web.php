<?php

use App\Http\Controllers\Voyager\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Voyager\UnitPriceController;
 
 use Illuminate\Support\Facades\File;
 use Illuminate\Support\Facades\Response;

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
Route::get('/get-pdf/{id}', [OrderController::class, 'createPDF']);

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});


Route::get('download/{file}', [OrderController::class ,'getPdf']);



// Route::get('storage/{filename}', function ($filename)
// {
//     $path = storage_path('public/' . $filename);

//     if (!File::exists($path)) {
//         abort(404);
//     }

//     $file = File::get($path);
//     $type = File::mimeType($path);

//     $response = Response::make($file, 200);
//     $response->header("Content-Type", $type);

//     return $response;
// });
