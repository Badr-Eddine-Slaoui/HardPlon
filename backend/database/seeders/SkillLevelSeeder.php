<?php

namespace Database\Seeders;

use App\Models\Level;
use App\Models\Skill;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class SkillLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $skills = Skill::all();
        $levels = Level::all();

        foreach ($skills as $skill) {
            foreach ($levels as $level) {
                DB::table('skill_levels')->insert([
                    'skill_id' => $skill->id,
                    'level_id' => $level->id,
                    'criteria' => $faker->sentence(),
                ]);
            }
        }
    }
}
