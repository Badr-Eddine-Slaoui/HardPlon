<?php

namespace Database\Seeders;

use App\Models\CorrectionDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CorrectionDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CorrectionDetail::factory(30)->create();
    }
}
