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

/**Admin Login */
// Route::post('/school-login', [AdminUserController::class, 'schoolLogin']);
// Route::post('/recover-password', [AdminUserController::class, 'recoverPassword']);
// Route::post('/confirm-password-reset-token', [AdminUserController::class, 'confirmPasswordResetToken']);
// Route::post('/reset-password', [AdminUserController::class, 'resetPassword']);
Route::group(['middleware' => ['auth:sanctum', 'ability:admin']], function () {
  //
});
