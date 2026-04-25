<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BriefRequest;
use App\Models\Brief;
use App\Models\BriefSkillLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Octane\Facades\Octane;

class BriefController extends Controller
{
    public function index()
    {
        try {

            $per_page = request()->get("per_page", 5);

            [$briefs, $archived_briefs] = Octane::concurrently([
                fn() => Brief::withoutTrashed()->with(['sprint'])->orderByDesc('created_at')->paginate($per_page),
                fn() => Brief::onlyTrashed()->with(['sprint'])->orderByDesc('created_at')->paginate($per_page)
            ]);

            return response()->json([
                "success" => true,
                "data" => compact("briefs", "archived_briefs"),
                "message" => "Successfully fetched briefs."
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
    public function get_teacher_briefs()
    {
        try{
            $per_page = request()->get("per_page", 5);

            [$briefs, $archived_briefs] = Octane::concurrently([
                fn() => Brief::withoutTrashed()->with(['sprint', "class_group", "teacher", "brief_skill_levels.skill", "brief_skill_levels.level", "stack", "problems.language", "problems.test_cases"])->where('teacher_id', Auth::guard('api')->user()->id)->orderByDesc('created_at')->paginate($per_page),
                fn() => Brief::onlyTrashed()->with(['sprint', "class_group", "teacher", "brief_skill_levels.skill", "brief_skill_levels.level", "stack", "problems.language", "problems.test_cases"])->where('teacher_id', Auth::guard('api')->user()->id)->orderByDesc('created_at')->paginate($per_page)
            ]);

            return response()->json([
                "success" => true,
                "data" => compact("briefs", "archived_briefs"),
                "message" => "Successfully fetched briefs."
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

    public function get_student_briefs(){
        try {
            $briefs = Brief::withoutTrashed()
                ->with(["sprint", "class_group", "teacher", "brief_skill_levels.skill", "brief_skill_levels.level", "stack", "problems.language", "problems.test_cases"])
                ->where("class_group_id", Auth::user()->id_class)
                ->orderByDesc("created_at")
                ->get();
            return response()->json([
                "success" => true,
                "data" => compact("briefs"),
                "message" => "Successfully fetched briefs."
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

            $brief = Brief::withoutTrashed()
                ->with([
                    "sprint",
                    "teacher",
                    "class_group",
                    "brief_skill_levels.skill",
                    "brief_skill_levels.level",
                    "stack",
                    "problems.language",
                    "brief_versions" => fn ($query) => $query->latest(),
                ])
                ->find($id);

            if (!$brief) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Brief not found."
                ], 404);
            }

            return response()->json([
                "success"=> true,
                "data" => compact("brief"),
                "message" => "Successfully fetched brief."
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

    public function store(BriefRequest $request)
    {
        try {
            $brief = Brief::create([
                "sprint_id" => $request->sprint_id,
                "class_group_id" => $request->class_group_id,
                "stack_id" => $request->stack_id,
                "teacher_id" => Auth::guard('api')->id(),
                "title" => $request->title,
                "description" => $request->description,
                "content" => $request->input("content"),
                "is_collective" => $request->is_collective ?? false,
                "start_date" => $request->start_date,
                "end_date" => $request->end_date
            ]);

            if ($brief) {

                foreach ($request->skill_levels as $skill_level) {
                    BriefSkillLevel::create([
                        'brief_id' => $brief->id,
                        'skill_id' => $skill_level['skill_id'],
                        'level_id' => $skill_level['level_id']
                    ]);
                }

                return response()->json([
                    "success" => true,
                    "data" => compact("brief"),
                    "message" => "Successfully created brief."
                ], 200);
            }

            return response()->json([
                "success" => false,
                "data" => $request->all(),
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

    public function update(BriefRequest $request, int $id)
    {
        try {
            $brief = Brief::find($id);

            if (!$brief) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Brief not found."
                ], 404);
            }
            $is_updated = $brief->update([
                "sprint_id" => $request->sprint_id,
                "class_group_id" => $request->class_group_id,
                "stack_id" => $request->stack_id,
                "teacher_id" => Auth::guard('api')->id(),
                "title" => $request->title,
                "description" => $request->description,
                "content" => $request->input("content"),
                "is_collective" => $request->is_collective ?? false,
                "start_date" => $request->start_date,
                "end_date" => $request->end_date
            ]);

            if ($is_updated) {

                $brief->brief_skill_levels()
                    ->whereNotIn("skill_id", array_map(fn($item) => $item["skill_id"], $request->skill_levels))
                    ->orWhereNotIn("level_id", array_map(fn($item) => $item["level_id"], $request->skill_levels))
                    ->delete();

                foreach ($request->skill_levels as $skill_level) {
                    BriefSkillLevel::updateOrCreate([
                        "brief_id" => $brief->id,
                        'skill_id' => $skill_level['skill_id'],
                        'level_id' => $skill_level['level_id']
                    ], [
                        "brief_id" => $brief->id,
                        'skill_id' => $skill_level['skill_id'],
                        'level_id' => $skill_level['level_id']
                    ]);
                }

                return response()->json([
                    "success" => true,
                    "data" => compact("brief"),
                    "message" => "Successfully updated brief."
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
            $brief = Brief::withoutTrashed()->find($id);

            if (!$brief) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Brief not found."
                ], 404);
            }

            $is_deleted = $brief->delete();

            if ($is_deleted) {
                return response()->json([
                    "success" => true,
                    "data" => compact("brief"),
                    "message" => "Successfully deleted brief."
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
            $brief = Brief::onlyTrashed()->find($id);

            if (!$brief) {
                return response()->json([
                    "success" => false,
                    "data" => null,
                    "message" => "Brief not found."
                ], 404);
            }

            $is_restored = $brief->restore();

            if ($is_restored) {
                return response()->json([
                    "success" => true,
                    "data" => compact("brief"),
                    "message" => "Successfully restored brief."
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

    public function get_class_group_briefs(int $id){
        try {

            $briefs = Brief::withoutTrashed()
                ->with([
                    "sprint",
                    "teacher",
                    "class_group",
                    "brief_skill_levels.skill",
                    "brief_skill_levels.level",
                    "stack",
                    "problems.language",
                    "brief_versions"
                ])
                ->where("class_group_id", $id)
                ->orderByDesc("created_at")
                ->get();

            return response()->json([
                "success" => true,
                "data" => compact("briefs"),
                "message" => "Successfully fetched class group briefs."
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "message" => "Something went wrong. Please try again.",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
