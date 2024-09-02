<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\CourseStudentController;
use App\Http\Controllers\ProfileCommentController;

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
    Route::post('/{user}','update');
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
    Route::post('/{course}', 'update');
    Route::delete('/{course}', 'destroy');
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'assignments',
    'controller' => AssignmentController::class
], function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('/{assignment}', 'show');
    Route::post('/{assignment}', 'update');
    Route::delete('/{assignment}', 'destroy');
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'submissions',
    'controller' => SubmissionController::class
], function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('/{submission}', 'show');
    Route::post('/{submission}', 'update');
    Route::delete('/{submission}', 'destroy');
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'course-students',
    'controller' => CourseStudentController::class
], function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('/{courseStudent}', 'show');
    Route::post('/{courseStudent}', 'update');
    Route::delete('/{courseStudent}', 'destroy');
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'profile-comments',
    'controller' => ProfileCommentController::class
], function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
});
