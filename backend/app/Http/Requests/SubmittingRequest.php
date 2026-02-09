<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmittingRequest extends FormRequest
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
            'links' => 'required|array|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'brief_id.required' => 'Brief is required.',
            'brief_id.exists' => 'Brief does not exist.',
            'squad_id.required' => 'Squad is required.',
            'squad_id.exists' => 'Squad does not exist.',
            'message.required' => 'Message is required.',
            'message.string' => 'Message must be a string.',
            'message.min' => 'Message must be at least 2 characters.',
            'links.required' => 'Links are required.',
            'links.array' => 'Links must be an array.',
        ];
    }
}
