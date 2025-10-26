<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminuser = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
        ]);
        $adminuser->assignRole('Admin DTIC');
        $Decanoruser = \App\Models\User::create([
            'name' => 'Decano User',
            'email' => 'decano@gmail.com',
            'password' => bcrypt('12345678'),
        ]);
        $Decanoruser->assignRole('Decano');
        $Directoruser = \App\Models\User::create([
            'name' => 'Director User',
            'email' => 'director@gmail.com',
            'password' => bcrypt('12345678'),
        ]);
        $Directoruser->assignRole('Director');
        $Coordinadoruser = \App\Models\User::create([
            'name' => 'Coordinador User',
            'email' => 'coordinador@gmail.com',
            'password' => bcrypt('12345678'),
        ]);
        $Coordinadoruser->assignRole('Coordinador');
        $Docenteuser = \App\Models\User::create([
            'name' => 'Docente User',
            'email' => 'docente@gmail.com',
            'password' => bcrypt('12345678'),
        ]);
        $Docenteuser->assignRole('Docente');
        $Bedeluser = \App\Models\User::create([
            'name' => 'Bedel User',
            'email' => 'bedel@gmail.com',
            'password' => bcrypt('12345678'),
        ]);
        $Bedeluser->assignRole('Bedel');
    }
}
