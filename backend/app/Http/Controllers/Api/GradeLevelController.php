<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GradeLevelRequest;
use App\Models\GradeLevel;
use Illuminate\Http\Request;

class GradeLevelController extends Controller
{
    public function index()
    {
        $grade_levels = GradeLevel::withTrashed()->with(['formations'])->orderByDesc('created_at')->get();
        $archived_grade_levels = GradeLevel::onlyTrashed()->get();

        return response()->json([
            "success" => true,
            "data" => compact("grade_levels", "archived_grade_levels")
        ], 200);
    }

    public function show(Request $request, GradeLevel $grade_level){

        $grade_level->loadMissing('formations');

        return response()->json([
            "success" => true,
            "data" => compact("grade_level")
        ], 200);
    }

    public function store(GradeLevelRequest $request)
    {
        $grade_level = GradeLevel::create([
            "name" => $request->name,
            "capacity" => $request->capacity,
            "description" => $request->description,
            "scholar_year_id" => $request->scholar_year_id
        ]);

        if ($grade_level) {
            return response()->json([
                "success" => true,
                "data" => compact("grade_level"),
                "message" => "Successfully created grade level."
            ], 200);
        }

        return response()->json([
            "success" => false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function update(GradeLevelRequest $request, GradeLevel $grade_level)
    {
        $is_updated = $grade_level->update([
            "name"=> $request->name,
            "capacity" => $request->capacity,
            "description" => $request->description,
            "scholar_year_id" => $request->scholar_year_id
        ]);

        if ($is_updated) {
            return response()->json([
                "success" => true,
                "data" => compact("grade_level"),
                "message" => "Successfully updated grade level."
            ], 200);
        }

        return response()->json([
            "success"=> false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function destroy(GradeLevel $grade_level)
    {
        $is_deleted = $grade_level->delete();

        if ($is_deleted) {
            return response()->json([
                "success" => true,
                "data" => compact("grade_level"),
                "message" => "Successfully deleted grade level."
            ], 200);
        }

        return response()->json([
            "success"=> false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function restore(int $id)
    {
        $grade_level = GradeLevel::withTrashed()->find($id);
        $is_restored = $grade_level->restore();

        if ($is_restored) {
            return response()->json([
                "success" => true,
                "data" => compact("grade_level"),
                "message" => "Successfully restored grade level."
            ], 200);
        }

        return response()->json([
            "success"=> false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }
}
