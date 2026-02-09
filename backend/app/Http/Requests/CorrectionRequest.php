<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CorrectionRequest extends FormRequest
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
            'brief_id' => 'required|exists:briefs,id',
            'student_id' => 'required|exists:users,id',
            'message' => 'required|string|min:2',
            'details' => 'required|array',
            'details.*.brief_skill_level_id' => 'required|exists:brief_skill_levels,id',
            'details.*.grade' => 'required|string|in:POOR,AVERAGE,EXCELLENT',
        ];
    }

    public function messages(): array
    {
        return [
            'brief_id.required' => 'Brief is required.',
            'brief_id.exists' => 'Brief does not exist.',
            'student_id.required' => 'Student is required.',
            'student_id.exists' => 'Student does not exist.',
            'message.required' => 'Message is required.',
            'message.string' => 'Message must be a string.',
            'message.min' => 'Message must be at least 2 characters.',
            'details.required' => 'Details are required.',
            'details.array' => 'Details must be an array.',
            'details.*.brief_skill_level_id.required' => 'Brief skill level is required.',
            'details.*.brief_skill_level_id.exists' => 'Brief skill level does not exist.',
            'details.*.grade.required' => 'Grade is required.',
            'details.*.grade.string' => 'Grade must be a string.',
            'details.*.grade.in' => 'Grade must be POOR, AVERAGE, or EXCELLENT.',
        ];
    }
}
