<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCognitiveScoreRequest extends FormRequest
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
            // 'assignment_id' => 'sometimes|exists:assignments,id',
            // 'submission_id' => 'sometimes|exists:submissions,id',
            'critical_thinking' => 'sometimes|integer|min:0|max:100',
            'logical_thinking' => 'sometimes|integer|min:0|max:100',
            'linguistic_ability' => 'sometimes|integer|min:0|max:100',
            'memory' => 'sometimes|integer|min:0|max:100',
            'attention_to_detail' => 'sometimes|integer|min:0|max:100',
        ];
    }
}
