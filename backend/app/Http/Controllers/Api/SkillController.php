<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SkillRequest;
use App\Models\Skill;
use App\Models\SkillLevel;
use Illuminate\Http\Request;
use Laravel\Octane\Facades\Octane;

class SkillController extends Controller
{
    public function index()
    {
        try {
            $per_page = request()->get("per_page", 5);

            [$skills, $archived_skills] = Octane::concurrently([
                fn() => Skill::withoutTrashed()->with(['formation', "skill_levels.level"])->orderByDesc('created_at')->paginate($per_page),
                fn() => Skill::onlyTrashed()->with(['formation', "skill_levels.level"])->orderByDesc('created_at')->paginate($per_page),
            ]);

            return response()->json([
                "success" => true,
                "data" => compact("skills", "archived_skills"),
                "message" => "Successfully fetched skills."
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

    public function get_by_formaction_id(Request $request, int $id)
    {
        try {
            $skills = Skill::withoutTrashed()
                ->where("formation_id", $id)
                ->with(['formation', "skill_levels.level"])
                ->orderByDesc("created_at")
                ->get();

            return response()->json([
                "success"=> true,
                "data" => compact("skills"),
                "message" => "Successfully fetched skills."
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
            $skill = Skill::with(['formation', "skill_levels.level"])->withoutTrashed()->find($id);

            if (!$skill) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Skill not found."
                ], 404);
            }

            return response()->json([
                "success"=> true,
                "data" => compact("skill"),
                "message" => "Successfully fetched skill."
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

    public function store(SkillRequest $request)
    {
        try {
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

    public function update(SkillRequest $request, int $id)
    {
        try {
            $skill = Skill::withoutTrashed()->find($id);

            if (!$skill) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Skill not found."
                ], 404);
            }

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
            $skill = Skill::withoutTrashed()->find($id);

            if (!$skill) {
                return response()->json([
                    "success" => false,
                    "message" => "Skill not found."
                ], 404);
            }

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
            $skill = Skill::withTrashed()->find($id);

            if (!$skill) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Skill not found."
                ], 404);
            }

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
