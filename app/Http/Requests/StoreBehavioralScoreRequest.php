<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBehavioralScoreRequest extends FormRequest
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
            'assignment_id' => 'requires|exists:assignments,id',
            'submission_id' => 'requires|exists:submissions,id',
            'engagement' => 'nullable|integer|min:0|max:100',
            'time_management' => 'nullable|integer|min:0|max:100',
            'adaptability' => 'nullable|integer|min:0|max:100',
            'collaboration' => 'nullable|integer|min:0|max:100',
            'focus' => 'nullable|integer|min:0|max:100',
        ];
    }
}
