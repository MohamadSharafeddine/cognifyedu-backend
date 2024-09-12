<?php

namespace App\Http\Controllers;

use App\Models\CourseStudent;
use App\Models\User;
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

    public function store(StoreCourseStudentRequest $request, $courseId): JsonResponse
    {
        try {
            $student = User::where('email', $request->input('email'))->first();
    
            if (!$student) {
                return response()->json(['message' => 'Student not found'], 404);
            }
    
            $courseStudent = CourseStudent::create([
                'course_id' => $courseId,
                'student_id' => $student->id,
            ]);
    
            return response()->json([
                'course_student' => $courseStudent,
                'student' => $student,
            ], 201);
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

    public function destroy($courseId, $studentId): JsonResponse
    {
        try {
            $courseStudent = CourseStudent::where('course_id', $courseId)
                ->where('student_id', $studentId)
                ->first();

            if (!$courseStudent) {
                return response()->json(['message' => 'Student not found in this course'], 404);
            }

            $courseStudent->delete();

            return response()->json(['message' => 'Successfully removed student from course'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to remove student from course'], 500);
        }
    }
}
