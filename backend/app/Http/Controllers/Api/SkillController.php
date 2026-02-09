<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SkillRequest;
use App\Models\Skill;
use App\Models\SkillLevel;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index()
    {
        $skills = Skill::withTrashed()->with(['formation', "skill_levels.level"])->orderByDesc('created_at')->get();
        $archived_skills = Skill::onlyTrashed()->get();

        return response()->json([
            "success" => true,
            "data" => compact("skills", "archived_skills")
        ], 200);
    }

    public function get_by_formaction_id(Request $request, int $id)
    {
        $skills = Skill::withoutTrashed()
            ->where("formation_id", $id)
            ->with(['formation', "skill_levels.level"])
            ->orderByDesc("created_at")
            ->get();

        return response()->json([
            "success"=> true,
            "data" => compact("skills")
        ], 200);
    }

    public function show(Request $request, Skill $skill)
    {
        $skill->loadMissing(["formation", "skill_levels.level"]);
        return response()->json([
            "success"=> true,
            "data" => compact("skill")
        ], 200);
    }

    public function store(SkillRequest $request)
    {
        $skill = Skill::create([
            "formation_id" => $request->formation_id,
            "code" => $request->code,
            "title" => $request->title,
            "description" => $request->description
        ]);

        if ($skill) {

            foreach ($request->skill_levels as $item) {
                SkillLevel::create([
                    'skill_id' => $skill->id,
                    'level_id' => $item['level_id'],
                    'criteria' => $item['criteria'],
                ]);
            }

            return response()->json([
                "success" => true,
                "data" => compact("skill"),
                "message" => "Successfully created skill."
            ], 200);
        }

        return response()->json([
            "success" => false,
            "data" => $request->all(),
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function update(SkillRequest $request, Skill $skill)
    {
        $is_updated = $skill->update([
            "formation_id" => $request->formation_id,
            "code" => $request->code,
            "title" => $request->title,
            "description" => $request->description
        ]);

        if ($is_updated) {
            $skill->skill_levels()->whereNotIn("level_id", array_map(fn($item) => $item["level_id"], $request->skill_levels))->delete();
            foreach ($request->skill_levels as $item) {
                SkillLevel::updateOrCreate([
                    "skill_id" => $skill->id,
                    "level_id" => $item["level_id"],
                ], [
                    "skill_id" => $skill->id,
                    "level_id" => $item["level_id"],
                    "criteria" => $item["criteria"],
                ]);
            }

            return response()->json([
                "success" => true,
                "data" => compact("skill"),
                "message" => "Successfully updated skill."
            ], 200);
        }

        return response()->json([
            "success"=> false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function destroy(Skill $skill)
    {
        $is_deleted = $skill->delete();

        if ($is_deleted) {
            return response()->json([
                "success" => true,
                "data" => compact("skill"),
                "message" => "Successfully deleted skill."
            ], 200);
        }

        return response()->json([
            "success"=> false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function restore(int $id)
    {
        $skill = Skill::withTrashed()->find($id);
        $is_restored = $skill->restore();

        if ($is_restored) {
            return response()->json([
                "success" => true,
                "data" => compact("skill"),
                "message" => "Successfully restored skill."
            ], 200);
        }

        return response()->json([
            "success"=> false,
            "message" => "Something went wrong. Please try again."
        ], 500);
    }
}
