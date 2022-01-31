<?php

use App\Http\Controllers\LinkController;
use App\Http\Controllers\LinkStatisticsController;
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

Route::get('/links', [LinkController::class, 'index']);
Route::get('/links/{id}', [LinkController::class, 'show']);
Route::post('/links', [LinkController::class, 'store']);
Route::patch('/links/{id}', [LinkController::class, 'update']);
Route::delete('/links/{id}', [LinkController::class, 'destroy']);

Route::get('/stats', [LinkStatisticsController::class, 'showOverall']);
Route::get('/stats/{id}', [LinkStatisticsController::class, 'showForLink']);
