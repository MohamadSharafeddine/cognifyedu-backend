<?php

namespace App\Http\Controllers;

use App\Models\ProfileComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use App\Http\Requests\StoreProfileCommentRequest;

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
            $data = $request->validated();
        
            $profileComment = ProfileComment::create($data);
        
            // $aiData = app(AIController::class)->prepareDataForPrompt($data['student_id']);
        
            return response()->json([
                'profile_comment' => $profileComment,
                // 'ai_data' => $aiData,
            ], 201);
        } catch (Exception $e) {
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
