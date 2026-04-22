<?php

namespace Database\Seeders;

use App\Models\Runner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RunnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Runner::create([
            "name" => "universal",
            "description" => "badreddineslaoui/universal-runner:1.0",
            "type" => "http",
            "status" => "active",
            "created_at" => now(),
            "updated_at" => now()
        ]);
    }
}
