<?php

namespace Database\Seeders;

use App\Models\ProblemTestCase;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProblemTestCaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProblemTestCase::create([
            'problem_id' => 1,
            'input' => [1,2],
            'expected_output' => [3],
            'is_hidden' => false,
            "created_at" => now(),
            "updated_at" => now()
        ]);
        
        ProblemTestCase::create([
            'problem_id' => 1,
            'input' => [-1,-2],
            'expected_output' => [-3],
            'is_hidden' => false,
            "created_at" => now(),
            "updated_at" => now()
        ]);

        ProblemTestCase::create([
            'problem_id' => 2,
            'input' => [-1,-2],
            'expected_output' => [1],
            'is_hidden' => false,
            "created_at" => now(),
            "updated_at" => now()
        ]);

        ProblemTestCase::create([
            'problem_id' => 2,
            'input' => [1,2],
            'expected_output' => [-1],
            'is_hidden' => false,
            "created_at" => now(),
            "updated_at" => now()
        ]);
    }
}
