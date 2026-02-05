<?php

namespace Database\Seeders;

use App\Models\Squad;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SquadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Squad::factory(10)->create();
    }
}
