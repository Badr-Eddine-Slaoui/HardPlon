<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradeLevelRequest extends FormRequest
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
            'name' => 'required|string|min:2|max:50',
            'capacity' => 'required|integer',
            'description' => 'required|string|min:2',
            'scholar_year_id' => 'required|exists:scholar_years,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'name.string'=> 'Name must be a string.',
            'name.min' => 'Name must be at least 2 characters.',
            'name.max' => 'Name must not exceed 50 characters.',
            'capacity.required' => 'Capacity is required.',
            'capacity.integer' => 'Capacity must be an integer.',
            'description.required' => 'Description is required.',
            'description.string' => 'Description must be a string.',
            'description.min' => 'Description must be at least 2 characters.',
            'scholar_year_id.required' => 'Scholar year is required.',
            'scholar_year_id.exists' => 'Scholar year does not exist.',
        ];
    }
}
