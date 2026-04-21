<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProblemSubmissionRequest extends FormRequest
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
            'submission_id' => 'required|exists:submissions,id',
            'submissions' => 'required|array',
            'submissions.*.problem_id' => 'required|exists:problems,id',
            'submissions.*.code' => 'required|string'
        ];
    }

    public function messages(): array
    {
        return [
            'submission_id.required' => 'The submission_id field is required.',
            'submission_id.exists' => 'The specified submission_id does not exist.',
            'submissions.required' => 'The submissions field is required.',
            'submissions.array' => 'The submissions field must be an array.',
            'submissions.*.problem_id.required' => 'The problem_id field is required.',
            'submissions.*.problem_id.exists' => 'The specified problem_id does not exist.',
            'submissions.*.code.required' => 'The code field is required.',
            'submissions.*.code.string' => 'The code field must be a valid string.'
        ];
    }
}
