<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\CourseStudentController;
use App\Http\Controllers\ProfileCommentController;
use App\Http\Controllers\CognitiveScoreController;
use App\Http\Controllers\BehavioralScoreController;
use App\Http\Controllers\InsightController;

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

Route::middleware('auth:api')->group(function () {

    Route::prefix('users')->controller(UserController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/{user}', 'show');
        Route::post('/{user}', 'update');
        Route::delete('/{user}', 'destroy');
        Route::post('/logout', 'logout');
    });

    Route::prefix('courses')->controller(CourseController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{course}', 'show');
        Route::post('/{course}', 'update');
        Route::delete('/{course}', 'destroy');
        Route::get('/{courseId}/students', 'getStudents');
        Route::get('/user/{userId}', 'getCoursesByUserId');
    });

    Route::prefix('assignments')->controller(AssignmentController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{assignment}', 'show');
        Route::post('/{assignment}', 'update');
        Route::delete('/{assignment}', 'destroy');
        Route::get('/course/{courseId}', 'getAssignmentsForCourse');
    });

    Route::prefix('submissions')->controller(SubmissionController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{submission}', 'show');
        Route::post('/{submission}', 'update');
        Route::delete('/{submission}', 'destroy');
        Route::get('/assignment/{assignmentId}', 'getSubmissionsForAssignment');
    });

    Route::prefix('course-students')->controller(CourseStudentController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{courseStudent}', 'show');
        Route::post('/{courseStudent}', 'update');
        Route::delete('/{courseStudent}', 'destroy');
    });

    Route::prefix('profile-comments')->controller(ProfileCommentController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{profileComment}', 'show');
        Route::post('/{profileComment}', 'update');
        Route::delete('/{profileComment}', 'destroy');
    });

    Route::prefix('cognitive-scores')->controller(CognitiveScoreController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{cognitiveScore}', 'show');
        Route::post('/{cognitiveScore}', 'update');
        Route::delete('/{cognitiveScore}', 'destroy');
    });

    Route::prefix('behavioral-scores')->controller(BehavioralScoreController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{behavioralScore}', 'show');
        Route::post('/{behavioralScore}', 'update');
        Route::delete('/{behavioralScore}', 'destroy');
    });

    Route::prefix('insights')->controller(InsightController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{insight}', 'show');
        Route::post('/{insight}', 'update');
        Route::delete('/{insight}', 'destroy');
    });
});
