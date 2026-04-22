<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProblemRequest;
use App\Models\Problem;
use Laravel\Octane\Facades\Octane;

class ProblemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $perPage = request()->get('per_page', 5);

            [$problems, $archived_problems] = Octane::concurrently([
                fn() => Problem::withoutTrashed()->paginate($perPage),
                fn() => Problem::onlyTrashed()->paginate($perPage),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully fetched problems.',
                'data' => compact('problems', 'archived_problems'),
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
    public function store(ProblemRequest $request)
    {
        try {

            $problem = Problem::create([
                'brief_id' => $request->brief_id,
                'brief_skill_level_id' => $request->brief_skill_level_id,
                'language_id' => $request->language_id,
                'title' => $request->title,
                'description' => $request->description,
                'hidden_header' => $request->hidden_header,
                'hidden_footer' => $request->hidden_footer,
                'skeleton_code' => $request->skeleton_code,
                'order_index' => $request->order_index
            ]);

            if ($problem) {
                return response()->json([
                    'success' => true,
                    'data' => compact('problem'),
                    'message' => 'Successfully created problem.'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create problem. Please try again.'
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
            $problem = Problem::with(['brief', 'brief_skill_level.skill', 'brief_skill_level.level', 'language'])->find($id);

            if (!$problem) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Problem not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Problem fetched successfully.',
                'data' => compact('problem')
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
    public function update(ProblemRequest $request, int $id)
    {
        try {
            $problem = Problem::find($id);

            if (!$problem) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Problem not found.'
                ], 404);
            }

            $is_updated = $problem->update([
                'brief_id' => $request->brief_id,
                'brief_skill_level_id' => $request->brief_skill_level_id,
                'language_id' => $request->language_id,
                'title' => $request->title,
                'description' => $request->description,
                'hidden_header' => $request->hidden_header,
                'hidden_footer' => $request->hidden_footer,
                'skeleton_code' => $request->skeleton_code,
                'order_index' => $request->order_index
            ]);

            if ($is_updated) {
                return response()->json([
                    'success' => true,
                    'data' => compact('problem'),
                    'message' => 'Successfully updated problem.'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to update problem. Please try again.'
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
            $problem = Problem::find($id);

            if (!$problem) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Problem not found.'
                ], 404);
            }

            $is_deleted = $problem->delete();

            if ($is_deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Problem deleted successfully.'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to delete problem. Please try again.'
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
