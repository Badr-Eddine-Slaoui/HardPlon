<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SprintRequest;
use App\Models\Sprint;
use App\Models\SprintSkill;
use Illuminate\Http\Request;

class SprintController extends Controller
{
    public function index()
    {
        $sprints = Sprint::withTrashed()->with(['formation', "sprint_skills.skill"])->orderByDesc('created_at')->get();
        $archived_sprints = Sprint::onlyTrashed()->get();

        return response()->json([
            "success" => true,
            "data" => compact("sprints", "archived_sprints")
        ], 200);
    }

    public function get_sprints_by_formation_id(int $id){
        $sprints = Sprint::withoutTrashed()
            ->with(["formation", "sprint_skills.skill"])
            ->where("formation_id", $id)
            ->orderByDesc("created_at")
            ->get();

        return response()->json([
            "success" => true,
            "data" => compact("sprints")
        ], 200);
    }

    public function get_sprint_skills(int $id){
        $skills = SprintSkill::withoutTrashed()
            ->with(["skill","skill.skill_levels.level"])
            ->where("sprint_id", $id)
            ->orderByDesc("created_at")
            ->get();

        return response()->json([
            "success"=> true,
            "data" => compact("skills")
        ], 200);
    }

    public function show(Request $request, Sprint $sprint)
    {
        $sprint->loadMissing(["formation", "sprint_skills.skill"]);
        return response()->json([
            "success"=> true,
            "data" => compact("sprint")
        ], 200);
    }

    public function store(SprintRequest $request)
    {
        $sprint = Sprint::create([
            "formation_id" => $request->formation_id,
            "name" => $request->name,
            "description" => $request->description,
            "start_date" => $request->start_date,
            "end_date"=> $request->end_date
        ]);

        if ($sprint) {

            foreach ($request->skills as $id) {
                SprintSkill::create([
                    'sprint_id' => $sprint->id,
                    'skill_id' => $id
                ]);
            }

            return response()->json([
                "success" => true,
                "data" => compact("sprint"),
                "message" => "Successfully created sprint."
            ], 200);
        }

        return response()->json([
            "success" => false,
            "data" => $request->all(),
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function update(SprintRequest $request, Sprint $sprint)
    {
        $is_updated = $sprint->update([
            "formation_id" => $request->formation_id,
            "name" => $request->name,
            "description" => $request->description,
            "start_date" => $request->start_date,
            "end_date"=> $request->end_date
        ]);

        if ($is_updated) {
            $sprint->sprint_skills()->whereNotIn("skill_id", $request->skills)->delete();
            foreach ($request->skills as $id) {
                SprintSkill::updateOrCreate([
                    "sprint_id" => $sprint->id,
                    "skill_id" => $id
                ], [
                    "sprint_id" => $sprint->id,
                    "skill_id" => $id
                ]);
            }

            return response()->json([
                "success" => true,
                "data" => compact("sprint"),
                "message" => "Successfully updated sprint."
            ], 200);
        }

        return response()->json([
            "success"=> false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function destroy(Sprint $sprint)
    {
        $is_deleted = $sprint->delete();

        if ($is_deleted) {
            return response()->json([
                "success" => true,
                "data" => compact("sprint"),
                "message" => "Successfully deleted sprint."
            ], 200);
        }

        return response()->json([
            "success"=> false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function restore(int $id)
    {
        $sprint = Sprint::withTrashed()->find($id);
        $is_restored = $sprint->restore();

        if ($is_restored) {
            return response()->json([
                "success" => true,
                "data" => compact("sprint"),
                "message" => "Successfully restored sprint."
            ], 200);
        }

        return response()->json([
            "success"=> false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }
}
