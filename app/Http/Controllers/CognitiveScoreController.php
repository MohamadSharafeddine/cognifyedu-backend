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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CognitiveScore $cognitiveScore)
    {
        //
    }
}
