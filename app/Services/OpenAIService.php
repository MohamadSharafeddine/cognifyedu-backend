<?php

namespace App\Services;

use OpenAI;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    public function analyzeText($prompt)
    {
        $openai = OpenAI::client(env('OPENAI_API_KEY'));

        $response = $openai->chat()->create([
            'model' => 'gpt-4-turbo',
            'messages' => [
                ['role' => 'system', 'content' => $this->getSystemPrompt()],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 2000,
            'temperature' => 0.7,
        ]);

        $content = $response['choices'][0]['message']['content'];

        Log::info('AI Raw Response: ' . $content);

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON decode error: ' . json_last_error_msg());
            return [];
        }

        Log::info('AI Decoded Response: ', $data);

        return $data;
    }

    private function getSystemPrompt()
    {
        return <<<EOD
        You are an advanced educational assistant and psychologist designed to analyze student performance based on their latest submissions, teacher feedback, and other relevant data. 
        Your task is to provide a detailed, psychology-based analysis of each student's cognitive and behavioral skills, along with actionable insights for improvement. 
    
        Important Instructions:
    
        - Data Utilization:
            - New Data: Focus your analysis on the new data provided, which includes recent submissions, teacher comments, and any new profile comments.
            - Contextual Data: The current average scores and latest insights are provided only for context to understand the student's historical performance. 
            - Generate your analysis based on the new data.
    
        - Scoring:
            - Assign scores as integers between 10 and 100, in increments of 10.
            - If there is absolutely no data available to determine a score for a specific criterion, use the provided average score.

        - Language:
            - Use psychological based terms and methods.
            - Be clear, cohesive, complete, concrete, and descriptive.
    
        - Output Format:
            - Return the analysis in strict JSON format, matching the structure provided.
            - Ensure that all fields are included and correctly populated.
            - Do not include any additional text or commentary outside the JSON structure.
            - Do not add backticks in the response.
    
        Output JSON Structure:
        {
            "cognitive_scores": {
                "student_id": [provided student_id],
                "critical_thinking": [integer],
                "logical_thinking": [integer],
                "linguistic_ability": [integer],
                "memory": [integer],
                "attention_to_detail": [integer]
            },
            "behavioral_scores": {
                "student_id": [provided student_id],
                "engagement": [integer],
                "time_management": [integer],
                "adaptability": [integer],
                "collaboration": [integer],
                "focus": [integer]
            },
            "insights": {
                "student_id": [provided student_id],
                "summary": "[brief summary of the detailed analysis, recommendations, and progress tracking]",
                "detailed_analysis": "[Include strengths, areas for improvement, and supporting data for each of the cognitive and behavioral analysis. 
                Identify fields or areas where the student could excel based on their strengths and performance.]",
                "recommendations": "[Include psychological strategies and techniques in your recommendations, and provide guidance on how the student can leverage their strengths. 
                Offer suggestions on how teachers and parents can support the student's development.]",
                "progress_tracking": "[Provide methods to track progress, focusing on specific cognitive and behavioral improvements. Identify chronological progress based on the given data]"
            }
        }
        EOD;
    }
    

    public function buildPrompt($studentId, $studentName, $studentAge, $submissions, $averageCognitiveScores, $averageBehavioralScores, $profileComment, $insights)
    {
        $prompt = "Student Information:\n";
        $prompt .= "- ID: $studentId\n";
        $prompt .= "- Name: $studentName\n";
        $prompt .= "- Age: " . ($studentAge !== 'N/A' ? $studentAge : "Not provided") . "\n\n";

        $prompt .= "Profile Comment:\n";
        $prompt .= "- Latest Comment: " . (!empty($profileComment) ? $profileComment : "No recent comments") . "\n\n";

        $prompt .= "Current Average Scores:\n";
        $prompt .= "- Cognitive Scores:\n";
        foreach ($averageCognitiveScores as $key => $value) {
            $score = is_null($value) ? "Not evaluated yet" : $value;
            $prompt .= "  - " . ucfirst(str_replace('_', ' ', $key)) . ": $score\n";
        }
        $prompt .= "- Behavioral Scores:\n";
        foreach ($averageBehavioralScores as $key => $value) {
            $score = is_null($value) ? "Not evaluated yet" : $value;
            $prompt .= "  - " . ucfirst(str_replace('_', ' ', $key)) . ": $score\n";
        }
        $prompt .= "\n";

        $prompt .= "Assignments and Corresponding Submissions:\n";
        foreach ($submissions as $index => $submission) {
            $prompt .= ($index + 1) . ". Assignment Title: {$submission['assignment_title']}\n";
            $prompt .= "   - Course: {$submission['course_name']}\n";
            $prompt .= "   - Description: {$submission['assignment_description']}\n";
            $prompt .= "   - Assignment Created At: {$submission['created_at']}\n";
            $prompt .= "   - Assignment Due Date: {$submission['assignment_due_date']}\n";
            $prompt .= "   - Assignment Content: {$submission['assignment_content']}\n";
            $prompt .= "   - Submission Date: {$submission['submission_date']}\n";
            $prompt .= "   - Submission Content: {$submission['submission_content']}\n";
            $prompt .= "   - Teacher Comment: {$submission['teacher_comment']}\n";
            $prompt .= "   - Mark: {$submission['mark']}\n";
        }

        if ($insights) {
            $prompt .= "Latest Insights:\n";
            $prompt .= "- Summary: " . $insights->summary . "\n";
            $prompt .= "- Detailed Analysis: " . $insights->detailed_analysis . "\n";
            $prompt .= "- Recommendations: " . $insights->recommendations . "\n";
            $prompt .= "- Progress Tracking: " . $insights->progress_tracking . "\n\n";
        }

        Log::info('Constructed AI Prompt: ' . $prompt);

        return $prompt;
    }
}
