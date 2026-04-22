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
use Laravel\Octane\Facades\Octane;

class UserController extends Controller
{
    public function index()
    {
        try {
            $perPage = request()->get('per_page', 5);

            [$users, $archived_users] = Octane::concurrently([
                fn() => User::withoutTrashed()->orderByDesc('created_at')->paginate($perPage),
                fn() => User::onlyTrashed()->orderByDesc('created_at')->paginate($perPage),
            ]);

            return response()->json([
                "success" => true,
                "data" => compact("users", "archived_users"),
                "message" => "Successfully fetched users."
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

    public function show(Request $request, string $role, int $id){

        try {
            $user = match(strtoupper($role)) {
                "STUDENT" => Student::with(['class_group', 'class_group.main_teacher.teacher', 'class_group.sub_teacher.teacher', 'class_group.formation.grade_level', ])->find($id),
                "TEACHER" => Teacher::with(['class_teachers.class_group.main_teacher.teacher', 'class_teachers.class_group.sub_teacher.teacher', 'class_teachers.class_group.formation.grade_level' ])->find($id),
                "ADMIN" => Admin::find($id)
            };

            if (!$user) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "User not found."
                ], 404);
            }

            return response()->json([
                "success" => true,
                "data" => compact("user"),
                "message" => "Successfully fetched user."
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

    public function get_main_teachers(Request $request, ?int $class_group = null){

        try {
            $query = Teacher::query();

            $query->select("users.*")->distinct()->leftJoin("class_teachers", "class_teachers.teacher_id", "=", "users.id")
                ->where("class_teachers.role", "=", "SUB")->orWhereNull("class_teachers.teacher_id");

            if($class_group){
                $query->orWhere("class_teachers.class_group_id", "=", $class_group);
            }

            $users = $query->get();

            return response()->json([
                "success" => true,
                "data" => compact("users"),
                "message" => "Successfully fetched teachers."
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

    public function get_sub_teachers(Request $request, ?int $class_group = null){
        try {
            $query = Teacher::query();

            $query->select("users.*")->distinct()->leftJoin("class_teachers", "class_teachers.teacher_id", "=", "users.id")
                ->where("class_teachers.role", "=", "MAIN")->orWhereNull("class_teachers.teacher_id");

            if($class_group){
                $query->orWhere("class_teachers.class_group_id", "=", $class_group);
            }

            $users = $query->get();

            return response()->json([
                "success" => true,
                "data" => compact("users"),
                "message" => "Successfully fetched teachers."
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

    public function get_students(Request $request, ?int $class_group = null){

        try {
            $query = Student::query();

            $query->withoutTrashed()->distinct()->whereNull("id_class")->orderByDesc('created_at');

            if($class_group){
                $query->OrWhere("id_class", "=", $class_group);
            }

            $users = $query->get();

            return response()->json([
                "success" => true,
                "data" => compact("users"),
                "message" => "Successfully fetched students."
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

    public function get_class_group_students(int $id){
        try {
            $users = Student::where("id_class", $id)->get();

            return response()->json([
                "success" => true,
                "data" => compact("users"),
                "message" => "Successfully fetched students."
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "Something went wrong. Please try again.",
                "code" => $e->getCode()
            ]);
        }
    }

    public function store(UserRequest $request)
    {
        try {
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

    public function update(UserRequest $request, int $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "User not found."
                ], 404);
            }

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
                "data" => null,
                "message" => "Something went wrong. Please try again."
            ], 400);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "Something went wrong. Please try again.",
                "code" => $e->getCode(),
            ], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $user = User::withoutTrashed()->find($id);

            if (!$user) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "User not found."
                ], 404);
            }

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
            $user = User::onlyTrashed()->find($id);

            if (!$user) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "User not found."
                ], 404);
            }

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
}
