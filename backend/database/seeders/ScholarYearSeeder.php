<?php

namespace Database\Seeders;

use App\Models\ScholarYear;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScholarYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ScholarYear::factory(3)->create();
    }
}
