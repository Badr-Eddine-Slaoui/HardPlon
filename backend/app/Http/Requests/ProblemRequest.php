<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProblemRequest extends FormRequest
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
            'brief_skill_level_id' => 'required|exists:brief_skill_levels,id',
            'language_id' => 'required|exists:languages,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'hidden_header' => 'nullable|string',
            'hidden_footer' => 'nullable|string',
            'skeleton_code' => 'nullable|string',
            'order_index' => 'required|integer|min:0'
        ];
    }

    public function messages(): array
    {
        return [
            'brief_id.required' => 'The brief_id field is required.',
            'brief_id.exists' => 'The specified brief_id does not exist.',
            'brief_skill_level_id.required' => 'The brief_skill_level_id field is required.',
            'brief_skill_level_id.exists' => 'The specified brief_skill_level_id does not exist.',
            'language_id.required' => 'The language_id field is required.',
            'language_id.exists' => 'The specified language_id does not exist.',
            'title.required' => 'The title field is required.',
            'title.string' => 'The title field must be a string.',
            'title.max' => 'The title field must not be greater than 255 characters.',
            'description.required' => 'The description field is required.',
            'description.string' => 'The description field must be a string.',
            'hidden_header.string' => 'The hidden_header field must be a string.',
            'hidden_footer.string' => 'The hidden_footer field must be a string.',
            'skeleton_code.string' => 'The skeleton_code field must be a string.',
            'order_index.required' => 'The order_index field is required.',
            'order_index.integer' => 'The order_index field must be an integer.',
            'order_index.min' => 'The order_index field must be at least 0.'
        ];
    }
}
