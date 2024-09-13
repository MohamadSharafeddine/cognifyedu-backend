<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Assignment;
use App\Http\Requests\StoreSubmissionRequest;
use App\Http\Requests\UpdateSubmissionRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Exception;

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
                $data['deliverable'] = Storage::url($filePath);
            }

            $data['submission_date'] = now();

            $submission = Submission::create($data);

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


    public function gradeSubmission(Request $request, $submissionId): JsonResponse
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
            return response()->json(['message' => 'Failed to grade submission'], 500);
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
    
            if (!$submission->deliverable || !Storage::disk('public')->exists($submission->deliverable)) {
                return response()->json(['message' => 'File not found'], 404);
            }
    
            $fileContent = Storage::disk('public')->get($submission->deliverable);
            $fileName = basename($submission->deliverable);
    
            return response($fileContent, 200)
                ->header('Content-Type', 'application/octet-stream')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error downloading file',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    
}
