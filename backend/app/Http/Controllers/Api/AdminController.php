<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brief;
use App\Models\ClassGroup;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(){
        $students_count = Student::count();
        $teachers_count = Teacher::count();
        $class_groups_count = ClassGroup::where('deleted_at', null)->count();
        $briefs_count = Brief::where('deleted_at', null)->count();

        return response()->json([
            "statistics" => compact("students_count","teachers_count", "class_groups_count", "briefs_count"),
        ]);
    }
}
