<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EvaluationJob;
use Illuminate\Http\Request;

class EvaluationJobController extends Controller


{
    private function buildJobpayload(EvaluationJob $job)
    {
        $payload = [
            'submission_id' => $job->submission_id,
            'brief_id' => $job->submission->brief_id,
            'repository_url' => $job->submission->link,
        ];

        $brief_version_data = $job->submission->brief->brief_versions()->latest()->first();
        $stack_runner_data = $job->submission->brief->stack->stack_runners()->latest()->first();

        $runner = [
            'name' => $stack_runner_data->runner_version->runner->name,
            'version' => $stack_runner_data->runner_version->version,
            'image' => $stack_runner_data->runner_version->docker_image,
        ];

        $config = $stack_runner_data->runner_version->default_config;

        $architecture_rules = $brief_version_data->architecture_rules;

        $test_config = $brief_version_data->test_config;

        return [
            'job_id' => $job->id,
            'payload' => $payload,
            'runner' => $runner,
            'config' => $config,
            'architecture_rules' => $architecture_rules,
            'test_config' => $test_config,
        ];
    }

    public function show(int $id)
    {
        try {
            $job = EvaluationJob::with(['submission.brief.brief_versions', 'submission.brief.stack.stack_runners.runner_version.runner'])->find($id);

            if (!$job) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Evaluation job not found."
                ], 404);
            }

            $job_payload = $this->buildJobpayload($job);

            return response()->json([
                "success" => true,
                "data" => compact('job_payload'),
                "message" => "Successfully fetched evaluation job."
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "An error occurred while fetching the evaluation job: " . $e->getMessage(),
                "code" => $e->getCode(),
            ], 500);
        }
    }


    public function update(Request $request, int $id)
    {
        try {

            $request->validate([
                'status' => 'in:pending,running,completed,failed',
                'result' => 'nullable|array',
            ]);

            $job = EvaluationJob::find($id);

            if (!$job) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Evaluation job not found."
                ], 404);
            }

            $is_updated = $job->update([
                'status' => $request->status ?? $job->status,
                'result' => $request->result ?? $job->result,
            ]);

            if ($is_updated) {
                return response()->json([
                    "success" => true,
                    "data" => compact('job'),
                    "message" => "Successfully updated evaluation job."
                ], 200);
            }

            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "Failed to update evaluation job. Please try again."
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "An error occurred while updating the evaluation job: " . $e->getMessage(),
                "code" => $e->getCode(),
            ], 500);
        }
    }
}
