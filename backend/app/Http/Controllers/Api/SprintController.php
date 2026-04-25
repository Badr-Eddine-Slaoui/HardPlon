<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SprintRequest;
use App\Models\Sprint;
use App\Models\SprintSkill;
use Illuminate\Http\Request;
use Laravel\Octane\Facades\Octane;

class SprintController extends Controller
{
    public function index()
    {
        try {
            $per_page = request()->get("per_page", 5);

            [$sprints, $archived_sprints] = Octane::concurrently([
                fn() => Sprint::withoutTrashed()->with(['formation', "sprint_skills.skill"])->orderByDesc('created_at')->paginate($per_page),
                fn() => Sprint::onlyTrashed()->with(['formation', "sprint_skills.skill"])->orderByDesc('created_at')->paginate($per_page),
            ]);

            return response()->json([
                "success" => true,
                "data" => compact("sprints", "archived_sprints"),
                "message" => "Successfully fetched sprints."
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

    public function get_sprints_by_formation_id($id){
        $id = (int) $id;
        try {
            $sprints = Sprint::withoutTrashed()
                ->with(["formation", "sprint_skills.skill"])
                ->where("formation_id", $id)
                ->orderByDesc("created_at")
                ->get();

            return response()->json([
                "success" => true,
                "data" => compact("sprints"),
                "message" => "Successfully fetched sprints."
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

    public function get_sprint_skills($id){
        $id = (int) $id;
        try {
            $skills = SprintSkill::withoutTrashed()
                ->with(["skill","skill.skill_levels.level"])
                ->where("sprint_id", $id)
                ->orderByDesc("created_at")
                ->get();

            return response()->json([
                "success"=> true,
                "data" => compact("skills"),
                "message" => "Successfully fetched sprint skills."
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


    public function show(Request $request, int $id)
    {
        try {
            $sprint = Sprint::withTrashed()->with(["formation", "sprint_skills.skill"])->find($id);

            if (!$sprint) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Sprint not found."
                ], 404);
            }

            return response()->json([
                "success"=> true,
                "data" => compact("sprint"),
                "message" => "Successfully fetched sprint."
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

    public function store(SprintRequest $request)
    {
        try {
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

    public function update(SprintRequest $request, int $id)
    {
        try {
            $sprint = Sprint::find($id);

            if (!$sprint) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Sprint not found."
                ], 404);
            }

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

    public function destroy(int $id)
    {
        try {
            $sprint = Sprint::withoutTrashed()->find($id);

            if (!$sprint) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Sprint not found."
                ], 404);
            }
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
            $sprint = Sprint::onlyTrashed()->find($id);

            if (!$sprint) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Sprint not found."
                ], 404);
            }

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
