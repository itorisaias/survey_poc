<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SurveyController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/customers', [CustomerController::class, 'store']);
Route::post('/surveys', [SurveyController::class, 'store']);
Route::get('/surveys/{survey}/customers/{customer}/answer', [SurveyController::class, 'answer']);
Route::get('/surveys/{survey}/result', [SurveyController::class, 'result']);
