<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'assignment_id' => 'sometimes|exists:assignments,id',
            'deliverable' => 'required|file|mimes:txt,pdf,doc,docx,jpeg,png,jpg,gif|max:10240',
            'mark' => 'nullable|integer|min:0|max:100',
            'teacher_comment' => 'nullable|string',
        ];
    }
}
