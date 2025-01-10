<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
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

/**Admin Section */
Route::group(['middleware' => ['auth:sanctum', 'ability:admin']], function () {
});

/** User Section */
Route::post('/user-login', [UserController::class, 'login']);
Route::post('/user-signup', [UserController::class, 'create']);
Route::group(['middleware' => ['auth:sanctum', 'ability:user']], function () {
  Route::get('/user-info', [UserController::class, 'getUserInfoByToken']);
});
