<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CorrectionRequest;
use App\Models\Correction;
use App\Models\CorrectionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CorrectionController extends Controller
{
    public function get_student_corrections(?int $id = null){
        try {
            $corrections = Correction::withoutTrashed()
                ->with(["brief", "teacher", "student", "correction_details.brief_skill_level.skill", "correction_details.brief_skill_level.level"])
                ->where("student_id", $id ?? Auth::guard("api")->id())
                ->orderByDesc("created_at")
                ->get();

            return response()->json([
                "success" => true,
                "data" => compact("corrections"),
                "message" => "Successfully fetched student corrections."
            ], 200);
        }
        catch (\Throwable $e)
        {
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
            $correction = Correction::withoutTrashed()
                ->with(["brief", "teacher", "student", "correction_details.brief_skill_level.skill", "correction_details.brief_skill_level.level"])
                ->where("brief_id", $id)
                ->where("student_id", Auth::guard("api")->id())
                ->orderByDesc("created_at")
                ->first();

            if(!$correction){
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Correction not found."
                ], 404);
            }

            return response()->json([
                "success"=> true,
                "data" => compact("correction"),
                "message" => "Successfully fetched student correction."
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

    public function get_student_correction(int $brief_id, int $student_id){
        try {
            $correction = Correction::withoutTrashed()
                ->with(["brief", "teacher", "student", "correction_details.brief_skill_level.skill", "correction_details.brief_skill_level.level"])
                ->where("brief_id", $brief_id)
                ->where("student_id", $student_id)
                ->orderByDesc("created_at")
                ->first();

            if(!$correction){
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Correction not found."
                ], 404);
            }

            return response()->json([
                "success"=> true,
                "data" => compact("correction")
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
    
}
