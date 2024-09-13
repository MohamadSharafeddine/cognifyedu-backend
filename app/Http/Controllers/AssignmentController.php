<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Requests\UpdateAssignmentRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Exception;

class AssignmentController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $assignments = Assignment::all();
            
            $assignments->each(function ($assignment) {
                if ($assignment->attachment) {
                    $assignment->attachment_url = Storage::url($assignment->attachment);
                }
            });

            return response()->json($assignments);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to fetch assignments'], 500);
        }
    }

    public function store(StoreAssignmentRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('attachment')) {
                $data['attachment'] = $request->file('attachment')->store('assignments', 'public');
            }

            $assignment = Assignment::create($data);

            if ($assignment->attachment) {
                $assignment->attachment_url = Storage::url($assignment->attachment);
            }

            return response()->json($assignment, 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to create assignment'], 500);
        }
    }

    public function show(Assignment $assignment): JsonResponse
    {
        if ($assignment->attachment) {
            $assignment->attachment_url = asset('storage/' . $assignment->attachment);
        }
    
        return response()->json($assignment);
    }

    public function update(UpdateAssignmentRequest $request, Assignment $assignment): JsonResponse
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('attachment')) {
                if ($assignment->attachment && Storage::exists($assignment->attachment)) {
                    Storage::delete($assignment->attachment);
                }

                $data['attachment'] = $request->file('attachment')->store('assignments', 'public');
            }

            $assignment->update($data);

            if ($assignment->attachment) {
                $assignment->attachment_url = Storage::url($assignment->attachment);
            }

            return response()->json($assignment);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update assignment'], 500);
        }
    }

    public function destroy(Assignment $assignment): JsonResponse
    {
        try {
            if ($assignment->attachment && Storage::exists($assignment->attachment)) {
                Storage::delete($assignment->attachment);
            }
            
            $assignment->delete();
            return response()->json(['message' => 'Successfully deleted assignment'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to delete assignment'], 500);
        }
    }

    public function getAssignmentsForCourse($courseId): JsonResponse
    {
        try {
            $assignments = Assignment::where('course_id', $courseId)->get();

            $assignments->each(function ($assignment) {
                if ($assignment->attachment) {
                    $assignment->attachment_url = Storage::url($assignment->attachment);
                }
            });

            return response()->json($assignments);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to fetch assignments'], 500);
        }
    }

    public function downloadAttachment($assignmentId)
    {
        try {
            $assignment = Assignment::findOrFail($assignmentId);
    
            if (!$assignment->attachment) {
                return response()->json(['message' => 'No attachment found for this assignment'], 404);
            }
    
            $filePath = storage_path('app/public/' . $assignment->attachment);
    
            if (!file_exists($filePath)) {
                return response()->json(['message' => 'File not found in storage'], 404);
            }
    
            return response()->download($filePath);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error downloading file', 'error' => $e->getMessage()], 500);
        }
    }    
}
