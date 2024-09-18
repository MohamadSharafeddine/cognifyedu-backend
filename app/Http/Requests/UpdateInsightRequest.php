<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInsightRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_id' => 'sometimes|exists:users,id',
            // 'cognitive_score_id' => 'sometimes|exists:cognitive_scores,id',
            // 'behavioral_score_id' => 'sometimes|exists:behavioral_scores,id',
            // 'profile_comment_id' => 'sometimes|exists:profile_comments,id',
            'summary' => 'sometimes|string',
            'detailed_analysis' => 'sometimes|string',
            'recommendations' => 'sometimes|string',
            'progress_tracking' => 'sometimes|string',
        ];
    }
}
