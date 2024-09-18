<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCognitiveScoreRequest extends FormRequest
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
            // 'assignment_id' => 'required|exists:assignments,id',
            // 'submission_id' => 'required|exists:submissions,id',
            'critical_thinking' => 'nullable|integer|min:0|max:100',
            'logical_thinking' => 'nullable|integer|min:0|max:100',
            'linguistic_ability' => 'nullable|integer|min:0|max:100',
            'memory' => 'nullable|integer|min:0|max:100',
            'attention_to_detail' => 'nullable|integer|min:0|max:100',
        ];
    }
}
