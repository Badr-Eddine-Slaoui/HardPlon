<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SprintRequest extends FormRequest
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
            'formation_id' => 'required|exists:formations,id',
            'name' => 'required|string|min:2|max:50',
            'description' => 'required|string|min:2',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'skills' => 'required|array',
        ];
    }

    public function messages(): array
    {
        return [
            'formation_id.required' => 'Formation is required.',
            'formation_id.exists' => 'Formation does not exist.',
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.min' => 'Name must be at least 2 characters.',
            'name.max' => 'Name must not exceed 50 characters.',
            'description.required' => 'Description is required.',
            'description.string' => 'Description must be a string.',
            'description.min' => 'Description must be at least 2 characters.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a valid date.',
            'end_date.required' => 'End date is required.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after' => 'End date must be after start date.',
            'skills_ids.required' => 'Skills are required.',
            'skills_ids.array' => 'Skills must be an array.',
        ];
    }
}
