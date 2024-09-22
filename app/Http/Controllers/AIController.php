<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenAIService;
use App\Models\Submission;
use App\Models\ProfileComment;
use App\Models\CognitiveScore;
use App\Models\BehavioralScore;
use App\Models\User;
use App\Models\Insight;
use Exception;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;

class AIController extends Controller
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    public function analyzeStudentPerformance($studentId)
    {
        try {
            Log::info('Analyzing student performance for student_id: ' . $studentId);

            $student = User::findOrFail($studentId);
            $submissions = $this->collectDataForAI($studentId);

            $averageCognitiveScores = CognitiveScore::where('student_id', $studentId)
                ->selectRaw('ROUND(AVG(critical_thinking)) as critical_thinking, 
                             ROUND(AVG(logical_thinking)) as logical_thinking, 
                             ROUND(AVG(linguistic_ability)) as linguistic_ability, 
                             ROUND(AVG(memory)) as memory, 
                             ROUND(AVG(attention_to_detail)) as attention_to_detail')
                ->first()
                ->toArray();

            $averageBehavioralScores = BehavioralScore::where('student_id', $studentId)
                ->selectRaw('ROUND(AVG(engagement)) as engagement, 
                             ROUND(AVG(time_management)) as time_management, 
                             ROUND(AVG(adaptability)) as adaptability, 
                             ROUND(AVG(collaboration)) as collaboration, 
                             ROUND(AVG(focus)) as focus')
                ->first()
                ->toArray();

            $latestProfileComment = ProfileComment::where('student_id', $studentId)->latest()->value('comment') ?? 'No recent comments';

            $latestInsights = Insight::where('student_id', $studentId)->latest()->first();

            $prompt = $this->openAIService->buildPrompt(
                $studentId,
                $student->name,
                $student->age ?? 'N/A',
                $submissions,
                $averageCognitiveScores,
                $averageBehavioralScores,
                $latestProfileComment,
                $latestInsights
            );

            $response = $this->openAIService->analyzeText($prompt);

            Log::info('Received AI response: ', $response);

            $this->storeAIResults($response);

            return [
                'message' => 'Analysis completed successfully.',
                'ai_response' => $response
            ];
        } catch (Exception $e) {
            Log::error('Error in analyzing student performance: ' . $e->getMessage());
            throw $e;
        }
    }

    public function collectDataForAI($studentId)
    {
        try {
            $newSubmissions = Submission::where('student_id', $studentId)
                ->with('assignment.course')
                ->orderBy('updated_at', 'desc')
                ->take(5)
                ->get();
    
            Log::info('Last 5 Submissions found: ', $newSubmissions->toArray());
    
            $dataForAI = [];
            foreach ($newSubmissions as $submission) {
                try {
                    $assignment = $submission->assignment;
                    $course = $assignment->course;
    
                    $extractedSubmissionText = '';
                    if ($submission->deliverable) {
                        $extractedSubmissionText = $this->extractTextFromFile(storage_path('app/public/' . $submission->deliverable));
                    } else {
                        Log::warning('Submission ID ' . $submission->id . ' has no deliverable.');
                    }
    
                    $extractedAssignmentText = '';
                    if ($assignment->attachment) {
                        $extractedAssignmentText = $this->extractTextFromFile(storage_path('app/public/' . $assignment->attachment));
                    } else {
                        Log::warning('Assignment ID ' . $assignment->id . ' has no attachment.');
                    }
    
                    $dataForAI[] = [
                        'student_id'            => $studentId,
                        'assignment_id'         => $assignment->id,
                        'assignment_title'      => $assignment->title,
                        'assignment_description'=> $assignment->description,
                        'assignment_content'    => $extractedAssignmentText,
                        'created_at'            => $assignment->created_at,
                        'assignment_due_date'   => $assignment->due_date,
                        'course_name'           => $course->name,
                        'submission_id'         => $submission->id,
                        'submission_date'       => $submission->submission_date,
                        'submission_content'    => $extractedSubmissionText,
                        'teacher_comment'       => $submission->teacher_comment,
                        'mark'                  => $submission->mark,
                    ];
                } catch (Exception $e) {
                    Log::error('Error processing submission ID ' . $submission->id . ': ' . $e->getMessage());
                }
            }
    
            Log::info('Data for AI: ', $dataForAI);
    
            return $dataForAI;
        } catch (Exception $e) {
            Log::error('Error in collecting data for AI: ' . $e->getMessage());
            return [];
        }
    }   


    private function extractTextFromFile($filePath)
    {
        if (!file_exists($filePath)) {
            Log::warning('File not found: ' . $filePath);
            return '';
        }
    
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        Log::info('Processing file with extension: ' . $extension);
    
        switch (strtolower($extension)) {
            case 'txt':
                return file_get_contents($filePath);
            case 'pdf':
                $parser = new PdfParser();
                $pdf = $parser->parseFile($filePath);
                return $pdf->getText();
            case 'docx':
                return $this->extractTextFromDocx($filePath);
            default:
                Log::warning('Unsupported file type: ' . $extension);
                return '';
        }
    }
    

    private function extractTextFromDocx($filePath)
    {
        $phpWord = WordIOFactory::load($filePath, 'Word2007');
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                    foreach ($element->getElements() as $textElement) {
                        if ($textElement instanceof \PhpOffice\PhpWord\Element\Text) {
                            $text .= $textElement->getText() . "\n";
                        }
                    }
                }
            }
        }

        return $text;
    }

    private function storeAIResults($response)
    {
        try {
            if (isset($response['cognitive_scores'])) {
                CognitiveScore::create($response['cognitive_scores']);
            }

            if (isset($response['behavioral_scores'])) {
                BehavioralScore::create($response['behavioral_scores']);
            }

            if (isset($response['insights'])) {
                Insight::create($response['insights']);
            }
        } catch (Exception $e) {
            Log::error('Error storing AI results: ' . $e->getMessage());
        }
    }
}
