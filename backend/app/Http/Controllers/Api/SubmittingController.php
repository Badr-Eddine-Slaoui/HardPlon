<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmittingRequest;
use App\Models\Submitting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmittingController extends Controller
{
    public function get_student_submittings(?int $id = null){
        $submittings = Submitting::withoutTrashed()
            ->with(["student", "squad.squad_members.student", "brief.sprint", "brief.class_group", "brief.teacher", "brief.brief_skill_levels.skill", "brief.brief_skill_levels.level"])
            ->where("student_id", $id ?? Auth::id())
            ->orderByDesc("created_at")
            ->get();
        return response()->json([
            "success" => true,
            "data" => compact("submittings")
        ], 200);
    }

    public function show(Request $request, Submitting $submitting)
    {
        $submitting->loadMissing(["student", "squad.squad_members.student", "brief.sprint", "brief.class_group", "brief.teacher", "brief.brief_skill_levels.skill", "brief.brief_skill_levels.level"]);
        return response()->json([
            "success"=> true,
            "data" => compact("submitting")
        ], 200);
    }

    public function store(SubmittingRequest $request)
    {
        $submitting = Submitting::create([
            'brief_id' => $request->brief_id,
            'student_id' => Auth::id(),
            'squad_id' => $request->squad_id,
            'message' => $request->message,
            'links' => $request->links,
        ]);

        if ($submitting) {
            return response()->json([
                "success" => true,
                "data" => compact("submitting"),
                "message" => "Successfully created submitting."
            ], 200);
        }

        return response()->json([
            "success" => false,
            "data" => $request->all(),
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function update(SubmittingRequest $request, Submitting $submitting)
    {
        $is_updated = $submitting->update([
            'brief_id' => $request->brief_id,
            'student_id' => $request->student_id,
            'squad_id' => $request->squad_id,
            'message' => $request->message,
            'links' => json_encode($request->links),
        ]);

        if ($is_updated) {
            return response()->json([
                "success" => true,
                "data" => compact("submitting"),
                "message" => "Successfully updated submitting."
            ], 200);
        }

        return response()->json([
            "success"=> false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function destroy(Submitting $submitting)
    {
        $is_deleted = $submitting->delete();

        if ($is_deleted) {
            return response()->json([
                "success" => true,
                "data" => compact("submitting"),
                "message" => "Successfully deleted submitting."
            ], 200);
        }

        return response()->json([
            "success"=> false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function restore(int $id)
    {
        $submitting = Submitting::withTrashed()->find($id);
        $is_restored = $submitting->restore();

        if ($is_restored) {
            return response()->json([
                "success" => true,
                "data" => compact("submitting"),
                "message" => "Successfully restored submitting."
            ], 200);
        }

        return response()->json([
            "success"=> false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }
}
