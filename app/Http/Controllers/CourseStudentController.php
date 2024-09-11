<?php

namespace App\Http\Controllers;

use App\Models\CourseStudent;
use App\Http\Requests\StoreCourseStudentRequest;
use App\Http\Requests\UpdateCourseStudentRequest;
use Illuminate\Http\JsonResponse;
use Exception;

class CourseStudentController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $courseStudents = CourseStudent::all();
            return response()->json($courseStudents);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to fetch course-student relationships'], 500);
        }
    }

    public function store(StoreCourseStudentRequest $request): JsonResponse
    {
        try {
            $courseStudent = CourseStudent::create($request->validated());
            return response()->json($courseStudent, 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to add student to course'], 500);
        }
    }

    public function show(CourseStudent $courseStudent): JsonResponse
    {
        try {
            return response()->json($courseStudent);
        } catch (Exception $e) {
            return response()->json(['message' => 'Course-student relationship not found'], 404);
        }
    }

    public function update(UpdateCourseStudentRequest $request, CourseStudent $courseStudent): JsonResponse
    {
        try {
            $courseStudent->update($request->validated());
            return response()->json($courseStudent);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update course-student relationship'], 500);
        }
    }

    public function destroy(CourseStudent $courseStudent): JsonResponse
    {
        try {
            $courseStudent->delete();
            return response()->json(['message' => 'Successfully removed student from course'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to remove student from course'], 500);
        }
    }
}
