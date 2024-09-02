<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInsightRequest extends FormRequest
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
            'student_id' => 'required|exists:users,id',
            'cognitive_score_id' => 'nullable|exists:cognitive_scores,id',
            'behavioral_score_id' => 'nullable|exists:behavioral_scores,id',
            'profile_comment_id' => 'nullable|exists:profile_comments,id',
            'summary' => 'nullable|string',
            'detailed_analysis' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'progress_tracking' => 'nullable|string',
        ];
    }
}
