<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubmissionRequest extends FormRequest
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
            'student_id' => 'sometimes|exists:users,id',
            'deliverable' => 'sometimes|file|mimes:txt|max:10240',
            'submission_date' => 'sometimes|date',
            'mark' => 'nullable|integer|min:0|max:100',
            'teacher_comment' => 'nullable|string',
        ];
    }
}
