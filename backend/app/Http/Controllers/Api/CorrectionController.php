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
        $corrections = Correction::withoutTrashed()
            ->with(["brief", "teacher", "student", "correction_details.brief_skill_level.skill", "correction_details.brief_skill_level.level"])
            ->where("student_id", $id ?? Auth::id())
            ->orderByDesc("created_at")
            ->get();
        return response()->json([
            "success" => true,
            "data" => compact("corrections")
        ], 200);
    }

    public function show(Request $request, int $id){
        $correction = Correction::withoutTrashed()
            ->with(["brief", "teacher", "student", "correction_details.brief_skill_level.skill", "correction_details.brief_skill_level.level"])
            ->where("brief_id", $id)
            ->where("student_id", Auth::id())
            ->orderByDesc("created_at")
            ->first();
        return response()->json([
            "success"=> true,
            "data" => compact("correction")
        ], 200);
    }

    public function get_student_correction(int $brief_id, int $student_id){
        $correction = Correction::withoutTrashed()
            ->with(["brief", "teacher", "student", "correction_details.brief_skill_level.skill", "correction_details.brief_skill_level.level"])
            ->where("brief_id", $brief_id)
            ->where("student_id", $student_id)
            ->orderByDesc("created_at")
            ->first();
        return response()->json([
            "success"=> true,
            "data" => compact("correction")
        ], 200);
    }

    public function store(CorrectionRequest $request){
        $correction = Correction::create([
            'brief_id' => $request->brief_id,
            'student_id' => $request->student_id,
            'teacher_id' => Auth::id(),
            'message' => $request->message,
        ]);

        if($correction){
            foreach($request->details as $item){
                CorrectionDetail::create([
                    'correction_id' => $correction->id,
                    'brief_skill_level_id' => $item['brief_skill_level_id'],
                    'grade' => $item['grade'],
                ]);
            }

            return response()->json([
                "success" => true,
                "data" => compact("correction")
            ], 200);
        }
    }

    public function update(CorrectionRequest $request, Correction $correction){
        $is_updated = $correction->update([
            'brief_id' => $request->brief_id,
            'student_id' => $request->student_id,
            'teacher_id' => Auth::id(),
            'message' => $request->message,
        ]);

        if($is_updated){

            $correction->correction_details()->delete();

            foreach($request->details as $item){
                CorrectionDetail::create([
                    'correction_id' => $correction->id,
                    'brief_skill_level_id' => $item['brief_skill_level_id'],
                    'grade' => $item['grade'],
                ]);
            }

            return response()->json([
                "success" => true,
                "data" => compact("correction")
            ], 200);
        }
    }
}
