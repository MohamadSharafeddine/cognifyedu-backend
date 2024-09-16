<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\User;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    protected function generateCourseCode(): string
    {
        do {
            $code = strtoupper(substr(md5(microtime()), 0, 8));
        } while (Course::where('code', $code)->exists());
        return $code;
    }

    public function index(): JsonResponse
    {
        try {
            $courses = Course::with('teacher:id,name')->get();
            return response()->json($courses);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to fetch courses'], 500);
        }
    }
    
    public function getCoursesByUserId($userId): JsonResponse
    {
        try {
            $user = User::findOrFail($userId);
    
            if ($user->type === 'teacher') {
                $courses = Course::with('teacher:id,name')
                                 ->where('teacher_id', $user->id)
                                 ->get();
            } else {
                $courseIds = CourseStudent::where('student_id', $user->id)->pluck('course_id');
                $courses = Course::with('teacher:id,name')
                                 ->whereIn('id', $courseIds)
                                 ->get();
            }
    
            return response()->json($courses);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to fetch courses for the user'], 500);
        }
    }
    

    public function store(StoreCourseRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
    
            if ($user->type !== 'teacher') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
    
            $data = $request->validated();
            $data['teacher_id'] = $user->id;
            $data['code'] = $this->generateCourseCode();
            $course = Course::create($data);
    
            $course->load('teacher');
    
            return response()->json($course, 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to create course'], 500);
        }
    }

    public function show(Course $course): JsonResponse
    {
        return response()->json($course);
    }

    public function update(UpdateCourseRequest $request, Course $course): JsonResponse
    {
        try {
            $course->update($request->validated());
            return response()->json($course);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update course'], 500);
        }
    }

    public function destroy(Course $course): JsonResponse
    {
        try {
            $course->delete();
            return response()->json(['message' => 'Successfully deleted course'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to delete course'], 500);
        }
    }

    public function getStudents($courseId): JsonResponse
    {
        try {
            $course = Course::findOrFail($courseId);
            $students = $course->CourseStudents->map(function ($courseStudent) {
                $student = $courseStudent->student;
    
                if ($student->profile_picture) {
                    $student->profile_picture = Storage::url($student->profile_picture);
                }
    
                return $student;
            });
    
            return response()->json($students);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to fetch students'], 500);
        }
    }
    
    
    
}
