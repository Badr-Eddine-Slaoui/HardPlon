<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RunnerVersionRequest;
use App\Models\RunnerVersion;
use Illuminate\Http\Request;
use Laravel\Octane\Facades\Octane;

class RunnerVersionController extends Controller
{
    private function baseRunnerConfig(): array {
        return [
            "execution" => [
                "mode" => "http",
                "port" => 8000,
                "start_command" => null,
                "working_dir" => "/workspace",
                "healthcheck" => [
                    "type" => "http",
                    "path" => "/",
                    "timeout_seconds" => 10,
                    "interval_ms" => 500
                ]
            ],
            "resources" => [
                "cpu" => 1,
                "memory_mb" => 512,
                "timeout_seconds" => 15,
                "pids_limit" => 100
            ],
            "filesystem" => [
                "read_only" => false,
                "workspace_path" => "/workspace",
                "output_path" => "/output",
                "config_path" => "/config"
            ],
            "network" => [
                "enabled" => false
            ],
            "features" => [
                "php" => false,
                "node" => false,
                "sqlite" => false
            ],
            "fallback" => [
                "on_failure" => "retry_with_universal"
            ]
        ];
    }

    private function buildRunnerConfig(RunnerVersionRequest $request): array {
        $config = $this->baseRunnerConfig();

        $config['execution']['mode'] = $request['mode'];
        $config['execution']['port'] = $request['port'];
        $config['execution']['healthcheck']['path'] = $request['healthcheck_path'];

        $config['resources']['cpu'] = $request['cpu'];
        $config['resources']['memory_mb'] = $request['memory_mb'];
        $config['resources']['timeout_seconds'] = $request['timeout'] ?? 15;

        $config['features']['php'] = $request['features']['php'] ?? false;
        $config['features']['node'] = $request['features']['node'] ?? false;
        $config['features']['sqlite'] = $request['features']['sqlite'] ?? false;

        $config['network']['enabled'] = $request['network_enabled'] ?? false;

        if ($request['mode'] === 'browser') {
            $config['execution']['port'] = 3000;
            $config['resources']['memory_mb'] = max($config['resources']['memory_mb'], 1024);
        }

        if ($request['mode'] === 'function') {
            $config['execution']['port'] = null;
            $config['execution']['healthcheck'] = null;
        }

        return $config;
    }

    public function index()
    {
        try {

            $perPage = request()->get('per_page', 5);

            [$runners, $archived_runners] = Octane::concurrently([
                fn() => RunnerVersion::where('status', 'active')->paginate($perPage),
                fn() => RunnerVersion::where('status', 'deprecated')->paginate($perPage),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully fetched runner versions.',
                'data' => compact('runners', 'archived_runners'),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'An error occurred while fetching runners: ' . $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RunnerVersionRequest $request)
    {
        try {

            $config = $this->buildRunnerConfig($request);

            $runnerVersion = RunnerVersion::create([
                'runner_id' => $request->runner_id,
                'version' => $request->version,
                'docker_image' => $request->docker_image,
                'default_config' => $config,
                'status' => $request->status,
                'is_default' => $request->is_default ?? false,
            ]);

            if (!$runnerVersion) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Failed to create runner version. Please try again.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => compact('runnerVersion'),
                'message' => 'Successfully created runner version.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'An error occurred while creating the runner version: ' . $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            $runnerVersion = RunnerVersion::with(['runner'])->find($id);

            if (!$runnerVersion) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Runner version not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => compact('runnerVersion'),
                'message' => 'Successfully fetched runner version.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'An error occurred while fetching the runner version: ' . $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $runnerVersion = RunnerVersion::find($id);

            if (!$runnerVersion) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Runner version not found.'
                ], 404);
            }

            $is_deleted = $runnerVersion->delete();

            if (!$is_deleted) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Failed to delete runner version. Please try again.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => compact('runnerVersion'),
                'message' => 'Successfully deleted runner version.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'An error occurred while deleting the runner version: ' . $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }
}
