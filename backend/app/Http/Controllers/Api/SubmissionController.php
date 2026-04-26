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

        try {
            $submissions = Submission::withoutTrashed()
                ->with([
                    'student',
                    'brief.stack',
                    'brief.brief_skill_levels.skill',
                    'brief.brief_skill_levels.level',
                    'brief.problems.language',
                    'brief.problems.test_cases',
                    'brief.brief_versions' => fn($q) => $q->latest(),
                    'evaluationJob',
                    'problemSubmissions.problem.language',
                    'problemSubmissionJob',
                    "correction"
                ])
                ->where("student_id", $id ?? Auth::id())
                ->orderByDesc("created_at")
                ->get();

            return response()->json([
                "success" => true,
                "data" => compact("submissions"),
                "message" => "Successfully fetched submissions."
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
            $submission = Submission::with([
                "student",
                "squad.squad_members.student",
                "brief.sprint",
                "brief.class_group",
                "brief.teacher",
                "brief.brief_skill_levels.skill",
                "brief.brief_skill_levels.level",
                "brief.problems.language",
                "brief.problems.test_cases",
                "evaluationJob",
                "problemSubmissions.problem.language",
                "problemSubmissionJob",
                "correction.correction_details.brief_skill_level.skill",
                "correction.correction_details.brief_skill_level.level"
            ])->find($id);

            if (!$submission) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Submission not found."
                ], 404);
            }

            return response()->json([
                "success"=> true,
                "data" => compact("submission"),
                "message" => "Successfully fetched submission."
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "Something went wrong. Please try again.",
                "code" => $e->getCode(),
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function store(SubmissionRequest $request)
    {
        try {
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

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "Something went wrong. Please try again: " . $e->getMessage(),
                "trace" => $e->getTraceAsString(),
                "code" => $e->getCode()
            ], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $submission = Submission::withoutTrashed()->find($id);

            if (!$submission) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Submission not found."
                ], 404);
            }

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
            $submission = Submission::onlyTrashed()->find($id);

            if (!$submission) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Submission not found."
                ], 404);
            }

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

    public function get_student_brief_submission(int $briefId, int $studentId)
    {
        try {
            $submission = Submission::withoutTrashed()
                ->with([
                    'student',
                    'brief.stack',
                    'brief.brief_skill_levels.skill',
                    'brief.brief_skill_levels.level',
                    'brief.problems.language',
                    'brief.problems.test_cases',
                    'brief.brief_versions' => fn($q) => $q->latest(),
                    'evaluationJob',
                    'problemSubmissions.problem.language',
                    'problemSubmissionJob',
                    "correction"
                ])
                ->where('brief_id', $briefId)
                ->where('student_id', $studentId)
                ->first();

            return response()->json([
                'success' => true,
                'data' => compact('submission'),
                'message' => 'Successfully fetched student brief submission.'
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => "Something went wrong. Please try again.",
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
