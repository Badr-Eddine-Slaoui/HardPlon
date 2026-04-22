<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LevelRequest;
use App\Models\Level;
use Illuminate\Http\Request;
use Laravel\Octane\Facades\Octane;

class LevelController extends Controller
{
    public function index()
    {
        try {
            $per_page = request()->get("per_page", 5);

            [$levels, $archived_levels] = Octane::concurrently([
                fn() => Level::withoutTrashed()->orderByDesc('created_at')->paginate($per_page),
                fn() => Level::onlyTrashed()->orderByDesc('created_at')->paginate($per_page)
            ]);

            return response()->json([
                "success" => true,
                "data" => compact("levels", "archived_levels"),
                "message" => "Successfully fetched levels."
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "Something went wrong. Please try again.",
                "code" => $e->getCode()
            ], 500);
        }
    }

    public function show(Request $request, int $id)
    {
        try {
            $level = Level::withTrashed()->find($id);

            if (!$level) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Level not found."
                ], 404);
            }

            return response()->json([
                "success"=> true,
                "data" => compact("level"),
                "message" => "Successfully fetched level."
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "Something went wrong. Please try again.",
                "code" => $e->getCode()
            ], 500);
        }
    }

    public function store(LevelRequest $request)
    {
        try {
            $level = Level::create([
                "name"=> $request->name,
                "description" => $request->description,
                "order" => $request->order
            ]);

            if ($level) {
                return response()->json([
                    "success" => true,
                    "data" => compact("level"),
                    "message" => "Successfully created level."
                ], 200);
            }

            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "Something went wrong. Please try again.",
            ], 400);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "Something went wrong. Please try again.",
                "code" => $e->getCode()
            ], 500);
        }
    }

    public function update(LevelRequest $request, int $id)
    {
        try {
            $level = Level::withTrashed()->find($id);

            if (!$level) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Level not found."
                ], 404);
            }

            $is_updated = $level->update([
                "name"=> $request->name,
                "description" => $request->description,
                "order" => $request->order
            ]);

            if ($is_updated) {
                return response()->json([
                    "success" => true,
                    "data" => compact("level"),
                    "message" => "Successfully updated level."
                ], 200);
            }

            return response()->json([
                "success"=> false,
                "data" => null,
                "message" => "Something went wrong. Please try again."
            ], 400);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "Something went wrong. Please try again.",
                "code" => $e->getCode()
            ], 500);
        }
    }

    public function destroy(int $id)
    {
        try{
            $level = Level::withoutTrashed()->find($id);

            if (!$level) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Level not found."
                ], 404);
            }

            $is_deleted = $level->delete();

            if ($is_deleted) {
                return response()->json([
                    "success" => true,
                    "data" => compact("level"),
                    "message" => "Successfully deleted level."
                ], 200);
            }

            return response()->json([
                "success"=> false,
                "data" => null,
                "message" => "Something went wrong. Please try again."
            ], 400);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "Something went wrong. Please try again.",
                "code" => $e->getCode()
            ], 500);
        }
    }

    public function restore(int $id)
    {
        try {
            $level = Level::withTrashed()->find($id);

            if (!$level) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Level not found."
                ], 404);
            }

            $is_restored = $level->restore();

            if ($is_restored) {
                return response()->json([
                    "success" => true,
                    "data" => compact("level"),
                    "message" => "Successfully restored level."
                ], 200);
            }

            return response()->json([
                "success"=> false,
                "data" => null,
                "message" => "Something went wrong. Please try again."
            ], 400);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "Something went wrong. Please try again.",
                "code" => $e->getCode()
            ], 500);
        }
    }
}
