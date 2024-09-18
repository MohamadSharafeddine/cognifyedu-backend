<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'assignment_id' => 'required|exists:assignments,id',
            'deliverable' => 'required|file|mimes:txt,pdf,doc,docx,jpeg,png,jpg,gif|max:10240',
        ];
    }
}
