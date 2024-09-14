<?php

namespace App\Services;

use GuzzleHttp\Client;
use Exception;

class OpenAIService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('OPENAI_API_KEY');
    }

    public function generateAssessment(array $studentData, string $comment, array $assignmentsData)
    {
        // Pass an array of assignments instead of a single assignment
        $prompt = $this->buildPrompt($studentData, $comment, $assignmentsData);

        try {
            $response = $this->client->post('https://api.openai.com/v1/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4',
                    'prompt' => $prompt,
                    'max_tokens' => 1000,
                    'temperature' => 0.7,
                ],
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);
            return $responseData['choices'][0]['text'] ?? 'No assessment generated.';
        } catch (Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    private function buildPrompt(array $studentData, string $comment, array $assignmentsData)
    {
        $assignmentsInfo = '';

        // Iterate over assignments to include their details in the prompt
        foreach ($assignmentsData as $assignment) {
            $assignmentsInfo .= "
                Assignment Title: {$assignment['title']}
                Assignment Description: {$assignment['description']}
                Assignment Due Date: {$assignment['due_date']}
                Submission File: {$assignment['file']}
                Submission Date: {$assignment['submission_date']}
            ";
        }

        return "Student Name: {$studentData['name']}, Age: {$studentData['age']}
                Recent Assignments and Submissions:
                {$assignmentsInfo}
                Teacher Comment: {$comment}";
    }
}
