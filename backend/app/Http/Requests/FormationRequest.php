<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormationRequest extends FormRequest
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
            'grade_level_id' => 'required|exists:grade_levels,id',
            'title' => 'required|string|min:2|max:50',
            'description' => 'required|string|min:2',
            'duration' => 'required|integer',
            'capacity' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'grade_level_id.required' => 'Grade level is required.',
            'grade_level_id.exists' => 'Grade level does not exist.',
            'title.required' => 'Title is required.',
            'title.string' => 'Title must be a string.',
            'title.min' => 'Title must be at least 2 characters.',
            'title.max' => 'Title must not exceed 50 characters.',
            'description.required' => 'Description is required.',
            'description.string' => 'Description must be a string.',
            'description.min' => 'Description must be at least 2 characters.',
            'duration.required' => 'Duration is required.',
            'duration.integer' => 'Duration must be an integer.',
            'capacity.required' => 'Capacity is required.',
            'capacity.integer' => 'Capacity must be an integer.',
        ];
    }
}
