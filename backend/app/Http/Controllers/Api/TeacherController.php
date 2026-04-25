<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brief;
use App\Models\ClassTeacher;
use App\Models\Correction;
use App\Models\Student;
use App\Models\Submission;
use App\Models\Sprint;
use Illuminate\Support\Facades\Auth;
use Laravel\Octane\Facades\Octane;
use Carbon\Carbon;

class TeacherController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $teacher_id = $user->id;

            $class_ids = ClassTeacher::where('teacher_id', $teacher_id)->pluck('class_group_id')->toArray();

            $brief_ids = Brief::where('teacher_id', $teacher_id)->pluck('id')->toArray();

            [$students_count, $average_haki, $weekly_metrics, $recent_logs, $upcoming_events] = Octane::concurrently([
                fn () => Student::whereIn('id_class', $class_ids)->count() ?? 0,
                fn () => $this->calculateHaki($teacher_id),
                fn () => $this->getWeeklyMetrics($teacher_id),
                fn () => $this->getRecentLogs($class_ids),
                fn () => $this->getUpcomingEvents(),
            ]);

            return response()->json([
                "success" => true,
                "data" => compact(
                    'students_count',
                    'average_haki',
                    'weekly_metrics',
                    'recent_logs',
                    'upcoming_events'
                ),
                "message" => "Successfully fetched teacher dashboard data."
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

    private function calculateHaki($teacher_id)
    {
        $total = Correction::where('teacher_id', $teacher_id)->count();
        if ($total == 0) return 0;

        $valid = Correction::where('teacher_id', $teacher_id)->where('result', 'Valid')->count();
        return round(($valid / $total) * 100, 1);
    }

    private function getWeeklyMetrics($teacher_id)
    {
        $metrics = [];
        for ($i = 4; $i >= 0; $i--) {
            $start = Carbon::now()->subWeeks($i)->startOfWeek();
            $end = Carbon::now()->subWeeks($i)->endOfWeek();

            $query = Correction::where('teacher_id', $teacher_id)
                ->whereBetween('created_at', [$start, $end]);

            $total = (clone $query)->count();
            $valid = (clone $query)->where('result', 'Valid')->count();

            $percentage = $total > 0 ? ($valid / $total) * 100 : 0;

            $metrics[] = [
                'label' => $i == 0 ? 'Live' : 'Wk ' . (5 - $i),
                'value' => round($percentage, 0)
            ];
        }
        return $metrics;
    }

    private function getRecentLogs($class_ids)
    {
        $submissions = Submission::with(['student', 'brief'])
            ->whereIn('student_id', function($query) use ($class_ids) {
                $query->select('id')->from('users')->whereIn('id_class', $class_ids);
            })
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($sub) {
                return [
                    'id' => $sub->id,
                    'type' => 'submission',
                    'title' => $sub->student->name . " submitted " . $sub->brief->name,
                    'description' => "Class: " . ($sub->student->class_group->name ?? 'N/A'),
                    'time' => $sub->created_at->diffForHumans(),
                    'icon' => 'flag',
                    'color' => 'orange'
                ];
            });

        return $submissions;
    }

    private function getUpcomingEvents()
    {
        return Sprint::where('end_date', '>=', Carbon::now())
            ->orderBy('end_date', 'asc')
            ->limit(3)
            ->get()
            ->map(function($sprint) {
                return [
                    'id' => $sprint->id,
                    'title' => $sprint->name,
                    'date' => Carbon::parse($sprint->end_date)->format('M d'),
                    'time' => '11:59 PM',
                    'type' => 'All Fleets',
                    'color' => Carbon::parse($sprint->end_date)->isToday() ? 'red' : 'gold'
                ];
            });
    }
}
