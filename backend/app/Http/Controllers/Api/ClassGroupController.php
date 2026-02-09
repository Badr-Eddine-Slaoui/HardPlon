<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassGroupRequest;
use App\Models\ClassGroup;
use App\Models\ClassTeacher;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassGroupController extends Controller
{
    public function index()
    {
        $class_groups = ClassGroup::withTrashed()->with(['formation', 'main_teacher.teacher'])->orderByDesc('created_at')->get();
        $archived_class_groups = ClassGroup::onlyTrashed()->get();

        return response()->json([
            "success" => true,
            "data" => compact("class_groups", "archived_class_groups")
        ], 200);
    }

    public function get_teacher_class_groups(){
        $class_groups = ClassGroup::withoutTrashed()
            ->with(["main_teacher.teacher", "sub_teacher.teacher"])
            ->whereHas('main_teacher', function ($query) {
                $query->where('teacher_id', Auth::id());
            })
            ->orWhereHas('sub_teacher', function ($query) {
                $query->where('teacher_id', Auth::id());
            })
            ->get();

        return response()->json([
            "success" => true,
            "data" => compact("class_groups")
        ], 200);
    }

    public function show(Request $request, ClassGroup $class_group){

        $class_group->loadMissing(['formation', 'students', 'main_teacher.teacher', 'sub_teacher.teacher']);

        return response()->json([
            "success" => true,
            "data" => compact("class_group")
        ], 200);
    }

    public function store(ClassGroupRequest $request)
    {
        $class_group = ClassGroup::create([
            "formation_id" => $request->formation_id,
            "name" => $request->name,
            "image_url" => $request->image_url,
            "capacity" => $request->capacity,
            "description" => $request->description
        ]);

        if ($class_group) {

            $main_teacher = ClassTeacher::create([
                "class_group_id" => $class_group->id,
                "teacher_id" => $request->main_teacher_id,
                "role" => "MAIN"
            ]);

            $sub_teacher = ClassTeacher::create([
                "class_group_id" => $class_group->id,
                "teacher_id" => $request->sub_teacher_id,
                "role" => "SUB"
            ]);

            $is_updated = Student::whereIn("id", $request->students_ids)->update(["id_class" => $class_group->id]);
            if ($is_updated && $sub_teacher && $main_teacher) {
                return response()->json([
                    "success" => true,
                    "data" => compact("class_group"),
                    "message" => "Successfully created class group"
                ], 200);
            }
        }

        return response()->json([
            "success" => false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function update(ClassGroupRequest $request, ClassGroup $class_group)
    {
        $is_updated = $class_group->update([
            "formation_id" => $request->formation_id,
            "name" => $request->name,
            "image_url" => $request->image_url,
            "capacity" => $request->capacity,
            "description" => $request->description
        ]);

        if ($is_updated) {

            $class_group->main_teacher()->update(["teacher_id" => $request->main_teacher_id]);
            $class_group->sub_teacher()->update(["teacher_id" => $request->sub_teacher_id]);
            $class_group->students()->whereNotIn("id", $request->students_ids)->update(["id_class" => null]);
            Student::whereIn("id", $request->students_ids)->update(["id_class" => $class_group->id]);

            return response()->json([
                "success" => true,
                "data" => compact("class_group"),
                "message" => "Successfully updated class group"
            ], 200);
        }

        return response()->json([
            "success"=> false,
            "data"=> compact("is_updated", "is_main_updated", "is_sub_updated", "remove_old_students", "is_students_updated"),
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function destroy(ClassGroup $class_group)
    {
        $is_deleted = $class_group->delete();

        if ($is_deleted) {
            return response()->json([
                "success" => true,
                "data" => compact("class_group"),
                "message" => "Successfully deleted class group"
            ], 200);
        }

        return response()->json([
            "success"=> false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function restore(int $id)
    {
        $class_group = ClassGroup::withTrashed()->find($id);
        $is_restored = $class_group->restore();

        if ($is_restored) {
            return response()->json([
                "success" => true,
                "data" => compact("class_group"),
                "message" => "Successfully restored class group"
            ], 200);
        }

        return response()->json([
            "success"=> false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }
}
