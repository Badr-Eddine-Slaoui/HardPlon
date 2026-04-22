<?php

namespace Database\Seeders;

use App\Models\ProblemSubmission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProblemSubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProblemSubmission::create([
            'problem_id' => 1,
            'submission_id' => 1,
            'code' => 'function add(a, b) { return a+b; }',
            "created_at" => now(),
            "updated_at" => now()
        ]);

        ProblemSubmission::create([
            'problem_id' => 2,
            'submission_id' => 1,
            'code' => 'function sub(a, b) { return a-b; }',
            "created_at" => now(),
            "updated_at" => now()
        ]);
    }
}
