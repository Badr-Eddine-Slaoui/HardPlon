<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScholarYearRequest;
use App\Models\ScholarYear;
use Illuminate\Http\Request;
use Laravel\Octane\Facades\Octane;

class ScholarYearController extends Controller
{
    public function index()
    {

        try {
            $per_page = request()->get("per_page", 5);

            [$scholar_years, $archived_years, $actived_years, $current_year] = Octane::concurrently([
                fn() => ScholarYear::withoutTrashed()->with(['grade_levels'])->orderByDesc('created_at')->paginate($per_page),
                fn() => ScholarYear::onlyTrashed()->with(['grade_levels'])->orderByDesc('created_at')->paginate($per_page),
                fn() => ScholarYear::withoutTrashed()->orderByDesc('created_at')->get(),
                fn() => ScholarYear::withoutTrashed()->get()->filter(function ($year) {
                    return $year->is_current();
                })->first(),
            ]);

            return response()->json([
                "success" => true,
                "data" => compact("scholar_years", "archived_years", "current_year", "actived_years"),
                "message" => "Successfully fetched scholar years."
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
            $scholar_year = ScholarYear::with(['grade_levels'])->withTrashed()->find($id);

            if (!$scholar_year) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Scholar year not found."
                ], 404);
            }

            return response()->json([
                "success" => true,
                "data" => compact("scholar_year"),
                "message" => "Successfully fetched scholar year."
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

    public function store(ScholarYearRequest $request)
    {
        try {
            $scholar_year = ScholarYear::create([
                "start_year" => $request->start_year,
                "end_year" => $request->end_year,
                "capacity" => $request->capacity
            ]);

            if ($scholar_year) {
                return response()->json([
                    "success" => true,
                    "data" => compact("scholar_year"),
                    "message" => "Successfully created scholar year."
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

    public function update(ScholarYearRequest $request, int $id)
    {
        try {
            $scholar_year = ScholarYear::withTrashed()->find($id);

            if (!$scholar_year) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Scholar year not found."
                ], 404);
            }

            $is_updated = $scholar_year->update([
                "start_year" => $request->start_year,
                "end_year" => $request->end_year,
                "capacity" => $request->capacity
            ]);

            if ($is_updated) {
                return response()->json([
                    "success" => true,
                    "data" => compact("scholar_year"),
                    "message" => "Successfully updated scholar year."
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
            $scholar_year = ScholarYear::withoutTrashed()->find($id);

            if (!$scholar_year) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Scholar year not found."
                ], 404);
            }

            $is_deleted = $scholar_year->delete();

            if ($is_deleted) {
                return response()->json([
                    "success" => true,
                    "data" => compact("scholar_year"),
                    "message" => "Successfully deleted scholar year."
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
            $scholar_year = ScholarYear::onlyTrashed()->find($id);

            if (!$scholar_year) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Scholar year not found."
                ], 404);
            }

            $is_restored = $scholar_year->restore();

            if ($is_restored) {
                return response()->json([
                    "success" => true,
                    "data" => compact("scholar_year"),
                    "message" => "Successfully restored scholar year."
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
