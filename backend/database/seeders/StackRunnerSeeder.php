<?php

namespace Database\Seeders;

use App\Models\StackRunner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StackRunnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StackRunner::create([
            "stack_id" => 1,
            "runner_version_id" => 1,
            "priority" => 1,
            "created_at" => now(),
            "updated_at" => now()
        ]);
    }
}
