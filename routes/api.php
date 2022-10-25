<?php

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
Route::controller(\App\Http\Controllers\Api\RegisterController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::middleware('auth:sanctum')->group(function(){
    Route::apiResource(name:'/dashboards', controller:\App\Http\Controllers\Api\DashboardController::class);
    Route::apiResource(name:'/departments', controller:\App\Http\Controllers\Api\DepartmentController::class);
    Route::apiResource(name:'/users', controller:\App\Http\Controllers\Api\UserController::class);
    Route::post('users/departments/{user}', '\App\Http\Controllers\Api\UserController@departments');
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

