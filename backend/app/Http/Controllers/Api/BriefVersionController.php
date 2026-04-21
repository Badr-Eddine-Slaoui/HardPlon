<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BriefVersionRequest;
use App\Models\BriefVersion;
use Laravel\Octane\Facades\Octane;

class BriefVersionController extends Controller
{

    private function basedArchitectureRules(): array
    {
        return [
            "required" => [],
            "optional" => [],
            "forbidden" => [
                ".env",
                "node_modules",
                "vendor"
            ],
            "structure" => [],
            "patterns" => []
        ];
    }

    private function buildArchitecureRules(BriefVersionRequest $request): array
    {
            $architecture_rules = $this->basedArchitectureRules();

            $architecture_rules['required'] = $request->required_files;
            $architecture_rules['optional'] = $request->optional_files;
            $architecture_rules['forbidden'] = array_merge($architecture_rules['forbidden'], $request->forbidden_files);
            $architecture_rules['patterns'] = $request->patterns;

            $structure = [];
            foreach ($architecture_rules['required'] as $key => $value) {
                $structure[$value] = !empty(pathinfo($value, PATHINFO_EXTENSION)) ? 'file' : 'dir';
            }

            $architecture_rules['structure'] = $structure;

            return $architecture_rules;
    }

    private function basedTestConfig(): array
    {
        return [
            "type" => "http",
            "timeout_seconds" => 5,
            "retries" => 1,
            "tests" => []
        ];
    }

    private function buildTestConfig(BriefVersionRequest $request): array
    {
        $test_config = $this->basedTestConfig();

        return match ($request->type) {
            'http' => array_merge($test_config, [
                "base_path" => $request->base_path,
                "tests" => $request->tests
            ]),
            'browser' => array_merge($test_config, [
                "tests" => $request->tests
            ]),
            'function' => array_merge($test_config, [
                "language" => $request->language,
                "tests" => $request->tests
            ]),
            default => $test_config
        };
    }

    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        try {
            $perPage = request()->get('per_page', 5);

            [$brief_versions, $archived_brief_versions] = Octane::concurrently([
                fn() => BriefVersion::withoutTrashed()->paginate($perPage),
                fn() => BriefVersion::onlyTrashed()->paginate($perPage),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully fetched brief versions.',
                'data' => compact('brief_versions', 'archived_brief_versions'),
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'An error occurred while fetching brief versions: ' . $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BriefVersionRequest $request)
    {
        try
        {
            $architecture_rules = $this->buildArchitecureRules($request);
            $test_config = $this->buildTestConfig($request);

            $brief_version = BriefVersion::create([
                'brief_id' => $request->brief_id,
                'version' => $request->version,
                'architecture_rules' => $architecture_rules,
                'test_config' => $test_config
            ]);

            if (!$brief_version) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Failed to create brief version. Please try again.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Brief version created successfully.',
                'data' => $brief_version
            ], 201);
        }
        catch (\Throwable $e)
        {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'An error occurred while creating the brief version: ' . $e->getMessage(),
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
            $brief_version = BriefVersion::with(['brief'])->find($id);

            if (!$brief_version) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Brief version not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Brief version fetched successfully.',
                'data' => compact('brief_version')
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'An error occurred while fetching the brief version: ' . $e->getMessage(),
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
            $brief_version = BriefVersion::find($id);

            if (!$brief_version) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Brief version not found.'
                ], 404);
            }

            $is_deleted = $brief_version->delete();

            if ($is_deleted) {
                return response()->json([
                    'success' => true,
                    'data' => compact('brief_version'),
                    'message' => 'Successfully deleted brief version.'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to delete brief version. Please try again.'
            ], 400);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'An error occurred while deleting the brief version: ' . $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }
}
