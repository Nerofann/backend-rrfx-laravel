<?php

use App\Http\Controllers\Api_v1\AuthController;
use App\Http\Controllers\Api_v1\PublicController;
use App\Http\Controllers\Api_v1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix("/auth")->group(function() {
    // Define auth routes here
    Route::post('/login', [AuthController::class, 'login']);
    Route::put('/register', [AuthController::class, 'register']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

Route::prefix("/public")->group(function() {
    // Define public routes here
    Route::get('/countries', [PublicController::class, 'countries']);
});

Route::middleware('auth:api')->group(function() {
    Route::get('/user/profile', [UserController::class, 'userinfo']);
});