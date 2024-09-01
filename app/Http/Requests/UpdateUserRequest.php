<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users',
            'password' => 'sometimes|string|min:8',
            'type' => 'sometimes|in:teacher,student,parent,admin',
            'date_of_birth' => 'sometimes|date',
            'address' => 'sometimes|string|max:255',
            'profile_picture' => 'sometimes|string|max:255',
            'parent_id' => 'sometimes|exists:users,id',
        ];
    }
}
