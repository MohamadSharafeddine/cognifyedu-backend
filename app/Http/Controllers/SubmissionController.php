<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Http\Requests\StoreSubmissionRequest;
use App\Http\Requests\UpdateSubmissionRequest;
use Illuminate\Support\Facades\Storage;
class SubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $submissions = Submission::all();
        return response()->json($submissions);
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
    public function store(StoreSubmissionRequest $request)
{
    $data = $request->validated();

    if ($request->hasFile('deliverable')) {
        $file = $request->file('deliverable');
        $filePath = $file->store('submissions', 'public');
    }

    $data['deliverable'] = $filePath;
    $submission = Submission::create($data);

    return response()->json($submission, 201);
}

    /**
     * Display the specified resource.
     */
    public function show(Submission $submission)
    {
        return response()->json($submission);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Submission $submission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(UpdateSubmissionRequest $request, Submission $submission)
    // {   
    //     $submission->update($request->validated());
    //     return response()->json($submission);
    // }

    public function update(UpdateSubmissionRequest $request, Submission $submission)
    {
        $data = $request->validated();

        if ($request->hasFile('deliverable')) {
            if ($submission->deliverable && Storage::exists($submission->deliverable)) {
                Storage::delete($submission->deliverable);
            }

            $data['deliverable'] = $request->file('deliverable')->store('submissions', 'public');
        }

        $submission->update($data);

        return response()->json($submission);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Submission $submission)
    {
        $submission->delete();
        return response()->json(['message' => 'Successfully deleted submission'], 200);
    }
}
