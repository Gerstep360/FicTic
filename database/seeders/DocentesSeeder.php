<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DocentesSeeder extends Seeder
{
    /**
     * Cambia la cantidad si necesitas más/menos docentes.
     */
    public int $cantidad = 10;

    public function run(): void
    {
        $faker = Faker::create('es_ES');
        $guard = config('auth.defaults.guard', 'web');

        // Asegura que el rol exista
        $rolDocente = Role::firstOrCreate([
            'name'       => 'Docente',
            'guard_name' => $guard,
        ]);

        $creados = 0;

        for ($i = 0; $i < $this->cantidad; $i++) {
            $name = $faker->name();

            $email = $this->uniqueGmail($name);

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'              => $name,
                    'password'          => Hash::make('12345678'), // cámbiala en producción
                    'email_verified_at' => now(),
                ]
            );

            if (!$user->hasRole('Docente')) {
                $user->assignRole($rolDocente);
            }

            $creados += $user->wasRecentlyCreated ? 1 : 0;
        }

        $this->command?->info("✅ DocentesSeeder: {$creados} nuevos docentes creados (de {$this->cantidad}), todos con @gmail.com y rol Docente.");
    }

    /**
     * Genera un email @gmail.com único a partir del nombre.
     */
    private function uniqueGmail(string $name): string
    {
        for ($tries = 0; $tries < 7; $tries++) {
            // local-part legible: nombre.apellido.#### (ascii, lower, puntos)
            $local = Str::of(Str::ascii($name))
                ->lower()
                ->replaceMatches('/[^a-z0-9]+/i', '.')
                ->trim('.')
                ->append('.', random_int(100, 9999))
                ->__toString();

            $email = "{$local}@gmail.com";

            if (!User::where('email', $email)->exists()) {
                return $email;
            }
        }

        // Fallback ultra único
        return Str::uuid() . '@gmail.com';
    }
}
