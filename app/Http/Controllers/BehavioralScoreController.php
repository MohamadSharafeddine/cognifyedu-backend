<?php

namespace App\Http\Controllers;

use App\Models\BehavioralScore;
use App\Http\Requests\StoreBehavioralScoreRequest;
use App\Http\Requests\UpdateBehavioralScoreRequest;

class BehavioralScoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $behavioralScores = BehavioralScore::all();
        return response()->json($behavioralScores);
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
    public function store(StoreBehavioralScoreRequest $request)
    {
        $behavioralScore = BehavioralScore::create($request->validated());
        return response()->json($behavioralScore, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(BehavioralScore $behavioralScore)
    {
        return response()->json($behavioralScore);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BehavioralScore $behavioralScore)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBehavioralScoreRequest $request, BehavioralScore $behavioralScore)
    {
        $behavioralScore->update($request->validated());
        return response()->json($behavioralScore);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BehavioralScore $behavioralScore)
    {
        $behavioralScore->delete();
        return response()->json(['message' => 'Successfully deleted behavioral score'], 200);
    }
    public function getUserAverageScores($userId)
    {
        $averageScores = BehavioralScore::where('user_id', $userId)
            ->selectRaw('AVG(engagement) as engagement, AVG(time_management) as time_management, AVG(adaptability) as adaptability, AVG(collaboration) as collaboration, AVG(focus) as focus')
            ->first();
        
        return response()->json($averageScores);
    }

}
