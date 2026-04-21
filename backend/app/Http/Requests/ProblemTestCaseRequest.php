<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProblemTestCaseRequest extends FormRequest
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
            'problem_id' => 'required|exists:problems,id',
            'input' => 'required|array',
            'input.*' => 'required',
            'expected_output' => 'required|array',
            'expected_output.*' => 'required',
            'is_hidden' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'problem_id.required' => 'The problem_id field is required.',
            'problem_id.exists' => 'The specified problem_id does not exist.',
            'input.required' => 'The input field is required.',
            'input.array' => 'The input must be an array.',
            'input.*.required' => 'Each input value is required.',
            'expected_output.required' => 'The expected_output field is required.',
            'expected_output.array' => 'The expected_output must be an array.',
            'expected_output.*.required' => 'Each expected_output value is required.',
            'is_hidden.boolean' => 'The is_hidden field must be a boolean.'
        ];
    }
}
