<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TemperatureController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/getOutfit', [TemperatureController::class, 'get_outfit']);

//Test
Route::get('/test_temperature/{temperature}', [TemperatureController::class, 'test_temperature']);
Route::get('/test_outfit/{weather}', [TemperatureController::class, 'test_outfit']);

