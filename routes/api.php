<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AssignmentController;

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

Route::post('/register', [UserController::class, 'store']);
Route::post('/login', [UserController::class, 'login']);

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'users',
    'controller' => UserController::class], function () {
    Route::get('/', 'index');
    Route::get('/{user}', 'show');
    Route::put('/{user}','update');
    Route::delete('/{user}', 'destroy');
    Route::post('/logout',  'logout');
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'courses',
    'controller' => CourseController::class
], function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('/{course}', 'show');
    Route::put('/{course}', 'update');
    Route::delete('/{course}', 'destroy');
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'assignments',
    'controller' => AssignmentController::class
], function () {
});
