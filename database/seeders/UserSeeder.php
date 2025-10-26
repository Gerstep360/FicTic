<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Asegúrate que RolesPermissionsSeeder corra antes
        $usuarios = [
            ['name' => 'Admin User',       'email' => 'admin@gmail.com',       'role' => 'Admin DTIC'],
            ['name' => 'Decano User',      'email' => 'decano@gmail.com',      'role' => 'Decano'],
            ['name' => 'Director User',    'email' => 'director@gmail.com',    'role' => 'Director'],
            ['name' => 'Coordinador User', 'email' => 'coordinador@gmail.com', 'role' => 'Coordinador'],
            ['name' => 'Docente User',     'email' => 'docente@gmail.com',     'role' => 'Docente'],
            ['name' => 'Bedel User',       'email' => 'bedel@gmail.com',       'role' => 'Bedel'],
        ];

        foreach ($usuarios as $u) {
            // Busca por email; si no existe, crea uno nuevo en memoria
            $user = User::firstOrNew(['email' => $u['email']]);

            // Actualiza nombre siempre
            $user->name = $u['name'];

            // Solo si es nuevo (no existe en DB), asigna password inicial
            if (!$user->exists || !$user->password) {
                $user->password = Hash::make('12345678');
            }

            $user->save();

            // Rol idempotente (requiere spatie/permission)
            if (method_exists($user, 'syncRoles')) {
                $user->syncRoles([$u['role']]);
            }
        }
    }
}
