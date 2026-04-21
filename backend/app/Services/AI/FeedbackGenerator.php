<?php

namespace App\Services\AI;

use App\Models\EvaluationJob;
use App\Models\Problem;
use App\Models\ProblemSubmissionJob;
use Gemini\Laravel\Facades\Gemini;

class FeedbackGenerator
{
    public function generate(array $payload): string
    {
        $prompt = "You are an expert programming evaluator. "
            . "You give structured, motivating, honest feedback to students based on coding evaluations.\n\n"
            . $this->buildPrompt($payload);

        $result = retry(3, function () use ($prompt) {
            return Gemini::generativeModel(model: 'gemini-2.5-flash')
                ->generateContent($prompt);
        }, 5000);

        return $result->text();
    }

    private function buildPrompt(array $data): string
    {
        return json_encode([
            "task" => "Evaluate this student's submission",
            "input" => $data,
            "instructions" => [
                "Be motivating but honest",
                "Explain weak points clearly",
                "Explain what to improve",
                "Mention test case performance",
                "Mention if brief is valid or not",
                "Give structured feedback"
            ]
        ], JSON_PRETTY_PRINT);
    }

    public function buildFeedbackPayload(EvaluationJob $job, ProblemSubmissionJob $problemSubmissionJob, float $final_score, string $brief_results): array
    {
        return [
            "brief" => [
                "title" => $job->submission->brief->title ?? null,
                "description" => $job->submission->brief->description ?? null,
                "content" => $job->submission->brief->content ?? null,
                "stack" => $job->submission->brief->stack->name ?? null,
            ],
            "evaluation" => [
                "final_score" => $final_score,
                "result" => $brief_results,
            ],
            "problem_submissions" => array_map(function($submissionResult) {
                $problem = Problem::find($submissionResult['problem_id']);

                return [
                    "skill" => $problem->brief_skill_level->skill->name ?? null,
                    "level" => $problem->brief_skill_level->level->name ?? null,
                    "title" => $problem->title ?? null,
                    "description" => $problem->description ?? null,
                    "score" => $submissionResult['score'],
                    "tests" => $submissionResult['tests'] ?? [],
                ];
            }, $problemSubmissionJob->result['submissions']),
        ];
    }
}