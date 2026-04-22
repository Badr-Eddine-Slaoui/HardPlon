<?php

namespace Database\Seeders;

use App\Models\Brief;
use App\Models\BriefSkillLevel;
use App\Models\Language;
use App\Models\Problem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProblemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brief_id = Brief::inRandomOrder()->value('id');
        Problem::create([
            'brief_id' => $brief_id,
            'brief_skill_level_id' => BriefSkillLevel::where('brief_id', $brief_id)->inRandomOrder()->value('id'),
            'language_id' => Language::inRandomOrder()->value('id'),
            'title' => "Sum of Two Numbers",
            'description' => 'Write a function that takes two numbers as input and returns their sum.',
            'hidden_header' => "const fs = require('fs');",
            'hidden_footer' => "const inputData = fs.readFileSync(0, 'utf-8').trim().split(/\s+/);
if (inputData.length >= 2) {
    const a = parseInt(inputData[0], 10);
    const b = parseInt(inputData[1], 10);
    console.log(add(a, b));
}",
            'skeleton_code' => "function add(a, b) {
    // Your code here
}",
            'order_index' => 1,
            "created_at" => now(),
            "updated_at" => now()
        ]);

        Problem::create([
            'brief_id' => $brief_id,
            'brief_skill_level_id' => BriefSkillLevel::where('brief_id', $brief_id)->inRandomOrder()->value('id'),
            'language_id' => Language::inRandomOrder()->value('id'),
            'title' => "Difference of Two Numbers",
            'description' => 'Create a program that subtracts the second number from the first.',
            'hidden_header' => "const fs = require('fs');",
            'hidden_footer' => "const inputData = fs.readFileSync(0, 'utf-8').trim().split(/\s+/);
if (inputData.length >= 2) {
    const a = parseInt(inputData[0], 10);
    const b = parseInt(inputData[1], 10);
    console.log(sub(a, b));
}",
            'skeleton_code' => "function sub(a, b) {
    // Your code here
}",
            'order_index' => 2,
            "created_at" => now(),
            "updated_at" => now()
        ]);
    }
}
