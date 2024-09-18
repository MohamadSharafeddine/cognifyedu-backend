<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Assignment;
use App\Http\Requests\StoreSubmissionRequest;
use App\Http\Requests\UpdateSubmissionRequest;
use App\Models\ProfileComment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log as Log;

class SubmissionController extends Controller
{
    public function getSubmissionsForAssignment($assignmentId): JsonResponse
    {
        try {
            $submissions = Submission::where('assignment_id', $assignmentId)
                ->with('student:id,name,email')
                ->get();

            return response()->json($submissions);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to fetch submissions'], 500);
        }
    }

    public function store(StoreSubmissionRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
    
            $data['assignment_id'] = $request->input('assignment_id');
            $data['student_id'] = Auth::id();
    
            if ($request->hasFile('deliverable')) {
                $filePath = $request->file('deliverable')->store('submissions', 'public');
                $data['deliverable'] = $filePath;
            }
    
            $data['submission_date'] = now();
    
            $submission = Submission::create($data);
    
            if ($submission->deliverable) {
                $submission->deliverable_url = Storage::url($submission->deliverable);
            }
    
            $submission->load('student:id,name,email');
    
            return response()->json($submission, 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to create submission'], 500);
        }
    }
    

    public function update(UpdateSubmissionRequest $request, Submission $submission): JsonResponse
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('deliverable')) {
                if ($submission->deliverable && Storage::exists('public/' . $submission->deliverable)) {
                    Storage::delete('public/' . $submission->deliverable);
                }
                $filePath = $request->file('deliverable')->store('submissions', 'public');
                $data['deliverable'] = Storage::url($filePath);
            }

            $data['submission_date'] = now();
            $submission->update($data);

            $submission->load('student:id,name,email');

            return response()->json($submission);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update submission'], 500);
        }
    }


    public function markSubmission(Request $request, $submissionId): JsonResponse
    {
        try {
            $submission = Submission::findOrFail($submissionId);

            $submission->update([
                'mark' => $request->input('mark'),
                'teacher_comment' => $request->input('teacher_comment'),
            ]);

            $submission->load('student:id,name,email');

            return response()->json($submission);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to mark submission'], 500);
        }
    }


    public function show(Submission $submission): JsonResponse
    {
        $submission->load('student:id,name,email');

        return response()->json($submission);
    }


    public function destroy(Submission $submission): JsonResponse
    {
        try {
            if ($submission->deliverable && Storage::exists('public/' . $submission->deliverable)) {
                Storage::delete('public/' . $submission->deliverable);
            }

            $submission->delete();
            return response()->json(['message' => 'Successfully deleted submission'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to delete submission'], 500);
        }
    }


    public function downloadFile($submissionId)
    {
        try {
            $submission = Submission::findOrFail($submissionId);
    
            if (!$submission->deliverable) {
                return response()->json(['message' => 'File not found'], 404);
            }
    
            $filePath = storage_path('app/public/' . $submission->deliverable);
    
            if (!file_exists($filePath)) {
                return response()->json(['message' => 'File not found in storage'], 404);
            }
    
            return response()->download($filePath);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error downloading file',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function getNewSubmissions($studentId)
    {
        try {
            $lastComment = ProfileComment::where('student_id', $studentId)->latest('created_at')->first();
            $lastCommentDate = $lastComment ? $lastComment->created_at : null;
    
            $query = Submission::where('student_id', $studentId)
                ->whereNotNull('mark');
    
            if ($lastCommentDate) {
                $query->where('updated_at', '>', $lastCommentDate);
            }
    
            $newSubmissions = $query->with('assignment:id,title,description')->get();
    
            foreach ($newSubmissions as $submission) {
                if ($submission->deliverable) {
                    $filePath = storage_path('app/public/' . $submission->deliverable);
                    if (file_exists($filePath)) {
                        $submission->deliverable_content = file_get_contents($filePath);
                    } else {
                        $submission->deliverable_content = null;
                    }
                }
            }
    
            return response()->json($newSubmissions);
        } catch (Exception $e) {
            Log::error('Error in getNewSubmissions: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch new submissions'], 500);
        }
    }
    
    

    
    
}
