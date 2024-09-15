<?php

namespace App\Http\Controllers;

use App\Models\Insight;
use App\Http\Requests\StoreInsightRequest;
use App\Http\Requests\UpdateInsightRequest;
use Exception;

class InsightController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $insights = Insight::all();
        return response()->json($insights);
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
    public function store(StoreInsightRequest $request)
    {
        $insight = Insight::create($request->all());
        return response()->json($insight);
    }

    /**
     * Display the specified resource.
     */
    public function show(Insight $insight)
    {
        return response()->json($insight);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Insight $insight)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInsightRequest $request, Insight $insight)
    {
        $insight->update($request->validated());
        return response()->json($insight);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Insight $insight)
    {
        $insight->delete();
        return response()->json(['message' => 'Successfully deleted insight'], 200);
    }
    
    public function getUserInsights($studentId)
    {
        $insights = Insight::where('student_id', $studentId)->first();

        if (!$insights) {
            return response()->json(['message' => 'Insights not found'], 404);
        }

        return response()->json($insights);
    }

    public function getLatestInsights($studentId)
    {
        try {
            $latestInsights = Insight::where('student_id', $studentId)->latest()->first();
            
            if (!$latestInsights) {
                return null;
            }
    
            return $latestInsights;
        } catch (Exception $e) {
            return null;
        }
    }
    


}
