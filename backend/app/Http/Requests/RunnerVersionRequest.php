<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RunnerVersionRequest extends FormRequest
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
            "runner_id" => "required|exists:runners,id",
            "version" => "required|string|max:255",
            "docker_image" => "required|string|max:255",
            "mode" => "required|in:http,browser,function",
            "port" => "required|integer|min:1|max:65535",
            "healthcheck_path" => "required|string|max:255",
            'cpu' => 'required|numeric|min:0.1',
            'memory_mb' => 'required|integer|min:16',
            'timeout_seconds' => 'required|integer|min:1',
            'php' => 'required|boolean',
            'node' => 'required|boolean',
            'sqlite' => 'required|boolean',
            'network_enabled' => 'required|boolean',
            "status" => "required|in:active,deprecated,disabled",
            'is_default' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'runner_id.required' => 'Runner ID is required.',
            'runner_id.exists' => 'Runner ID must exist in the runners table.',
            'version.required' => 'Version is required.',
            'version.string' => 'Version must be a string.',
            'version.max' => 'Version must not exceed 255 characters.',
            'docker_image.required' => 'Docker image is required.',
            'docker_image.string' => 'Docker image must be a string.',
            'docker_image.max' => 'Docker image must not exceed 255 characters.',
            'mode.required' => 'Execution mode is required.',
            'mode.in' => 'Execution mode must be one of the following: http, browser, function.',
            'port.required' => 'Port is required.',
            'port.integer' => 'Port must be an integer.',
            'port.min' => 'Port must be at least 1.',
            'port.max' => 'Port must not exceed 65535.',
            'healthcheck_path.required' => 'Healthcheck path is required.',
            'healthcheck_path.string' => 'Healthcheck path must be a string.',
            'healthcheck_path.max' => 'Healthcheck path must not exceed 255 characters.',
            'cpu.required' => 'CPU allocation is required.',
            'cpu.numeric' => 'CPU allocation must be a number.',
            'cpu.min' => 'CPU allocation must be at least 0.1.',
            'memory_mb.required' => 'Memory allocation is required.',
            'memory_mb.integer' => 'Memory allocation must be an integer.',
            'memory_mb.min' => 'Memory allocation must be at least 16 MB.',
            'timeout_seconds.required' => 'Timeout is required.',
            'timeout_seconds.integer' => 'Timeout must be an integer.',
            'timeout_seconds.min' => 'Timeout must be at least 1 second.',
            "status.required" => "Status is required.",
            "status.in" => "Status must be one of the following: active, deprecated, disabled.",
        ];
    }
}
