<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmissionRequest extends FormRequest
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
            'squad_id ' => 'nullable|exists:squads,id',
            'message' => 'required|string|min:2',
            'link' => 'required|string|regex:/^(https?:\/\/)?(www\.)?github\.com\/[A-Za-z0-9_-]+\/[A-Za-z0-9_-]+(\/)?\.git$/|min:5',
        ];
    }

    public function messages(): array
    {
        return [
            'brief_id.required' => 'Brief is required.',
            'brief_id.exists' => 'Brief does not exist.',
            'squad_id.exists' => 'Squad does not exist.',
            'message.required' => 'Message is required.',
            'message.string' => 'Message must be a string.',
            'message.min' => 'Message must be at least 2 characters.',
            'link.required' => 'Link is required.',
            'link.string' => 'Link must be a string.',
            'link.regex' => 'Link must be a valid GitHub repository link.',
            'link.min' => 'Link must be at least 5 characters.',
        ];
    }
}
