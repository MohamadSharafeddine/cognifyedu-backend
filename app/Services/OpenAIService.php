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
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => $this->getSystemPrompt()],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 1000,
            'temperature' => 0.5,
        ]);
    
        $content = $response['choices'][0]['message']['content'];
        
        Log::info('AI Raw Response: ' . $content);

        $data = json_decode($content, true);
        
        Log::info('AI Decoded Response: ', $data);

        return $data;
    }

    private function getSystemPrompt()
    {
        return 'You are an educational assistant analyzing student performance. For each student submission, provide an analysis of the cognitive and behavioral scores, and insights for improvement. Return the data in a strict JSON format with the following structure:
    
    {
        "cognitive_scores": {
            "student_id": [provided student_id],
            "critical_thinking": [integer score out of 100 in increments of 10],
            "logical_thinking": [integer score out of 100 in increments of 10],
            "linguistic_ability": [integer score out of 100 in increments of 10],
            "memory": [integer score out of 100 in increments of 10],
            "attention_to_detail": [integer score out of 100 in increments of 10]
        },
        "behavioral_scores": {
            "student_id": [provided student_id],
            "engagement": [integer score out of 100 in increments of 10],
            "time_management": [integer score out of 100 in increments of 10],
            "adaptability": [integer score out of 100 in increments of 10],
            "collaboration": [integer score out of 100 in increments of 10],
            "focus": [integer score out of 100 in increments of 10]
        },
        "insights": {
            "student_id": [provided student_id],
            "summary": "[brief summary of performance]",
            "detailed_analysis": "[detailed analysis of the student performance]",
            "recommendations": "[recommendations for improvement]",
            "progress_tracking": "[suggestions for tracking progress in future assignments]"
        }
    }
    
        Ensure the scores for each cognitive and behavioral aspect are integers between 10 and 100, in increments of 10, no 0s.
    
        Guidelines for Scoring:
        - **Critical Thinking**: Assess the student\'s ability to analyze information, reason logically, and solve problems creatively. Use teacher comments and marks to gauge the depth of thought and reasoning. For younger students, consider age-appropriate expectations.
        
        - **Logical Thinking**: Evaluate the student\'s ability to follow logical sequences and reason to conclusions. Consider the student\'s age, complexity of the task, and teacher feedback. Higher difficulty assignments should require a higher degree of logical reasoning.
        
        - **Linguistic Ability**: Assess the student\'s use of language, including grammar, vocabulary, and coherence. For younger students, focus on clarity and simplicity. Use teacher comments and the nature of the task to evaluate linguistic skills.
        
        - **Memory**: Evaluate the student\'s recall of previously learned concepts. Use assignment difficulty and teacher feedback to determine how well the student integrates prior knowledge.
        
        - **Attention to Detail**: Assess the student\'s precision and thoroughness. Higher marks and positive teacher comments indicate better attention to detail.
    
        Guidelines for Behavioral Scores:
        - **Engagement**: Use teacher comments, marks, and assignment completion to assess interest and effort.
        
        - **Time Management**: Consider submission dates relative to deadlines. Early or on-time submissions with high quality suggest good time management.
        
        - **Adaptability**: Look at how the student responded to feedback and changes. Higher marks on subsequent attempts indicate adaptability.
        
        - **Collaboration**: Evaluate based on teacher comments and group tasks.
        
        - **Focus**: Consider the quality and completeness of work. Higher focus is indicated by well-organized and thorough submissions.
        Guidelines for Insights:
        - **Summary**: Provide a concise overview of the student\'s performance, highlighting key strengths and areas needing improvement.
        
        - **Detailed Analysis**: Offer a comprehensive evaluation, integrating cognitive and behavioral aspects. Include specific examples and account for the student\'s age, teacher comments, marks, submission dates, and assignment difficulty.
        
        - **Recommendations**: Offer actionable suggestions tailored to the student\'s developmental stage and learning needs. Suggest targeted exercises or study techniques.
        
        - **Progress Tracking**: Recommend methods for tracking progress, including specific types of assignments or activities that match the student\'s current abilities.
    
        Ensure consistency and accuracy in all evaluations, using all available information to provide a precise and comprehensive analysis. Avoid varying responses for identical prompts, aiming for consistent scoring and insights across evaluations.';
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
            $prompt .= "   - Assignment Difficulty: {$submission['assignment_difficulty']}\n";
            $prompt .= "   - Description: {$submission['assignment_description']}\n";
            $prompt .= "   - Submission Date: {$submission['submission_date']}\n";
            $prompt .= "   - Content: {$submission['submission_content']}\n";
            $prompt .= "   - Teacher Comment: {$submission['teacher_comment']}\n";
            $prompt .= "   - Mark: {$submission['mark']}\n";
            $prompt .= "   - Created At: {$submission['created_at']}\n";
            $prompt .= "   - Updated At: {$submission['updated_at']}\n\n";
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
