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
            'assignment_id' => 'required|exists:assignments,id',
            'submission_id' => 'required|exists:submissions,id',
            'critical_thinking' => 'required|integer|min:0|max:100',
            'logical_thinking' => 'required|integer|min:0|max:100',
            'linguistic_ability' => 'required|integer|min:0|max:100',
            'memory' => 'required|integer|min:0|max:100',
            'attention_to_detail' => 'required|integer|min:0|max:100',
        ];
    }
}
