<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmissionRequest;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class SubmissionController extends Controller
{
    public function get_student_submissions(?int $id = null){
        $submissions = Submission::withoutTrashed()
            ->with(["student", "squad.squad_members.student", "brief.sprint", "brief.class_group", "brief.teacher", "brief.brief_skill_levels.skill", "brief.brief_skill_levels.level"])
            ->where("student_id", $id ?? Auth::id())
            ->orderByDesc("created_at")
            ->get();
        return response()->json([
            "success" => true,
            "data" => compact("submissions")
        ], 200);
    }

    public function show(Request $request, Submission $submission)
    {
        $submission->loadMissing(["student", "squad.squad_members.student", "brief.sprint", "brief.class_group", "brief.teacher", "brief.brief_skill_levels.skill", "brief.brief_skill_levels.level"]);
        return response()->json([
            "success"=> true,
            "data" => compact("submission")
        ], 200);
    }

    public function store(SubmissionRequest $request)
    {
        $submission = Submission::create([
            'brief_id' => $request->brief_id,
            'student_id' => Auth::id(),
            'squad_id' => $request->squad_id,
            'message' => $request->message,
            'link' => $request->link,
        ]);

        if (!$submission) {
            return response()->json([
                "success"=> false,
                "message" => "Something went wrong. Please try again."
            ], 400);
        }

        $job = $submission->evaluationJob()->create([
            'status' => 'pending',
        ]);

        if (!$job) {
            return response()->json([
                "success"=> false,
                "message" => "Submission created but failed to create evaluation job. Please contact support."
            ], 400);
        }

        Redis::publish('evaluation.jobs.created', json_encode([
            'job_id' => $job->id,
        ]));

        return response()->json([
            "success" => true,
            "data" => compact("submission"),
            "message" => "Successfully created submission."
        ], 201);
    }

    public function update(SubmissionRequest $request, Submission $submission)
    {
        $is_updated = $submission->update([
            'brief_id' => $request->brief_id,
            'student_id' => $request->student_id,
            'squad_id' => $request->squad_id,
            'message' => $request->message,
            'link' => $request->link,
        ]);

        if ($is_updated) {
            return response()->json([
                "success" => true,
                "data" => compact("submission"),
                "message" => "Successfully updated submission."
            ], 200);
        }

        return response()->json([
            "success"=> false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function destroy(Submission $submission)
    {
        $is_deleted = $submission->delete();

        if ($is_deleted) {
            return response()->json([
                "success" => true,
                "data" => compact("submission"),
                "message" => "Successfully deleted submission."
            ], 200);
        }

        return response()->json([
            "success"=> false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function restore(int $id)
    {
        $submission = Submission::withTrashed()->find($id);
        $is_restored = $submission->restore();

        if ($is_restored) {
            return response()->json([
                "success" => true,
                "data" => compact("submission"),
                "message" => "Successfully restored submission."
            ], 200);
        }

        return response()->json([
            "success"=> false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }
}
