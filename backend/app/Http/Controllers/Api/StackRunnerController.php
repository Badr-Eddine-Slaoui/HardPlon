<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StackRunnerRequest;
use App\Models\StackRunner;
use Illuminate\Http\Request;
use Laravel\Octane\Facades\Octane;

class StackRunnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $perPage = request()->get('per_page', 5);

            [$stackRunners, $archivedStackRunners] = Octane::concurrently([
                fn() => StackRunner::with(['stack', 'runner_version'])->withoutTrashed()->paginate($perPage),
                fn() => StackRunner::with(['stack', 'runner_version'])->onlyTrashed()->paginate($perPage),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully fetched stack-runner associations.',
                'data' => compact('stackRunners', 'archivedStackRunners'),
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => "Something went wrong. Please try again.",
                'code' => $e->getCode(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StackRunnerRequest $request)
    {
        try {

            $stackRunner = StackRunner::create([
                'stack_id' => $request->stack_id,
                'runner_version_id' => $request->runner_version_id,
                'priority' => $request->priority,
            ]);

            if ($stackRunner) {
                return response()->json([
                    'success' => true,
                    'data' => compact('stackRunner'),
                    'message' => 'Successfully associated runner with stack.'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to associate runner with stack. Please try again.'
            ], 400);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => "Something went wrong. Please try again.",
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
            $stackRunner = StackRunner::with(['stack', 'runner_version'])->find($id);

            if ($stackRunner) {
                return response()->json([
                    'success' => true,
                    'data' => compact('stackRunner'),
                    'message' => 'Successfully fetched stack-runner association.'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Stack-runner association not found.'
            ], 404);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => "Something went wrong. Please try again.",
                'code' => $e->getCode(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StackRunnerRequest $request, int $id)
    {
        try {
            $stackRunner = StackRunner::find($id);

            if (!$stackRunner) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Stack-runner association not found.'
                ], 404);
            }

            $is_updated = $stackRunner->update([
                'stack_id' => $request->stack_id,
                'runner_version_id' => $request->runner_version_id,
                'priority' => $request->priority,
            ]);

            if (!$is_updated) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Failed to update stack-runner association. Please try again.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => compact('stackRunner'),
                'message' => 'Successfully updated stack-runner association.'
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => "Something went wrong. Please try again.",
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
            $stackRunner = StackRunner::find($id);

            if (!$stackRunner) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Stack-runner association not found.'
                ], 404);
            }

            $is_deleted = $stackRunner->delete();

            if (!$is_deleted) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Failed to delete stack-runner association. Please try again.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => compact('stackRunner'),
                'message' => 'Successfully deleted stack-runner association.'
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => "Something went wrong. Please try again.",
                'code' => $e->getCode(),
            ], 500);
        }
    }
}
