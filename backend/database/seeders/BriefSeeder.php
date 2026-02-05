<?php

namespace Database\Seeders;

use App\Models\Brief;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BriefSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Brief::factory(20)->create();
    }
}
