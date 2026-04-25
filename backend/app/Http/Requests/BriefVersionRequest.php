<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Validator;

class BriefVersionRequest extends FormRequest
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


    private function testConfigRules(): array{
        return match ($this->input('type')) {
            'http' => [
                'base_path' => 'required|string',
                "tests" => 'required|array',
                "tests.*" => 'array:name,method,url,headers,body,expected',
                "tests.*.name" => 'required|string|max:255',
                "tests.*.method" => 'required|in:GET,POST,PUT,DELETE,PATCH',
                "tests.*.url" => 'required|string|max:255',
                "tests.*.headers" => 'nullable|array',
                "tests.*.headers.*" => 'array:name,value',
                "tests.*.headers.*.name" => 'required|string|max:255',
                "tests.*.headers.*.value" => 'required|string|max:255',
                "tests.*.body" => 'required_if:tests.*.method,POST,PUT,PATCH|nullable|string',
                "tests.*.expected" => 'required|array:status,json_contains',
                "tests.*.expected.status" => 'required|integer|min:100|max:599',
                "tests.*.expected.json_contains" => 'nullable|array',
                "tests.*.expected.json_contains.*" => 'nullable|string|max:255',
            ],
            'browser' => [
                "tests" => 'required|array',
                "tests.*" => 'array:name,steps,expected',
                "tests.*.name" => 'required|string|max:255',
                "tests.*.steps" => 'required|array',
                "tests.*.steps.*" => 'array:action,url,selector',
                "tests.*.steps.*.action" => 'required|in:goto,click',
                "tests.*.steps.*.url" => 'required_if:tests.*.steps.*.action,goto|string|max:255',
                "tests.*.steps.*.selector" => 'required_if:tests.*.steps.*.action,click|string|max:255',
                "tests.*.expected" => 'required|array:title,text',
                "tests.*.expected.title" => 'nullable|string|max:255',
                "tests.*.expected.text" => 'nullable|string|max:255',
            ],
            'function' => [
                "language" => 'required',
                "tests" => 'required|array',
                "tests.*" => 'array:name,input,expected_output',
                "tests.*.name" => 'required|string|max:255',
                "tests.*.input" => 'required|array',
                "tests.*.input.*" => 'required',
                "tests.*.expected_output" => 'required|array',
                "tests.*.expected_output.*" => 'required',
            ],
            default => []
        };
    }

    private function testConfigMessages(): array{
        return match ($this->input('type')) {
            'http' => [
                'base_path.required' => 'Base path is required.',
                'base_path.string' => 'Base path must be a string.',
                "tests.required" => "Tests list is required.",
                "tests.array" => "Tests must be an array.",
                "tests.*.array" => "Each test must be an array, containing name, method, url, and expected, optionally headers and body.",
                "tests.*.name.required" => "Test name is required.",
                "tests.*.name.string" => "Test name must be a string.",
                "tests.*.name.max" => "Test name must not exceed 255 characters.",
                "tests.*.method.required" => "Test method is required.",
                "tests.*.method.in" => "Test method must be one of the following: GET, POST, PUT, DELETE, PATCH.",
                "tests.*.url.required" => "Test URL is required.",
                "tests.*.url.string" => "Test URL must be a string.",
                "tests.*.url.max" => "Test URL must not exceed 255 characters.",
                "tests.*.headers.array" => "Test headers must be an array.",
                "tests.*.headers.*.array" => "Each test header must be an array, containing name and value.",
                "tests.*.headers.*.name.required" => "Test header name is required.",
                "tests.*.headers.*.name.string" => "Test header name must be a string.",
                "tests.*.headers.*.name.max" => "Test header name must not exceed 255 characters.",
                "tests.*.headers.*.value.required" => "Test header value is required.",
                "tests.*.headers.*.value.string" => "Test header value must be a string.",
                "tests.*.headers.*.value.max" => "Test header value must not exceed 255 characters.",
                "tests.*.body.required_if" => "Test body is required.",
                "tests.*.body.array" => "Test body must be an array.",
                "tests.*.expected.required" => "Test expected is required.",
                "tests.*.expected.array" => "Test expected must be an array, containing status and json_contains.",
                "tests.*.expected.status.required" => "Test expected status is required.",
                "tests.*.expected.status.integer" => "Test expected status must be an integer.",
                "tests.*.expected.status.min" => "Test expected status must be at least 100.",
                "tests.*.expected.status.max" => "Test expected status must not exceed 599.",
                "tests.*.expected.json_contains.array" => "Test expected JSON contains must be an array.",
                "tests.*.expected.json_contains.*.string" => "Test expected JSON contains value must be a string.",
                "tests.*.expected.json_contains.*.max" => "Test expected JSON contains value must not exceed 255 characters.",
            ],
            'browser' => [
                "tests.required" => "Tests list is required.",
                "tests.array" => "Tests must be an array.",
                "tests.*.array" => "Each test must be an array, containing name, steps and expected.",
                "tests.*.name.required" => "Test name is required.",
                "tests.*.name.string" => "Test name must be a string.",
                "tests.*.name.max" => "Test name must not exceed 255 characters.",
                "tests.*.steps.required" => "Test steps are required.",
                "tests.*.steps.array" => "Test steps must be an array.",
                "tests.*.steps.*.array" => "Each test step must be an array, containing action and either url or selector.",
                "tests.*.steps.*.action.required" => "Test step action is required.",
                "tests.*.steps.*.action.in" => "Test step action must be one of the following: goto, click.",
                "tests.*.steps.*.url.string" => "Test step URL must be a string.",
                "tests.*.steps.*.url.max" => "Test step URL must not exceed 255 characters.",
                "tests.*.steps.*.selector.string" => "Test step selector must be a string.",
                "tests.*.steps.*.selector.max" => "Test step selector must not exceed 255 characters.",
                "tests.*.expected.required" => "Test expected is required.",
                "tests.*.expected.array" => "Test expected must be an array, containing title or text.",
                "tests.*.expected.title.string" => "Test expected title must be a string.",
                "tests.*.expected.title.max" => "Test expected title must not exceed 255 characters.",
                "tests.*.expected.text.string" => "Test expected text must be a string.",
                "tests.*.expected.text.max" => "Test expected text must not exceed 255 characters.",
            ],
            'function' => [
                "language.required" => "Function test language is required.",
                "tests.required" => "Tests list is required.",
                "tests.array" => "Tests must be an array.",
                "tests.*.array" => "Each test must be an array, containing name, input and expected_output.",
                "tests.*.name.required" => "Test name is required.",
                "tests.*.name.string" => "Test name must be a string.",
                "tests.*.name.max" => "Test name must not exceed 255 characters.",
                "tests.*.input.required" => "Test input is required.",
                "tests.*.input.array" => "Test input must be an array.",
                "tests.*.input.*.required" => "Test input value is required.",
                "tests.*.expected_output.required" => "Test expected output is required.",
                "tests.*.expected_output.array" => "Test expected output must be an array.",
                "tests.*.expected_output.*.required" => "Test expected output value is required."
            ],
            default => []
        };
    }

    public function rules(): array
    {
        $base_rules = [
            'brief_id' => 'required|exists:briefs,id',
            'version' => 'required|string|max:255',
            "required_files" => 'required|array',
            "required_files.*" => 'string|max:255',
            "optional_files" => 'required|array',
            "optional_files.*" => 'string|max:255',
            "forbidden_files" => 'nullable|array',
            "forbidden_files.*" => 'string|max:255',
            "patterns" => 'required|array',
            "patterns.*.type" => 'required|in:file_contains,dir_contains,file_not_contains,dir_not_contains',
            "patterns.*.path" => 'required|string|max:255',
            "patterns.*.value" => 'required|string|max:255',
            "type" => 'required|in:http,browser,function',
        ];

        return array_merge($base_rules, $this->testConfigRules());
    }

    protected function failedValidation(ValidatorContract $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 422));
    }

    protected function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->input('type') !== 'browser') {
                return;
            }

            foreach ((array) $this->input('tests', []) as $index => $test) {
                $steps = (array) data_get($test, 'steps', []);
                $requiresTitle = false;
                $requiresText = false;

                foreach ($steps as $step) {
                    $action = data_get($step, 'action');
                    if ($action === 'goto') {
                        $requiresTitle = true;
                    }
                    if ($action === 'click') {
                        $requiresText = true;
                    }
                }

                $title = data_get($test, 'expected.title');
                $text = data_get($test, 'expected.text');

                if ($requiresTitle && ($title === null || $title === '')) {
                    $validator->errors()->add("tests.$index.expected.title", 'Test expected title is required for goto steps.');
                }

                if ($requiresText && ($text === null || $text === '')) {
                    $validator->errors()->add("tests.$index.expected.text", 'Test expected text is required for click steps.');
                }
            }
        });
    }

    public function messages(): array
    {
        $base_messages = [
            'brief_id.required' => 'Brief ID is required.',
            'brief_id.exists' => 'Brief ID must exist in the briefs table.',
            'version.required' => 'Version is required.',
            'version.string' => 'Version must be a string.',
            'version.max' => 'Version must not exceed 255 characters.',
            "required_files.required" => "Required files list is required.",
            "required_files.array" => "Required files must be an array.",
            "required_files.*.string" => "Each required file must be a string.",
            "required_files.*.max" => "Each required file must not exceed 255 characters.",
            "optional_files.required" => "Optional files list is required.",
            "optional_files.array" => "Optional files must be an array.",
            "optional_files.*.string" => "Each optional file must be a string.",
            "optional_files.*.max" => "Each optional file must not exceed 255 characters.",
            "forbidden_files.array" => "Forbidden files must be an array.",
            "forbidden_files.*.string" => "Each forbidden file must be a string.",
            "forbidden_files.*.max" => "Each forbidden file must not exceed 255 characters.",
            "patterns.required" => "Patterns list is required.",
            "patterns.array" => "Patterns must be an array.",
            "patterns.*.type.required" => "Pattern type is required.",
            "patterns.*.type.in" => "Pattern type must be one of the following: file_contains, dir_contains, file_not_contains, dir_not_contains.",
            "patterns.*.path.required" => "Pattern path is required.",
            "patterns.*.path.string" => "Pattern path must be a string.",
            "patterns.*.path.max" => "Pattern path must not exceed 255 characters.",
            "patterns.*.value.required" => "Pattern value is required.",
            "patterns.*.value.string" => "Pattern value must be a string.",
            "patterns.*.value.max" => "Pattern value must not exceed 255 characters.",
            "type.required" => "Test config type is required.",
            "type.in" => "Test config type must be one of the following: http, browser, function."
        ];

        return array_merge($base_messages, $this->testConfigMessages());
    }
}
