<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LanguageRequest extends FormRequest
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
            'docker_image' => 'required|string|max:255',
            'compile_command' => 'nullable|string|max:255',
            'run_command' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name field must be a string.',
            'name.max' => 'The name field must not be greater than 255 characters.',
            'docker_image.required' => 'The docker image field is required.',
            'docker_image.string' => 'The docker image field must be a string.',
            'docker_image.max' => 'The docker image field must not be greater than 255 characters.',
            'compile_command.string' => 'The compile command field must be a string.',
            'compile_command.max' => 'The compile command field must not be greater than 255 characters.',
            'run_command.required' => 'The run command field is required.',
            'run_command.string' => 'The run command field must be a string.',
            'run_command.max' => 'The run command field must not be greater than 255 characters.',
        ];
    }
}
