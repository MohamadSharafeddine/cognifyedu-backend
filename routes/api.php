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
use App\Http\Controllers\AIController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
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
        Route::get('/{user}/profile-picture', 'downloadProfilePicture');
        Route::get('/email/{email}', 'getUserByEmail');
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
        Route::get('{assignmentId}/download', 'downloadAttachment');
        Route::get('/course/{courseId}', 'getAssignmentsForCourse');
    });

    Route::prefix('submissions')->controller(SubmissionController::class)->group(function () {
        Route::get('/assignment/{assignmentId}', 'getSubmissionsForAssignment');
        Route::post('/', 'store');
        Route::post('/{submission}', 'update');
        Route::post('/{submissionId}/mark', 'markSubmission');
        Route::get('/{submissionId}/download', 'downloadFile');
        Route::delete('/{submission}', 'destroy');
        Route::get('/{submission}', 'show');
        Route::get('/student/{studentId}/new-submissions', 'getNewSubmissions');
    });

    Route::prefix('course-students')->controller(CourseStudentController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/{courseId}', 'store');
        Route::get('/{courseStudent}', 'show');
        Route::post('/{courseStudent}', 'update');
        Route::delete('/{courseId}/{studentId}', 'destroy');
    });

    Route::prefix('profile-comments')->controller(ProfileCommentController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{profileComment}', 'show');
        Route::post('/{profileComment}', 'update');
        Route::delete('/{profileComment}', 'destroy');
        Route::get('/student/{studentId}', 'getProfileComments');
        Route::get('/student/{studentId}/latest', 'getLastProfileComment');
    });

    Route::prefix('cognitive-scores')->controller(CognitiveScoreController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{cognitiveScore}', 'show');
        Route::post('/{cognitiveScore}', 'update');
        Route::delete('/{cognitiveScore}', 'destroy');
        Route::get('/{userId}/average', 'getUserAverageScores');
        Route::get('/{userId}/progress', 'getUserScoresProgress'); 
    });
    
    Route::prefix('behavioral-scores')->controller(BehavioralScoreController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{behavioralScore}', 'show');
        Route::post('/{behavioralScore}', 'update');
        Route::delete('/{behavioralScore}', 'destroy');
        Route::get('/{userId}/average', 'getUserAverageScores');
        Route::get('/{userId}/progress', 'getUserScoresProgress');
    });
    
    Route::prefix('insights')->controller(InsightController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{insight}', 'show');
        Route::post('/{insight}', 'update');
        Route::delete('/{insight}', 'destroy');
        Route::get('/user/{userId}', 'getUserInsights');
    });

    Route::get('/ai/assessment/{studentId}', [AIController::class, 'analyzeStudentPerformance']);
});
