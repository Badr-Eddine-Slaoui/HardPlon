<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScholarYearRequest;
use App\Models\ScholarYear;
use Illuminate\Http\Request;

class ScholarYearController extends Controller
{
    public function index()
    {
        $scholar_years = ScholarYear::withTrashed()->with(['grade_levels'])->orderByDesc('created_at')->get();
        $archived_years = ScholarYear::onlyTrashed()->get();
        $actived_years = ScholarYear::withoutTrashed()->get();
        $current_year = $actived_years->filter(function ($year) {
            return $year->is_current();
        })->first();

        return response()->json([
            "success" => true,
            "data" => compact("scholar_years", "archived_years", "current_year")
        ], 200);
    }

    public function show(Request $request, ScholarYear $scholar_year){

        $scholar_year->loadMissing('grade_levels');

        return response()->json([
            "success" => true,
            "data" => compact("scholar_year")
        ], 200);
    }

    public function store(ScholarYearRequest $request)
    {
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
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function update(ScholarYearRequest $request, ScholarYear $scholar_year)
    {
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
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function destroy(ScholarYear $scholar_year)
    {
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
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function restore(int $id)
    {
        $scholar_year = ScholarYear::withTrashed()->find($id);
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
            "message" => "Something went wrong. Please try again."
        ], 500);
    }
}
