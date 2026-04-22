<?php

namespace Database\Seeders;

use App\Models\Stack;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Stack::create([
            "name" => "Static",
            "description" => "HTML, CSS, JavaScript, and PHP",
            "created_at" => now(),
            "updated_at" => now()
        ]);
    }
}
