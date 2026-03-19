<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Synchronize;
use App\Http\Controllers\API;
use App\Http\Controllers\TestPatternImageController;

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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('/sync', [Synchronize::class, 'action']);
Route::get('/sync', [Synchronize::class, 'index']);

Route::post('/register', [API::class, 'register']);
Route::post('/unregister', [API::class, 'unregister']);

Route::get('/testpatternimages', [TestPatternImageController::class, 'index']);
