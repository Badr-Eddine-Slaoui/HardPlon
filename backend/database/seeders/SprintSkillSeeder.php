<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\Sprint;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SprintSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sprints = Sprint::all();

        foreach ($sprints as $sprint) {
            $skills = Skill::inRandomOrder()->take(5)->get();
            foreach($skills as $skill) {
                DB::table('sprint_skills')->insert([
                    "sprint_id" => $sprint->id,
                    "skill_id" => $skill->id,
                ]);
            }
        }
    }
}
