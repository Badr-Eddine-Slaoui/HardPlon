<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StackRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'runner_versions' => 'required|array|min:1',
            'runner_versions.*.runner_version_id' => 'required|exists:runner_versions,id',
            'runner_versions.*.priority' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name must not exceed 255 characters.',
            'description.string' => 'Description must be a string.',
            'runner_versions.required' => 'At least one runner version is required.',
            'runner_versions.array' => 'Runner versions must be an array.',
            'runner_versions.*.runner_version_id.required' => 'Runner version ID is required.',
            'runner_versions.*.runner_version_id.exists' => 'The selected runner version is invalid.',
            'runner_versions.*.priority.required' => 'Priority is required.',
            'runner_versions.*.priority.integer' => 'Priority must be an integer.',
        ];
    }
}
