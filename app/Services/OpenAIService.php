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

    public function generateAssessment(array $studentData, string $comment, array $assignmentData, array $submissionData)
    {
        $prompt = $this->buildPrompt($studentData, $comment, $assignmentData, $submissionData);

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

    private function buildPrompt(array $studentData, string $comment, array $assignmentData, array $submissionData)
    {
        return "Student Name: {$studentData['name']}, Age: {$studentData['age']}
                Assignment Title: {$assignmentData['title']}
                Assignment Description: {$assignmentData['description']}
                Assignment Due Date: {$assignmentData['due_date']}
                Submission File: {$submissionData['file']}
                Submission Date: {$submissionData['submission_date']}
                Teacher Comment: {$comment}";
    }
}
