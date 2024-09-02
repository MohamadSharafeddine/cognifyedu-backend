<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBehavioralScoreRequest extends FormRequest
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
            'assignment_id' => 'sometimes|exists:assignments,id',
            'submission_id' => 'sometimes|exists:submissions,id',
            'engagement' => 'sometimes|integer|min:0|max:100',
            'time_management' => 'sometimes|integer|min:0|max:100',
            'adaptability' => 'sometimes|integer|min:0|max:100',
            'collaboration' => 'sometimes|integer|min:0|max:100',
            'focus' => 'sometimes|integer|min:0|max:100',
        ];
    }
}
