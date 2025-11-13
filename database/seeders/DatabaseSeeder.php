<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            RolesPermissionsSeeder::class,
            UserSeeder::class,
            FacultadSeeder::class,
            AulasSeeder::class,
            CarreraSeeder::class,
            MateriasTroncoComunSeeder::class, // primero (multicarrera)
            MateriasInformaticaSeeder::class,
            MateriasSistemasSeeder::class,
            MateriasRedesSeeder::class,
            DocentesSeeder::class,
            PopularFICCTGruposSeeder::class,
            BloquesSeeder::class,

        ]);
    }
}
