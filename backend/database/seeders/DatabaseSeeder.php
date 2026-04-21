<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ScholarYearSeeder::class,
            GradeLevelSeeder::class,
            FormationSeeder::class,
            ClassGroupSeeder::class,
            UserSeeder::class,
            ClassTeacherSeeder::class,
            LevelSeeder::class,
            SkillSeeder::class,
            SkillLevelSeeder::class,
            SprintSeeder::class,
            SprintSkillSeeder::class,
            BriefSeeder::class,
            BriefSkillLevelSeeder::class,
            LanguageSeeder::class,
        ]);

        /* // 1️⃣ Academic structure
        $this->call([
            ScholarYearSeeder::class,
            GradeLevelSeeder::class,
            FormationSeeder::class,
            ClassGroupSeeder::class,
        ]);

        // 2️⃣ Users
        $this->call([
            UserSeeder::class,
            ClassTeacherSeeder::class,
        ]);

        // 3️⃣ Skills & levels
        $this->call([
            LevelSeeder::class,
            SkillSeeder::class,
            SkillLevelSeeder::class,
        ]);

        // 4️⃣ Sprints & squads
        $this->call([
            SprintSeeder::class,
            SprintSkillSeeder::class,
            SquadSeeder::class,
            SquadMemberSeeder::class,
        ]);

        // 5️⃣ Briefs & corrections
        $this->call([
            BriefSeeder::class,
            BriefSkillLevelSeeder::class,
            SubmissionSeeder::class,
            CorrectionSeeder::class,
            CorrectionDetailSeeder::class,
        ]); */
    }
}
