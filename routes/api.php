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
  /** actions on self */

  /** actions on drivers */

  /** actions on users */
  Route::post('/admin-create-user', [UserController::class, 'create']);
  Route::put('/admin-edit-user/{userId}', [UserController::class, 'edit']);
  Route::put('/admin-delete-user/{userId}', [UserController::class, 'delete']);
  Route::post('/admin-upload-user-profile-pic', [UserController::class, 'uploadProfilePic']);
  Route::put('/admin-change-user-password/{userId}', [UserController::class, 'changePassword']);
});

/** User Section */
Route::post('/user-login', [UserController::class, 'login']);
Route::post('/user-signup', [UserController::class, 'create']);
Route::post('/user-recover-password', [UserController::class, 'recoverPassword']);
Route::post('/user-confirm-password-reset-token', [UserController::class, 'confirmPasswordResetToken']);
Route::post('/user-reset-password', [UserController::class, 'resetPassword']);

Route::group(['middleware' => ['auth:sanctum', 'ability:user']], function () {
  Route::get('/user-info', [UserController::class, 'getUserInfoByToken']);
  Route::put('/user-edit/{userId}', [UserController::class, 'edit']);
  Route::post('/user-upload-profile-pic', [UserController::class, 'uploadProfilePic']);
  Route::put('/user-change-password/{userId}', [UserController::class, 'changePassword']);
});
