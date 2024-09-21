<?php

namespace App\Http\Controllers;

use App\Models\CognitiveScore;
use App\Http\Requests\StoreCognitiveScoreRequest;
use App\Http\Requests\UpdateCognitiveScoreRequest;

class CognitiveScoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cognitiveScores = CognitiveScore::all();
        return response()->json($cognitiveScores);
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
    public function store(StoreCognitiveScoreRequest $request)
    {
        $score = CognitiveScore::create($request->validated());
        return response()->json($score, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CognitiveScore $cognitiveScore)
    {
        return response()->json($cognitiveScore);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CognitiveScore $cognitiveScore)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCognitiveScoreRequest $request, CognitiveScore $cognitiveScore)
    {
        $cognitiveScore->update($request->validated());
        return response()->json($cognitiveScore);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CognitiveScore $cognitiveScore)
    {
        $cognitiveScore->delete();
        return response()->json(['message' => 'Successfully deleted cognitive score'], 200);
    }

    public function getUserAverageScores($userId) {
        $averageScores = CognitiveScore::where('student_id', $userId)
            ->selectRaw('ROUND(AVG(critical_thinking)) as critical_thinking, 
                         ROUND(AVG(logical_thinking)) as logical_thinking, 
                         ROUND(AVG(linguistic_ability)) as linguistic_ability, 
                         ROUND(AVG(memory)) as memory, 
                         ROUND(AVG(attention_to_detail)) as attention_to_detail')
            ->first();
        
        return response()->json($averageScores);
    }
    
    public function getUserScoresProgress($userId)
    {
        $cognitiveScores = CognitiveScore::where('student_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get(['critical_thinking', 'logical_thinking', 'linguistic_ability', 'memory', 'attention_to_detail', 'created_at']);
        
        return response()->json($cognitiveScores);
    }

}
