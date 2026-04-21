<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProblemSubmissionRequest;
use App\Models\ProblemSubmission;
use App\Models\ProblemSubmissionJob;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Laravel\Octane\Facades\Octane;

class ProblemSubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $perPage = request()->get('per_page', 5);

            [$problem_submissions, $archived_problem_submissions] = Octane::concurrently([
                fn() => ProblemSubmission::withoutTrashed()->paginate($perPage),
                fn() => ProblemSubmission::onlyTrashed()->paginate($perPage),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully fetched problem submissions.',
                'data' => compact('problem_submissions', 'archived_problem_submissions'),
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'An error occurred while fetching problem submissions: ' . $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProblemSubmissionRequest $request)
    {
        try {
            
            $submission = Submission::find($request->submission_id);

            if (!$submission) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Submission not found.'
                ], 404);
            }

            $problem_submissions = [];

            foreach ($request->submissions as $submission) {
                $problem_submission = ProblemSubmission::create([
                    'submission_id' => $request->submission_id,
                    'problem_id' => $submission['problem_id'],
                    'code' => $submission['code']
                ]);

                $problem_submissions[] = $problem_submission;
            }

            $job = ProblemSubmissionJob::create([
                'submission_id' => $request->submission_id,
                'status' => 'pending',
            ]);

            if (!$job) {
                return response()->json([
                    "success"=> false,
                    "message" => "Submission created but failed to create problem submission job. Please contact support."
                ], 400);
            }

            Redis::publish('problem.submissions.jobs.created', json_encode([
                'job_id' => $job->id,
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Problem submission created successfully.',
                'data' => $problem_submissions
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'An error occurred while creating the problem submission: ' . $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            $problem_submission = ProblemSubmission::withTrashed()->with('problem')->find($id);

            if (!$problem_submission) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Problem submission not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Problem submission fetched successfully.',
                'data' => compact('problem_submission')
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'An error occurred while fetching the problem submission: ' . $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $problem_submission = ProblemSubmission::withTrashed()->find($id);

            if (!$problem_submission) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Problem submission not found.'
                ], 404);
            }

            $is_deleted = $problem_submission->delete();

            if (!$is_deleted) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Failed to delete problem submission.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Problem submission deleted successfully.'
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'An error occurred while deleting the problem submission: ' . $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }
}
