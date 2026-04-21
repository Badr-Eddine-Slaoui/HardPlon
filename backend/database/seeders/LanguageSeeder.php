<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Language::create([
            'name' => 'JavaScript',
            'docker_image' => 'node:20-alpine',
            'compile_command' => null,
            'run_command' => 'node {file}',
        ]);
    }
}
