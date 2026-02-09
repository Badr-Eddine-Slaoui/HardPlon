<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BriefRequest;
use App\Models\Brief;
use App\Models\BriefSkillLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BriefController extends Controller
{
    public function index()
    {
        $briefs = Brief::withTrashed()->with(['sprint'])->orderByDesc('created_at')->get();
        $archived_briefs = Brief::onlyTrashed()->get();

        return response()->json([
            "success" => true,
            "data" => compact("briefs", "archived_briefs")
        ], 200);
    }

    public function get_student_briefs(){
        $briefs = Brief::withoutTrashed()
            ->with(["sprint", "class_group", "teacher", "brief_skill_levels.skill", "brief_skill_levels.level"])
            ->where("class_group_id", Auth::user()->id_class)
            ->orderByDesc("created_at")
            ->get();
        return response()->json([
            "success" => true,
            "data" => compact("briefs")
        ], 200);
    }

    public function show(Request $request, Brief $brief)
    {
        $brief->loadMissing(["sprint", "class_group.formation", "teacher", "brief_skill_levels.skill", "brief_skill_levels.level"]);
        return response()->json([
            "success"=> true,
            "data" => compact("brief")
        ], 200);
    }

    public function store(BriefRequest $request)
    {
        $brief = Brief::create([
            "sprint_id" => $request->sprint_id,
            "class_group_id" => $request->class_group_id,
            "teacher_id" => Auth::id(),
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
        ], 500);
    }

    public function update(BriefRequest $request, Brief $brief)
    {
        $is_updated = $brief->update([
            "sprint_id" => $request->sprint_id,
            "class_group_id" => $request->class_group_id,
            "teacher_id" => Auth::id(),
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
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function destroy(Brief $brief)
    {
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
            "message" => "Something went wrong. Please try again."
        ], 500);
    }

    public function restore(int $id)
    {
        $brief = Brief::withTrashed()->find($id);
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
            "message" => "Something went wrong. Please try again."
        ], 500);
    }
}
