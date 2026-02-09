<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FormationRequest;
use App\Models\Formation;
use Illuminate\Http\Request;

class FormationController extends Controller
{
    public function index()
    {
        $formations = Formation::withTrashed()->with(['class_groups'])->orderByDesc('created_at')->get();
        $archived_formations = Formation::onlyTrashed()->get();

        return response()->json([
            "success" => true,
            "data" => compact("formations", "archived_formations")
        ], 200);
    }

    public function show(Request $request, Formation $formation){

        $formation->loadMissing(['class_groups', 'class_groups.main_teacher.teacher']);

        return response()->json([
            "success" => true,
            "data" => compact("formation")
        ], 200);
    }

    public function store(FormationRequest $request)
    {
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
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function update(FormationRequest $request, Formation $formation)
    {
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
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function destroy(Formation $formation)
    {
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
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function restore(int $id)
    {
        $formation = Formation::withTrashed()->find($id);
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
            "message" => "Something went wrong. Please try again."
        ], 500);
    }
}
