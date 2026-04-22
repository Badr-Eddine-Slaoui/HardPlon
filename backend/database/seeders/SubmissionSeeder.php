<?php

namespace Database\Seeders;

use App\Models\Submission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Submission::create([
            "brief_id" => 1,
            "student_id" => 3,
            "squad_id" => null,
            "message" => "My first submission",
            "link" => "https://www.github.com/Badr-Eddine-Slaoui/EcoShopAPI.git",
            "created_at" => now(),
            "updated_at" => now()
        ]);
    }
}
