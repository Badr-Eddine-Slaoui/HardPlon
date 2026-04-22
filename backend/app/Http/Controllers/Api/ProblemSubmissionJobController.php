<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Correction;
use App\Models\CorrectionDetail;
use App\Models\EvaluationJob;
use App\Models\Problem;
use App\Models\ProblemSubmissionJob;
use App\Services\AI\FeedbackGenerator;
use Illuminate\Http\Request;

class ProblemSubmissionJobController extends Controller
{
    private function buildJobpayload(ProblemSubmissionJob $job)
    {
        $payload = [
            'submission_id' => $job->submission_id,
        ];

        $submissionsData = $job->submission->problemSubmissions()->with('problem.test_cases', 'problem.language')->get();

        $submissions = $submissionsData->map(function ($submission) {
            $retunValue = [
                'problem' => $submission->problem,
                'test_cases' => $submission->problem->test_cases->toArray(),
                'submission_code' => $submission->code,
            ];

            unset($retunValue['problem']['test_cases']);

            return $retunValue;
        });

        return [
            'job_id' => $job->id,
            'payload' => $payload,
            'submissions' => $submissions,
        ];
    }

    public function show(int $id)
    {
        try {
            $job = ProblemSubmissionJob::find($id);

            if (!$job) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Problem submission job not found."
                ], 404);
            }

            $job_payload = $this->buildJobpayload($job);

            return response()->json([
                "success" => true,
                "data" => compact('job_payload'),
                "message" => "Successfully fetched problem submission job."
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "Something went wrong. Please try again.",
                "code" => $e->getCode(),
            ], 500);
        }
    }


    public function update(Request $request, int $id)
    {
        try {

            $request->validate([
                'status' => 'in:pending,running,completed,failed',
                'result' => 'nullable|array',
            ]);

            $job = ProblemSubmissionJob::find($id);

            if (!$job) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Problem submission job not found."
                ], 404);
            }

            $is_updated = $job->update([
                'status' => $request->status ?? $job->status,
                'result' => $request->result ?? $job->result,
            ]);

            if (!$is_updated) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Failed to update problem submission job. Please try again."
                ], 400);
            }

            if($request->status === 'completed' && !is_null($request->result)) {

                $result = $request->result;

                $evaluation_job = EvaluationJob::where('submission_id', $job->submission_id)->first();

                if(is_null($evaluation_job)){
                    return response()->json([
                        "success" => false,
                        "data" => null,
                        "message" => "Failed to create correction for the submission. Evaluation job not found."
                    ], 400);
                }

                $final_score = $evaluation_job->result['architecture']['score'] * 0.25 + $evaluation_job->result['tests']['score'] * 0.75;

                $brief_results = $final_score > 7 ? "Valid" : "Invalid";

                $corrections = array_map(function($submissionResult) {
                    $problem = Problem::find($submissionResult['problem_id']);
                    return [
                        "brief_skill_level_id" => $problem->brief_skill_level_id,
                        "grade" => $submissionResult['score'] > 7 ?  "EXCELLENT" : ($submissionResult['score'] > 5 ? "AVERAGE" : "POOR"),
                    ];
                }, $result['submissions']);

                $ai = app(FeedbackGenerator::class);

                $aiMessage = $ai->generate($ai->buildFeedbackPayload($evaluation_job, $job, $final_score, $brief_results));

                $correction = Correction::create([
                    'brief_id' => $job->submission->brief_id,
                    'student_id' => $job->submission->student_id,
                    'teacher_id' => $job->submission->brief->teacher_id,
                    'message' => $aiMessage,
                    'result' => $brief_results
                ]);

                if($correction){
                    foreach($corrections as $item){
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

                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Failed to create correction for the submission."
                ], 400);
            }

            return response()->json([
                "success" => true,
                "data" => compact('job'),
                "message" => "Successfully updated problem submission job."
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "Something went wrong. Please try again.",
                "code" => $e->getCode(),
            ], 500);
        }
    }
}
