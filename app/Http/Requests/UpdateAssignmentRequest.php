<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id' => 'sometimes|exists:courses,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'attachment' => 'sometimes|file|mimes:txt,pdf,doc,docx,jpeg,png,jpg,gif|max:10240',
            'due_date' => 'sometimes|date',
        ];
    }
}
