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
use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreCognitiveScoreRequest;
use App\Http\Requests\StoreBehavioralScoreRequest;
use App\Http\Requests\StoreInsightRequest;

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

            $latestProfileComment = ProfileComment::where('student_id', $studentId)->latest()->value('comment');

            $latestInsights = Insight::where('student_id', $studentId)->latest()->first();

            $prompt = $this->openAIService->buildPrompt(
                $studentId,
                $student->name,
                $student->age ?? 'N/A',
                $submissions,
                $averageCognitiveScores,
                $averageBehavioralScores,
                $latestProfileComment ?? 'No recent comments',
                $latestInsights
            );

            $response = $this->openAIService->analyzeText($prompt);

            $this->storeAIResults($response);

            return response()->json([
                'message' => 'Analysis completed successfully.',
                'ai_response' => $response
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function collectDataForAI($studentId)
    {
        try {
            $lastComment = ProfileComment::where('student_id', $studentId)->latest('created_at')->first();
    
            if (!$lastComment) {
                $newSubmissions = Submission::where('student_id', $studentId)->with('assignment.course')->get();
            } else {
                $newSubmissions = Submission::where('student_id', $studentId)
                    ->where('updated_at', '>', $lastComment->created_at)
                    ->with('assignment.course')
                    ->get();
            }
    
            $dataForAI = [];
            foreach ($newSubmissions as $submission) {
                $assignment = $submission->assignment;
                $course = $assignment->course;
                $extractedText = $this->extractTextFromFile(storage_path('app/public/' . $submission->deliverable));
                $dataForAI[] = [
                    'student_id' => $studentId,
                    'assignment_id' => $assignment->id,
                    'assignment_title' => $assignment->title,
                    'assignment_description' => $assignment->description,
                    'course_name' => $course->name,
                    'assignment_difficulty' => $assignment->difficulty,
                    'submission_date' => $submission->submission_date,
                    'submission_content' => $extractedText,
                    'teacher_comment' => $submission->teacher_comment,
                    'mark' => $submission->mark,
                    'created_at' => $submission->created_at,
                    'updated_at' => $submission->updated_at,
                ];
            }
    
            return $dataForAI;
        } catch (Exception $e) {
            return [];
        }
    }

    private function extractTextFromFile($filePath)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

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
                throw new Exception('Unsupported file type.');
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
