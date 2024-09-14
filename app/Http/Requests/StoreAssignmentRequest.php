<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'attachment' => 'sometimes|file|mimes:txt,pdf,doc,docx,jpeg,png,jpg,gif|max:10240',
            'due_date' => 'required|date',
        ];
    }
}
