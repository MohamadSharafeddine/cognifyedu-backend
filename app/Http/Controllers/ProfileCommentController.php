<?php

namespace App\Http\Controllers;

use App\Models\ProfileComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use App\Http\Requests\StoreProfileCommentRequest;
use Illuminate\Support\Facades\Log as Log;

class ProfileCommentController extends Controller
{
    public function index()
    {
        $profileComments = ProfileComment::all();
        return response()->json($profileComments);
    }

    public function store(StoreProfileCommentRequest $request): JsonResponse
    {
        try {
            Log::info('Storing profile comment: ', $request->all());
            
            $data = $request->validated();
            
            Log::info('Validated profile comment data: ', $data);
    
            $profileComment = ProfileComment::create($data);
            
            Log::info('Created profile comment: ', $profileComment->toArray());
    
            $aiController = app(AIController::class);
            $aiResult = $aiController->analyzeStudentPerformance($data['student_id']);
    
            Log::info('AI analysis completed: ', $aiResult);
    
            return response()->json([
                'profile_comment' => $profileComment,
                'ai_response' => $aiResult,
            ], 201);
        } catch (Exception $e) {
            Log::error('Error storing profile comment: ' . $e->getMessage());
            
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    
    

    public function getProfileComments($studentId): JsonResponse
    {
        try {
            $comments = ProfileComment::where('student_id', $studentId)->get();

            return response()->json($comments);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to fetch profile comments'], 500);
        }
    }

    public function getLastProfileComment($studentId): JsonResponse
    {
        try {
            $lastComment = ProfileComment::where('student_id', $studentId)->latest()->first();

            return response()->json($lastComment);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to fetch latest profile comment'], 500);
        }
    }
}
