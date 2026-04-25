<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brief;
use App\Models\Correction;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Octane\Facades\Octane;

class StudentController extends Controller
{
    public function getDashboardData()
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user || $user->role !== 'STUDENT') {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Unauthorized"
                ], 403);
            }

            $id_class = $user->id_class;
            $student_id = $user->id;

            // Fetch statistics concurrently to match AdminController structure
            [$totalMissions, $completedMissions, $validCorrectionsCount] = Octane::concurrently([
                fn () => Brief::where('class_group_id', $id_class)->count() ?? 0,
                fn () => Submission::where('student_id', $student_id)->count() ?? 0,
                fn () => Correction::where('student_id', $student_id)->where('result', 'Valid')->count() ?? 0,
            ]);

            $totalBelly = (int)$validCorrectionsCount * 1000;

            // Skills Progress
            $skillsProgress = Correction::where('student_id', $student_id)
                ->with(['correction_details.brief_skill_level.skill', 'correction_details.brief_skill_level.level'])
                ->get()
                ->pluck('correction_details')
                ->flatten()
                ->groupBy('brief_skill_level.skill_id')
                ->map(function ($details) {
                    return $details->sortByDesc(function ($detail) {
                        $grades = ['POOR' => 1, 'AVERAGE' => 2, 'EXCELLENT' => 3];
                        return $grades[$detail->grade] ?? 0;
                    })->first();
                })
                ->values();

            // Recent Activity
            $recentSubmissions = Submission::where('student_id', $student_id)
                ->with('brief')
                ->latest()
                ->limit(5)
                ->get()
                ->map(function($s) {
                    return [
                        'id' => 'sub-'.$s->id,
                        'type' => 'submission',
                        'title' => $s->brief->title ?? 'Untitled Mission',
                        'date' => $s->created_at,
                        'status' => 'Submitted',
                        'icon' => 'upload_file'
                    ];
                });

            $recentCorrections = Correction::where('student_id', $student_id)
                ->with('brief')
                ->latest()
                ->limit(5)
                ->get()
                ->map(function($c) {
                    return [
                        'id' => 'cor-'.$c->id,
                        'type' => 'correction',
                        'title' => $c->brief->title ?? 'Untitled Mission',
                        'date' => $c->created_at,
                        'status' => $c->result,
                        'icon' => $c->result === 'Valid' ? 'verified' : 'cancel'
                    ];
                });

            $recentActivity = $recentSubmissions->concat($recentCorrections)
                ->sortByDesc('date')
                ->values()
                ->take(8);

            $data = [
                'stats' => [
                    'total_missions' => (int)$totalMissions,
                    'completed_missions' => (int)$completedMissions,
                    'total_belly' => (int)$totalBelly,
                    'rank' => $this->calculateRank($validCorrectionsCount),
                    'success_rate' => $totalMissions > 0 ? (int)round(($completedMissions / $totalMissions) * 100) : 0
                ],
                'skills_progress' => $skillsProgress,
                'recent_activity' => $recentActivity
            ];

            return response()->json([
                "success" => true,
                "data" => (object)$data,
                "message" => "Successfully fetched student dashboard data."
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

    private function calculateRank($points)
    {
        if ($points >= 10) return 'Pirate King';
        if ($points >= 7) return 'Admiral';
        if ($points >= 5) return 'Captain';
        if ($points >= 3) return 'First Mate';
        return 'Cabin Boy';
    }
}
