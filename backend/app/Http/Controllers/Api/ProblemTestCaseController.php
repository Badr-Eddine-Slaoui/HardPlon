<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProblemTestCaseRequest;
use App\Models\ProblemTestCase;
use Laravel\Octane\Facades\Octane;

class ProblemTestCaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $perPage = request()->get('per_page', 5);

            [$problem_test_cases, $archived_problem_test_cases] = Octane::concurrently([
                fn() => ProblemTestCase::withoutTrashed()->paginate($perPage),
                fn() => ProblemTestCase::onlyTrashed()->paginate($perPage),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully fetched problem test cases.',
                'data' => compact('problem_test_cases', 'archived_problem_test_cases'),
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
    public function store(ProblemTestCaseRequest $request)
    {
        try {

            $problemTestCase = ProblemTestCase::create([
                'problem_id' => $request->problem_id,
                'input' => $request->input,
                'expected_output' => $request->expected_output,
                'is_hidden' => $request->is_hidden ?? false,
            ]);

            if ($problemTestCase) {
                return response()->json([
                    'success' => true,
                    'data' => compact('problemTestCase'),
                    'message' => 'Successfully created problem test case.'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create problem test case. Please try again.'
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
            $problemTestCase = ProblemTestCase::with(['problem'])->find($id);

            if (!$problemTestCase) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Problem test case not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => compact('problemTestCase'),
                'message' => 'Successfully fetched problem test case.'
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
     * Update the specified resource in storage.
     */
    public function update(ProblemTestCaseRequest $request, int $id)
    {
        try {
            $problemTestCase = ProblemTestCase::find($id);

            if (!$problemTestCase) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Problem test case not found.'
                ], 404);
            }

            $is_updated = $problemTestCase->update([
                'problem_id' => $request->problem_id,
                'input' => $request->input,
                'expected_output' => $request->expected_output,
                'is_hidden' => $request->is_hidden ?? false,
            ]);

            if ($is_updated) {
                return response()->json([
                    'success' => true,
                    'data' => compact('problemTestCase'),
                    'message' => 'Successfully updated problem test case.'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to update problem test case. Please try again.'
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
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $problemTestCase = ProblemTestCase::find($id);

            if (!$problemTestCase) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Problem test case not found.'
                ], 404);
            }

            $is_deleted = $problemTestCase->delete();

            if ($is_deleted) {
                return response()->json([
                    'success' => true,
                    'data' => compact('problemTestCase'),
                    'message' => 'Successfully deleted problem test case.'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to delete problem test case. Please try again.'
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
}
