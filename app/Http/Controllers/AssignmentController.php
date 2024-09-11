<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Requests\UpdateAssignmentRequest;
use Illuminate\Http\JsonResponse;
use Exception;

class AssignmentController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $assignments = Assignment::all();
            return response()->json($assignments);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to fetch assignments'], 500);
        }
    }

    public function store(StoreAssignmentRequest $request): JsonResponse
    {
        try {
            $assignment = Assignment::create($request->validated());
            return response()->json($assignment, 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to create assignment'], 500);
        }
    }

    public function show(Assignment $assignment): JsonResponse
    {
        return response()->json($assignment);
    }

    public function update(UpdateAssignmentRequest $request, Assignment $assignment): JsonResponse
    {
        try {
            $assignment->update($request->validated());
            return response()->json($assignment);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update assignment'], 500);
        }
    }

    public function destroy(Assignment $assignment): JsonResponse
    {
        try {
            $assignment->delete();
            return response()->json(['message' => 'Successfully deleted assignment'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to delete assignment'], 500);
        }
    }
}
