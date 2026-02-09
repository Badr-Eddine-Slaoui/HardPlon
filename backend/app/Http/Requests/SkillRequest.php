<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SkillRequest extends FormRequest
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
            'code' => 'required|string|min:2|max:50',
            'title' => 'required|string|min:2|max:50',
            'description' => 'required|string|min:2',
            'skill_levels' => 'required|array|min:1',
            'skill_levels.*.level_id' => 'required|exists:levels,id',
            'skill_levels.*.criteria' => 'required|string'
        ];
    }

    public function messages(): array
    {
        return [
            'formation_id.required' => 'Formation is required.',
            'formation_id.exists' => 'Formation does not exist.',
            'code.required' => 'Code is required.',
            'code.string' => 'Code must be a string.',
            'code.min' => 'Code must be at least 2 characters.',
            'code.max' => 'Code must not exceed 50 characters.',
            'title.required' => 'Title is required.',
            'title.string' => 'Title must be a string.',
            'title.min' => 'Title must be at least 2 characters.',
            'title.max' => 'Title must not exceed 50 characters.',
            'description.required' => 'Description is required.',
            'description.string' => 'Description must be a string.',
            'description.min' => 'Description must be at least 2 characters.',
            'skill_levels.required' => 'Skill levels are required.',
            'skill_levels.array' => 'Skill levels must be an array.',
            'skill_levels.*.level_id.required' => 'Level is required.',
            'skill_levels.*.level_id.exists' => 'Level does not exist.',
            'skill_levels.*.criteria.required' => 'Criteria is required.',
            'skill_levels.*.criteria.string' => 'Criteria must be a string.',
        ];
    }
}
