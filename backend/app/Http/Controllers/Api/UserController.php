<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Admin;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withTrashed()->orderByDesc('created_at')->get();
        $archived_users = User::onlyTrashed()->get();

        return response()->json([
            "success" => true,
            "data" => compact("users", "archived_users")
        ], 200);
    }

    public function show(Request $request, string $role, int $id){

        $user = match(strtoupper($role)) {
            "STUDENT" => Student::with(['class_group', 'class_group.main_teacher.teacher', 'class_group.sub_teacher.teacher', 'class_group.formation.grade_level', ])->findOrFail($id),
            "TEACHER" => Teacher::with(['class_teachers.class_group.main_teacher.teacher', 'class_teachers.class_group.sub_teacher.teacher', 'class_teachers.class_group.formation.grade_level' ])->findOrFail($id),
            "ADMIN" => Admin::findOrFail($id)
        };

        return response()->json([
            "success" => true,
            "data" => compact("user")
        ], 200);
    }

    public function get_main_teachers(Request $request, ?int $class_group = null){
        $query = Teacher::query();

        $query->select("users.*")->distinct()->leftJoin("class_teachers", "class_teachers.teacher_id", "=", "users.id")
            ->where("class_teachers.role", "=", "SUB")->orWhereNull("class_teachers.teacher_id");

        if($class_group){
            $query->orWhere("class_teachers.class_group_id", "=", $class_group);
        }

        $users = $query->get();
        return response()->json([
            "success" => true,
            "data" => compact("users")
        ], 200);
    }

    public function get_sub_teachers(Request $request, ?int $class_group = null){
        $query = Teacher::query();

        $query->select("users.*")->distinct()->leftJoin("class_teachers", "class_teachers.teacher_id", "=", "users.id")
            ->where("class_teachers.role", "=", "MAIN")->orWhereNull("class_teachers.teacher_id");

        if($class_group){
            $query->orWhere("class_teachers.class_group_id", "=", $class_group);
        }

        $users = $query->get();
        return response()->json([
            "success" => true,
            "data" => compact("users")
        ], 200);
    }

    public function get_students(Request $request, ?int $class_group = null){

        $query = Student::query();

        $query->withoutTrashed()->distinct()->whereNull("id_class")->orderByDesc('created_at');

        if($class_group){
            $query->OrWhere("id_class", "=", $class_group);
        }

        $users = $query->get();

        return response()->json([
            "success" => true,
            "data" => compact("users")
        ], 200);
    }

    public function get_class_group_students(int $id){
        $users = Student::where("id_class", $id)->get();
        return response()->json([
            "success" => true,
            "data" => compact("users")
        ], 200);
    }

    public function store(UserRequest $request)
    {
        $user = User::create([
            "first_name" => $request->first_name,
            "last_name" => $request->last_name,
            "age" => $request->age,
            "email" => $request->email,
            "cin" => $request->cin,
            "phone" => $request->phone,
            "password" => Hash::make($request->password ?? "password"),
            "role" => $request->role
        ]);

        if ($user) {
            return response()->json([
                "success" => true,
                "data" => compact("user"),
                "message" => "Successfully created user"
            ], 200);
        }

        return response()->json([
            "success" => false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function update(UserRequest $request, User $user)
    {
        $is_updated = $user->update([
            "first_name" => $request->first_name,
            "last_name" => $request->last_name,
            "age" => $request->age,
            "email" => $request->email,
            "cin" => $request->cin,
            "phone" => $request->phone,
            "role" => $request->role
        ]);

        if ($is_updated) {
            return response()->json([
                "success" => true,
                "data" => compact("user"),
                "message" => "Successfully updated user"
            ], 200);
        }

        return response()->json([
            "success"=> false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function destroy(User $user)
    {
        $is_deleted = $user->delete();

        if ($is_deleted) {
            return response()->json([
                "success" => true,
                "data" => compact("user"),
                "message" => "Successfully deleted user"
            ], 200);
        }

        return response()->json([
            "success"=> false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function restore(int $id)
    {
        $user = User::withTrashed()->find($id);
        $is_restored = $user->restore();

        if ($is_restored) {
            return response()->json([
                "success" => true,
                "data" => compact("user"),
                "message" => "Successfully restored user"
            ], 200);
        }

        return response()->json([
            "success"=> false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }
}
