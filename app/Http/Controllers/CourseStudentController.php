<?php

namespace App\Http\Controllers;

use App\Models\CourseStudent;
use App\Http\Requests\StoreCourseStudentRequest;
use App\Http\Requests\UpdateCourseStudentRequest;

class CourseStudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courseStudents = CourseStudent::all();
        return response()->json($courseStudents);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseStudentRequest $request)
    {
        $courseStudent = CourseStudent::create($request->validated());
        return response()->json($courseStudent, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseStudent $CourseStudent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseStudent $CourseStudent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseStudentRequest $request, CourseStudent $CourseStudent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseStudent $CourseStudent)
    {
        //
    }
}
