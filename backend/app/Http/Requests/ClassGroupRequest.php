<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClassGroupRequest extends FormRequest
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
            'image_url' => 'required|url',
            'capacity' => 'required|integer|min:1',
            'description' => 'required|string|min:2',
            "main_teacher_id" => "required|exists:users,id",
            "sub_teacher_id" => "required|exists:users,id",
            "students_ids" => "required|array",
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
            'image_url.required' => 'Image URL is required.',
            'image_url.url' => 'Image URL must be a valid URL.',
            'capacity.required' => 'Capacity is required.',
            'capacity.integer' => 'Capacity must be an integer.',
            'capacity.min' => 'Capacity must be at least 1.',
            'description.required' => 'Description is required.',
            'description.string' => 'Description must be a string.',
            'description.min' => 'Description must be at least 2 characters.',
            'main_teacher_id.required' => 'Main teacher is required.',
            'main_teacher_id.exists' => 'Main teacher does not exist.',
            'sub_teacher_id.required' => 'Sub teacher is required.',
            'sub_teacher_id.exists' => 'Sub teacher does not exist.',
            'students_ids.required' => 'Students are required.',
            'students_ids.array' => 'Students must be an array.',
        ];
    }
}
