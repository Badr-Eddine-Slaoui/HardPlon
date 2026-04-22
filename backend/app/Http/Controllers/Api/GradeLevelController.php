<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GradeLevelRequest;
use App\Models\GradeLevel;
use Illuminate\Http\Request;
use Laravel\Octane\Facades\Octane;

class GradeLevelController extends Controller
{
    public function index()
    {
        try {
            $per_page = request()->get("per_page", 5);

            [$grade_levels, $archived_grade_levels] = Octane::concurrently([
                fn() => GradeLevel::withoutTrashed()->with(['formations'])->orderByDesc('created_at')->paginate($per_page),
                fn() => GradeLevel::onlyTrashed()->with(['formations'])->orderByDesc('created_at')->paginate($per_page)
            ]);

            return response()->json([
                "success" => true,
                "data" => compact("grade_levels", "archived_grade_levels"),
                "message" => "Successfully fetched grade levels."
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

    public function show(Request $request, int $id){

        try {
            $grade_level = GradeLevel::with(['formations'])->find($id);

            if (!$grade_level) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Grade level not found."
                ], 404);
            }

            return response()->json([
                "success" => true,
                "data" => compact("grade_level"),
                "message" => "Successfully fetched grade level."
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

    public function store(GradeLevelRequest $request)
    {
        try {
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

    public function update(GradeLevelRequest $request, int $id)
    {
        try {
            $grade_level = GradeLevel::find($id);

            if (!$grade_level) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Grade level not found."
                ], 404);
            }

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
        try {
            $grade_level = GradeLevel::find($id);

            if (!$grade_level) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Grade level not found."
                ], 404);
            }

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
            $grade_level = GradeLevel::onlyTrashed()->find($id);

            if (!$grade_level) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Grade level not found."
                ], 404);
            }

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
