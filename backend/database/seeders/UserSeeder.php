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

        User::factory()->create([
            'first_name' => "Mounir",
            'last_name' => "Slaoui",
            'age' => 22,
            'email' => "badrslaoui29@gmail.com",
            'cin' => "HH127727",
            'phone' => "0713387595",
            'password' => Hash::make('1811977'),
            'role' => "TEACHER",
            'id_class' => null,
        ]);

        User::factory()->create([
            'first_name' => "Badr",
            'last_name' => "Slaoui",
            'age' => 22,
            'email' => "badrslaoui8@gmail.com",
            'cin' => "HH127748",
            'phone' => "0713387594",
            'password' => Hash::make('1811977'),
            'role' => "STUDENT",
            'id_class' => null,
        ]);

        //User::factory(150)->create();
    }
}
