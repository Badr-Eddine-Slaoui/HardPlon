<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'first_name' => "Badr Eddine",
            'last_name' => "Slaoui",
            'age' => 22,
            'email' => "slaoui.badr.eddine.sdv@gmail.com",
            'cin' => "HH127747",
            'phone' => "0713387592",
            'password' => Hash::make('1811977'),
            'role' => "ADMIN",
            'id_class' => null,
        ]);
        User::factory(150)->create();
    }
}
