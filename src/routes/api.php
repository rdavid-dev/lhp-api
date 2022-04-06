<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\NoteController;
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

Route::group([
    'prefix' => 'v1/',
    'middleware' => ['json.response']
], function() {
    Route::post('/authenticate', LoginController::class);
    Route::post('/register', RegisterController::class);

    Route::group([
        'middleware' => ['auth:sanctum']
    ], function() {
        Route::get('/logout', LogoutController::class);
        Route::apiResource('notes', NoteController::class);
    });
});