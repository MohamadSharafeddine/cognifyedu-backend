<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Assignment;
use App\Http\Requests\StoreSubmissionRequest;
use App\Http\Requests\UpdateSubmissionRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Exception;

class SubmissionController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $submissions = Submission::all();
            return response()->json($submissions);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to fetch submissions'], 500);
        }
    }

    public function store(StoreSubmissionRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            if ($request->hasFile('deliverable')) {
                $data['deliverable'] = $request->file('deliverable')->store('submissions', 'public');
            }
            $submission = Submission::create($data);
            return response()->json($submission, 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to create submission'], 500);
        }
    }

    public function show(Submission $submission): JsonResponse
    {
        return response()->json($submission);
    }

    public function update(UpdateSubmissionRequest $request, Submission $submission): JsonResponse
    {
        try {
            $data = $request->validated();
            if ($request->hasFile('deliverable')) {
                if ($submission->deliverable && Storage::exists($submission->deliverable)) {
                    Storage::delete($submission->deliverable);
                }
                $data['deliverable'] = $request->file('deliverable')->store('submissions', 'public');
            }
            $submission->update($data);
            return response()->json($submission);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update submission'], 500);
        }
    }

    public function destroy(Submission $submission): JsonResponse
    {
        try {
            $submission->delete();
            return response()->json(['message' => 'Successfully deleted submission'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to delete submission'], 500);
        }
    }

    public function getSubmissionsForAssignment($assignmentId): JsonResponse
    {
        try {
            $submissions = Submission::where('assignment_id', $assignmentId)->get();
            return response()->json($submissions);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to fetch submissions'], 500);
        }
    }
}
