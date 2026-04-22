<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StackRequest;
use App\Models\Stack;
use Illuminate\Http\Request;
use Laravel\Octane\Facades\Octane;

class StackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $perPage = request()->get('per_page', 5);

            [$stacks, $archived_stacks] = Octane::concurrently([
                fn() => Stack::withoutTrashed()->paginate($perPage),
                fn() => Stack::onlyTrashed()->paginate($perPage),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully fetched stacks.',
                'data' => compact('stacks', 'archived_stacks'),
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
    public function store(StackRequest $request)
    {
        try {

            $stack = Stack::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            if ($stack) {
                return response()->json([
                    'success' => true,
                    'data' => compact('stack'),
                    'message' => 'Successfully created stack.'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create stack. Please try again.'
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
            $stack = Stack::with(['stack_runners'])->find($id);

            if ($stack) {
                return response()->json([
                    'success' => true,
                    'data' => compact('stack'),
                    'message' => 'Successfully fetched stack.'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Stack not found.'
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
    public function update(StackRequest $request, int $id)
    {
        try {
            $stack = Stack::find($id);

            if (!$stack) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Stack not found.'
                ], 404);
            }

            $is_updated = $stack->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            if (!$is_updated) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Failed to update stack. Please try again.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => compact('stack'),
                'message' => 'Successfully updated stack.'
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
            $stack = Stack::find($id);

            if (!$stack) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Stack not found.'
                ], 404);
            }

            $is_deleted = $stack->delete();

            if (!$is_deleted) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Failed to delete stack. Please try again.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => compact('stack'),
                'message' => 'Successfully deleted stack.'
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
