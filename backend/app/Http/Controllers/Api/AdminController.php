<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brief;
use App\Models\ClassGroup;
use App\Models\Student;
use App\Models\Teacher;
use Laravel\Octane\Facades\Octane;

class AdminController extends Controller
{
    public function index(){
        try {

            [$students_count, $teachers_count, $class_groups_count, $briefs_count] = Octane::concurrently([
                fn () => Student::where('deleted_at', null)->count() ?? 0,
                fn () => Teacher::where('deleted_at', null)->count() ?? 0,
                fn () => ClassGroup::where('deleted_at', null)->count() ?? 0,
                fn () => Brief::where('deleted_at', null)->count() ?? 0,
            ]);

            return response()->json([
                "success" => true,
                "data" => compact('students_count', 'teachers_count', 'class_groups_count', 'briefs_count'),
                "message" => "Successfully fetched admin dashboard data."
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
