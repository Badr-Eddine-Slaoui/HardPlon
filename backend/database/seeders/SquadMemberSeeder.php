<?php

namespace Database\Seeders;

use App\Models\Squad;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SquadMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $squads = Squad::all();

        foreach ($squads as $squad) {
            $squadMembers = Student::inRandomOrder()->take(5)->get();
            foreach($squadMembers as $squadMember) {
                DB::table('squad_members')->insert([
                    "squad_id" => $squad->id,
                    "student_id" => $squadMember->id,
                ]);
            }
        }
    }
}
