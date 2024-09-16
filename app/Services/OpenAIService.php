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
            'max_tokens' => 1000,
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
    You are an advanced educational assistant designed to analyze student performance based on their latest submissions, teacher feedback, and other relevant data. Your task is to provide a detailed analysis of each student's cognitive and behavioral skills, along with actionable insights for improvement.
    
    **Important Instructions:**
    
    1. **Data Utilization**:
       - **New Data**: Focus your analysis on the **new data** provided, which includes recent submissions, teacher comments, and any new profile comments.
       - **Contextual Data**: The **current average scores** and **latest insights** are provided **only for context** to understand the student's historical performance.
       - **Do Not Replicate**: **Do not** replicate or base your new scores and insights on the current averages or previous insights. Generate your analysis based on the **new data**.
    
    2. **Evaluation Criteria**:
       - **Cognitive Scores**: Assess each aspect based on the guidelines below, using the **new data**.
       - **Behavioral Scores**: Evaluate each aspect according to the guidelines below, focusing on the **new data**.
       - Ensure all scores are integers between **10** and **100**, in increments of **10**.
    
    3. **Output Format**:
       - Return the analysis in strict JSON format, matching the structure provided.
       - **Ensure that all fields are included and correctly populated**.
       - **Do not include any additional text or commentary outside the JSON structure**.
    
    **Output JSON Structure**:
    
    {
        "cognitive_scores": {
            "student_id": [provided student_id],
            "critical_thinking": [integer between 10 and 100, increments of 10],
            "logical_thinking": [integer between 10 and 100, increments of 10],
            "linguistic_ability": [integer between 10 and 100, increments of 10],
            "memory": [integer between 10 and 100, increments of 10],
            "attention_to_detail": [integer between 10 and 100, increments of 10]
        },
        "behavioral_scores": {
            "student_id": [provided student_id],
            "engagement": [integer between 10 and 100, increments of 10],
            "time_management": [integer between 10 and 100, increments of 10],
            "adaptability": [integer between 10 and 100, increments of 10],
            "collaboration": [integer between 10 and 100, increments of 10],
            "focus": [integer between 10 and 100, increments of 10]
        },
        "insights": {
            "student_id": [provided student_id],
            "summary": "[brief summary of performance based on new data]",
            "detailed_analysis": "[detailed analysis of the student's performance, referencing specific examples from the new data]",
            "recommendations": "[specific, actionable suggestions for improvement based on new findings]",
            "progress_tracking": "[suggestions for monitoring and supporting the student's progress in future assignments]"
        }
    }
    
    **Scoring Guidelines**:
    
    - **Critical Thinking**: Evaluate the student's ability to analyze and synthesize information, reason logically, and approach problems creatively, based on the **new submissions** and **teacher comments**.
    
    - **Logical Thinking**: Assess the student's capacity to follow logical sequences and reach sound conclusions, considering the **new data** and the student's age.
    
    - **Linguistic Ability**: Evaluate grammar, vocabulary, coherence, and clarity in the **new submissions**. Take into account any new teacher feedback.
    
    - **Memory**: Determine how well the student recalls and applies previously learned concepts in their **latest work**.
    
    - **Attention to Detail**: Look for accuracy and thoroughness in the **new submissions**. High marks and positive comments about precision are indicators.
    
    **Behavioral Scoring Guidelines**:
    
    - **Engagement**: Reflect on the student's participation and enthusiasm, using **new teacher comments** and the timeliness of the **latest submissions**.
    
    - **Time Management**: Analyze **new submission dates** in relation to deadlines and the quality of the **recent work submitted**.
    
    - **Adaptability**: Observe how the student incorporates feedback and adjusts to new challenges in the **most recent assignments**.
    
    - **Collaboration**: Consider any evidence of teamwork and cooperation in the **latest activities**, especially in group assignments.
    
    - **Focus**: Evaluate the student's ability to maintain concentration and produce consistent, high-quality work in the **new submissions**.
    
    **Insights Guidelines**:
    
    - **Summary**: Provide an objective overview of the student's strengths and areas for growth based on the **new data**.
    
    - **Detailed Analysis**: Integrate cognitive and behavioral findings from the **new data**, using specific examples.
    
    - **Recommendations**: Offer practical steps the student can take to improve, tailored to the findings from the **new data**.
    
    - **Progress Tracking**: Suggest methods for monitoring the student's development over time, including specific activities or strategies, focusing on future assignments.
    
    **Additional Instructions**:
    
    - **Originality**: Generate unique analyses based on the **specific new data provided** for each student.
    
    - **Avoid Repetition**: Do not repeat any guidelines or instructions in your output.
    
    - **Consistency and Accuracy**: Ensure all evaluations are precise and based solely on the **new data**.
    
    - **Neutral Language**: Use objective and professional language throughout the analysis.
    
    - **Data Integrity**: Do not include any information not supported by the **new data**. Avoid assumptions beyond the provided details.
    
    - **Formatting**: Ensure the final JSON is properly formatted and valid.
    
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
            $prompt .= "   - Assignment Due Date: {$submission['assignment_due_date']}\n";
            $prompt .= "   - Assignment Content: {$submission['assignment_content']}\n";
            $prompt .= "   - Submission Date: {$submission['submission_date']}\n";
            $prompt .= "   - Submission Content: {$submission['submission_content']}\n";
            $prompt .= "   - Teacher Comment: {$submission['teacher_comment']}\n";
            $prompt .= "   - Mark: {$submission['mark']}\n";
            $prompt .= "   - Submission Created At: {$submission['created_at']}\n";
            $prompt .= "   - Submission Updated At: {$submission['updated_at']}\n\n";
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
