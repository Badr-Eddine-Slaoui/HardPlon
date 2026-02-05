<?php

namespace Database\Seeders;

use App\Models\Brief;
use App\Models\Level;
use App\Models\Skill;
use App\Models\Sprint;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BriefSkillLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $briefs = Brief::all();

        foreach ($briefs as $brief) {
            $sprint = Sprint::where("id", $brief->sprint_id)->first();
            $skills = Skill::join("sprint_skills", "skills.id", "=", "sprint_skills.skill_id")->where("sprint_skills.sprint_id", "=", $sprint->id)->get();
            foreach ($skills as $skill) {
                DB::table('brief_skill_levels')->insert([
                    "brief_id" => $brief->id,
                    "skill_id" => $skill->id,
                    "level_id" => Level::join("skill_levels", "levels.id", "=", "skill_levels.level_id")->where("skill_levels.skill_id", "=", $skill->id)->inRandomOrder()->value("levels.id"),
                ]);
            }
        }
    }
}
