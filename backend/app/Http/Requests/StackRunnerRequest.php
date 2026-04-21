<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StackRunnerRequest extends FormRequest
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
            'stack_id' => 'required|exists:stacks,id',
            'runner_version_id' => 'required|exists:runner_versions,id',
            'priority' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'stack_id.required' => 'Stack ID is required.',
            'stack_id.exists' => 'Stack ID must exist in the stacks table.',
            'runner_version_id.required' => 'Runner Version ID is required.',
            'runner_version_id.exists' => 'Runner Version ID must exist in the runner_versions table.',
            'priority.required' => 'Priority is required.',
            'priority.integer' => 'Priority must be an integer.',
            'priority.min' => 'Priority must be at least 1.',
        ];
    }
}
