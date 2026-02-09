<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LevelRequest;
use App\Models\Level;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function index()
    {
        $levels = Level::withTrashed()->orderByDesc('created_at')->get();
        $archived_levels = Level::onlyTrashed()->get();

        return response()->json([
            "success" => true,
            "data" => compact("levels", "archived_levels")
        ], 200);
    }

    public function show(Request $request, Level $level)
    {
        return response()->json([
            "success"=> true,
            "data" => compact("level")
        ], 200);
    }

    public function store(LevelRequest $request)
    {
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
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function update(LevelRequest $request, Level $level)
    {
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
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function destroy(Level $level)
    {
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
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function restore(int $id)
    {
        $level = Level::withTrashed()->find($id);
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
            "message" => "Something went wrong. Please try again."
        ], 500);
    }
}
