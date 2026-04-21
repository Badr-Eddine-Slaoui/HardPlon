<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RunnerRequest extends FormRequest
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
            'type' => 'required|in:http,browser,function',
            'status' => 'required|in:active,deprecated,disabled',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name must not exceed 255 characters.',
            'description.string' => 'Description must be a string.',
            'type.required' => 'Type is required.',
            'type.in' => 'Type must be one of the following: http, browser, function.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be one of the following: active, deprecated, disabled.',
        ];
    }
}
