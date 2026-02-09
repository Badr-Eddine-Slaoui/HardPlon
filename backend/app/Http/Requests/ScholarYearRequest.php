<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScholarYearRequest extends FormRequest
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
            "start_year" => "required",
            "end_year" => "required|after:start_year",
            "capacity" => "required",
        ];
    }

    public function messages(): array
    {
        return [
            "start_year.required" => "Start year is required.",
            "end_year.required" => "End year is required.",
            "end_year.after" => "End year must be after start year.",
            "capacity.required" => "Capacity is required.",
        ];
    }
}
