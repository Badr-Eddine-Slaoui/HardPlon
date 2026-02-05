<?php

namespace Database\Seeders;

use App\Models\ClassGroup;
use App\Models\Teacher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassTeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classGroups = ClassGroup::all();

        foreach ($classGroups as $classGroup) {
            DB::table('class_teachers')->insert([
                'class_group_id' => $classGroup->id,
                'teacher_id' => Teacher::inRandomOrder()->value('id'),
                'role' => "MAIN",
                'assigned_at' => now(),
            ]);

            DB::table('class_teachers')->insert([
                'class_group_id' => $classGroup->id,
                'teacher_id' => Teacher::inRandomOrder()->value('id'),
                'role' => "SUB",
                'assigned_at' => now(),
            ]);
        }
    }
}
