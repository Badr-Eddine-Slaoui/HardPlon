<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FormationRequest;
use App\Models\Formation;
use Illuminate\Http\Request;
use Laravel\Octane\Facades\Octane;

class FormationController extends Controller
{
    public function index()
    {
        try {
            $per_page = request()->get("per_page", 5);

            [$formations, $archived_formations] = Octane::concurrently([
                fn() => Formation::withoutTrashed()->with(['class_groups'])->orderByDesc('created_at')->paginate($per_page),
                fn() => Formation::onlyTrashed()->with(['class_groups'])->orderByDesc('created_at')->paginate($per_page)
            ]);

            return response()->json([
                "success" => true,
                "data" => compact("formations", "archived_formations"),
                "message" => "Successfully fetched formations."
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

    public function show(Request $request, int $id){

        try {
            $formation = Formation::with(['class_groups', 'class_groups.main_teacher.teacher'])->find($id);

            if (!$formation) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Formation not found."
                ], 404);
            }

            return response()->json([
                "success" => true,
                "data" => compact("formation"),
                "message" => "Successfully fetched formation."
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

    public function store(FormationRequest $request)
    {
        try {
            $formation = Formation::create([
                "grade_level_id" => $request->grade_level_id,
                "title" => $request->title,
                "description" => $request->description,
                "duration" => $request->duration,
                "capacity" => $request->capacity
            ]);

            if ($formation) {
                return response()->json([
                    "success" => true,
                    "data" => compact("formation"),
                    "message" => "Successfully created formation."
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

    public function update(FormationRequest $request, int $id)
    {
        try {
            $formation = Formation::find($id);

            if (!$formation) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Formation not found."
                ], 404);
            }

            $is_updated = $formation->update([
                "grade_level_id" => $request->grade_level_id,
                "title" => $request->title,
                "description" => $request->description,
                "duration" => $request->duration,
                "capacity" => $request->capacity
            ]);

            if ($is_updated) {
                return response()->json([
                    "success" => true,
                    "data" => compact("formation"),
                    "message" => "Successfully updated formation."
                ], 200);
            }

            return response()->json([
                "success"=> false,
                "data"=> null,
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

    public function destroy(int $id)
    {
        try {
            $formation = Formation::find($id);

            if (!$formation) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Formation not found."
                ], 404);
            }

            $is_deleted = $formation->delete();

            if ($is_deleted) {
                return response()->json([
                    "success" => true,
                    "data" => compact("formation"),
                    "message" => "Successfully deleted formation."
                ], 200);
            }

            return response()->json([
                "success"=> false,
                "data"=> null,
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
            $formation = Formation::onlyTrashed()->find($id);

            if (!$formation) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Formation not found."
                ], 404);
            }

            $is_restored = $formation->restore();

            if ($is_restored) {
                return response()->json([
                    "success" => true,
                    "data" => compact("formation"),
                    "message" => "Successfully restored formation."
                ], 200);
            }

            return response()->json([
                "success"=> false,
                "data"=> null,
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
