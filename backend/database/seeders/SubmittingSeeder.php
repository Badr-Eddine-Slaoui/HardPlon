<?php

namespace Database\Seeders;

use App\Models\Submitting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubmittingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Submitting::factory(20)->create();
    }
}
