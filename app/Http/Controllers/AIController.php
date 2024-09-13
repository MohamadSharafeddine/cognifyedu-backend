<?php

namespace App\Http\Controllers;

use App\Services\OpenAIService;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;

class AIController extends Controller
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    public function generateAssignmentAssessment(Request $request, $submissionId)
    {
        $submission = Submission::with('assignment', 'student')->findOrFail($submissionId);

        // Build necessary data
        $studentData = [
            'name' => $submission->student->name,
            'age' => $this->calculateAge($submission->student->date_of_birth),
        ];

        $assignmentData = [
            'title' => $submission->assignment->title,
            'description' => $submission->assignment->description,
            'due_date' => $submission->assignment->due_date,
        ];

        $submissionData = [
            'file' => $submission->deliverable,
            'submission_date' => $submission->submission_date,
        ];

        $comment = $request->input('comment', 'No comment provided.');

        // Call OpenAI Service
        $assessment = $this->openAIService->generateAssessment($studentData, $comment, $assignmentData, $submissionData);

        return response()->json(['assessment' => $assessment]);
    }

    private function calculateAge($dateOfBirth)
    {
        return \Carbon\Carbon::parse($dateOfBirth)->age;
    }
}
