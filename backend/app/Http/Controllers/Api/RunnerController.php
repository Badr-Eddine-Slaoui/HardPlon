<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RunnerRequest;
use App\Models\Runner;
use Laravel\Octane\Facades\Octane;

class RunnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $perPage = request()->get('per_page', 5);

            [$runners, $archived_runners] = Octane::concurrently([
                fn() => Runner::with('versions')->where('status', 'active')->paginate($perPage),
                fn() => Runner::with('versions')->where('status', 'deprecated')->paginate($perPage),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully fetched runners.',
                'data' => compact('runners', 'archived_runners'),
            ], 200);

        } catch (\Throwable $e) {
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
    public function store(RunnerRequest $request)
    {
        try {

            $runner = Runner::create([
                'name' => $request->name,
                'description' => $request->description,
                'type' => $request->type,
                'status' => $request->status,
            ]);

            if ($runner) {
                return response()->json([
                    'success' => true,
                    'data' => compact('runner'),
                    'message' => 'Successfully created runner.'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create runner. Please try again.'
            ], 400);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'An error occurred while creating the runner: ' . $e->getMessage(),
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
            $runner = Runner::with('versions')->find($id);

            if (!$runner) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Runner not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => compact('runner'),
                'message' => 'Successfully fetched runner.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'An error occurred while fetching the runner: ' . $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RunnerRequest $request, int $id)
    {
        try {
            $runner = Runner::find($id);

            if (!$runner) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Runner not found.'
                ], 404);
            }

            $is_updated = $runner->update([
                'name' => $request->name,
                'description' => $request->description,
                'type' => $request->type,
                'status' => $request->status,
            ]);

            if (!$is_updated) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Failed to update runner. Please try again.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => compact('runner'),
                'message' => 'Successfully updated runner.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'An error occurred while updating the runner: ' . $e->getMessage(),
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
            $runner = Runner::find($id);

            if (!$runner) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Runner not found.'
                ], 404);
            }

            $is_deleted = $runner->delete();

            if (!$is_deleted) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Failed to delete runner. Please try again.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => compact('runner'),
                'message' => 'Successfully deleted runner.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'An error occurred while deleting the runner: ' . $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }
}
