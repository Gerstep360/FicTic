<?php

namespace Database\Seeders;

use App\Models\Grupo;
use App\Models\Materia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class PopularFICCTGruposSeeder extends Seeder
{
    /**
     * Catálogo REDUCIDO a 2-3 grupos por materia para evitar saturación.
     * Con ~28 aulas disponibles, este tamaño permite horarios realistas sin solapamientos críticos.
     */
    private array $catalogo = [
        // ===== 1er semestre (Tronco) =====
        'FIS100' => ['SA','SB','Z1'],           // 3 grupos
        'INF110' => ['SA','SB','Z1'],           // 3 grupos
        'INF119' => ['SA','Z1'],                // 2 grupos
        'LIN100' => ['SA','Z1'],                // 2 grupos
        'MAT101' => ['SA','SB','Z1'],           // 3 grupos

        // ===== 2do semestre (Tronco) =====
        'FIS102' => ['SA','SB'],                // 2 grupos
        'INF120' => ['SA','SB','Z1'],           // 3 grupos
        'LIN101' => ['SA','SB'],                // 2 grupos
        'MAT102' => ['SA','SB'],                // 2 grupos
        'MAT103' => ['SA','SB','Z1'],           // 3 grupos

        // ===== 3er semestre (Tronco) =====
        'ADM100' => ['SA','SB'],                // 2 grupos
        'FIS200' => ['SA','SB'],                // 2 grupos
        'INF210' => ['SA','SB','Z1'],           // 3 grupos
        'INF211' => ['SA','SB'],                // 2 grupos
        'MAT207' => ['SA','SB'],                // 2 grupos
        // Optativas para habilitar ruta Redes
        'ELT241' => ['SA'],                     // 1 grupo
        'RDS210' => ['SA','SB'],                // 2 grupos

        // ===== 4to semestre (Tronco) =====
        'ADM200' => ['SA','SB'],                // 2 grupos
        'INF220' => ['SA','SB'],                // 2 grupos
        'INF221' => ['SA','SB','Z1'],           // 3 grupos
        'MAT202' => ['SA','SB'],                // 2 grupos
        'MAT205' => ['SA','SB'],                // 2 grupos
        'RDS220' => ['SA'],                     // 1 grupo

        // ===== 5to semestre (Rutas) =====
        'INF310' => ['SA','SB'],                // 2 grupos (INF/SIS)
        'INF312' => ['SA','SB'],                // 2 grupos (INF/SIS)
        'INF318' => ['SA'],                     // 1 grupo (INF)
        'INF319' => ['SA'],                     // 1 grupo (INF)
        'MAT302' => ['SA','SB'],                // 2 grupos (INF/SIS/RED)
        'ADM330' => ['SA'],                     // 1 grupo (SIS)
        'ECO300' => ['SA'],                     // 1 grupo (SIS)
        'ELT352' => ['R1'],                     // 1 grupo (RED)
        'ELT354' => ['R1'],                     // 1 grupo (RED)
        'RDS310' => ['SA'],                     // 1 grupo (RED)

        // ===== 6to semestre (Rutas) =====
        'INF322' => ['SA','SB'],                // 2 grupos (INF/SIS/RED)
        'INF323' => ['SA','SB'],                // 2 grupos (INF/SIS/RED)
        'INF329' => ['SA'],                     // 1 grupo (INF)
        'INF342' => ['SA','SB'],                // 2 grupos (INF/SIS)
        'MAT329' => ['SA','SB'],                // 2 grupos (INF/SIS/RED)
        'ADM320' => ['SA'],                     // 1 grupo (SIS)
        'ELC103' => ['SA'],                     // 1 grupo (Electiva INF)
        'ELC104' => ['SA'],                     // 1 grupo (Electiva INF)
        'ELT362' => ['R1'],                     // 1 grupo (RED)
        'RDS320' => ['SA'],                     // 1 grupo (RED)

        // ===== 7mo semestre =====
        'INF412' => ['SA','SB'],                // 2 grupos (INF)
        'INF413' => ['SA','SB'],                // 2 grupos (INF)
        'INF418' => ['SA'],                     // 1 grupo (INF)
        'INF433' => ['SA','SB'],                // 2 grupos (INF)
        'MAT419' => ['SA'],                     // 1 grupo (INF/SIS/RED)
        'INF432' => ['SA'],                     // 1 grupo (SIS)
        'ELT374' => ['R1'],                     // 1 grupo (RED)
        'RDS410' => ['SA'],                     // 1 grupo (RED)

        // ===== 8vo semestre =====
        'ECO449' => ['SA'],                     // 1 grupo (INF/SIS/RED)
        'INF422' => ['SA','SB'],                // 2 grupos (INF/SIS)
        'INF423' => ['SA'],                     // 1 grupo (INF/RED)
        'INF428' => ['SA'],                     // 1 grupo (INF)
        'INF442' => ['SA','SB'],                // 2 grupos (INF/SIS)
        'INF462' => ['SA'],                     // 1 grupo (SIS)
        'ELT384' => ['R1'],                     // 1 grupo (RED)
        'RDS421' => ['SA'],                     // 1 grupo (RED)
        'RDS429' => ['SA'],                     // 1 grupo (RED)

        // ===== 9no semestre =====
        'INF511' => ['SA','SB'],                // 2 grupos (INF/SIS/RED)
        'INF512' => ['SA'],                     // 1 grupo (INF/SIS)
        'INF513' => ['SA','SB'],                // 2 grupos (INF/SIS/RED)
        'INF552' => ['SA'],                     // 1 grupo (INF/SIS)
        'ELC105' => ['SA'],                     // 1 grupo (Electiva)
        'ELC107' => ['SA'],                     // 1 grupo (Electiva)

        // ===== 10mo semestre =====
        'INF521' => ['SA','SB'],                // 2 grupos (INF/SIS/RED)
        'ELC106' => ['SA'],                     // 1 grupo (Electiva)
        'ELC101' => ['SA'],                     // 1 grupo (Electiva)
        'ELC102' => ['SA'],                     // 1 grupo (Electiva)
        'ELC108' => ['SA'],                     // 1 grupo (Electiva)
        'ELC209' => ['SA'],                     // 1 grupo (Electiva RED)
        'ELC210' => ['SA'],                     // 1 grupo (Electiva RED)
    ];

    private string $defaultModalidad = 'Presencial';
    private int    $defaultCupo      = 40;

    /** Cambia a false si NO quieres asignar docentes en este seeder */
    private bool $asignarDocentes = true;

    public function run(): void
    {
        $gestionId = $this->getOrCreateGestionActiva();

        // ---- Pool de docentes existentes (IDs) ----
        $docenteIds = $this->asignarDocentes
            ? (User::role('Docente')->pluck('id')->all() ?? [])
            : [];

        if ($this->asignarDocentes && empty($docenteIds)) {
            $this->command?->warn('⚠️  No hay usuarios con rol Docente. Se crearán los grupos sin docente.');
        }

        // Rueda round-robin para repartir docentes
        $i = 0;
        $pickDocente = function () use (&$i, $docenteIds) {
            if (empty($docenteIds)) return null;
            $id = $docenteIds[$i % count($docenteIds)];
            $i++;
            return $id;
        };

        $nuevos = 0;
        $actualizados = 0;

        foreach ($this->catalogo as $codigoMateria => $grupos) {
            // IMPORTANTE: Tronco común aparece en 3 carreras, pero solo necesitamos 1 set de grupos
            // Para evitar duplicación, tomamos SOLO la primera materia encontrada
            $materia = Materia::where('codigo', $codigoMateria)->first();

            if (!$materia) {
                $this->command->warn("⚠️  Materia {$codigoMateria} no existe (se omite).");
                continue;
            }

            foreach ($grupos as $nombre) {
                $turno = $this->inferirTurno($nombre);

                $grupo = Grupo::firstOrCreate(
                    [
                        'id_materia'   => $materia->id_materia,
                        'id_gestion'   => $gestionId,
                        'nombre_grupo' => $nombre,
                    ],
                    [
                        'turno'      => $turno,
                        'modalidad'  => $this->defaultModalidad,
                        'cupo'       => $this->defaultCupo,
                        'id_docente' => $pickDocente(),
                    ]
                );

                if ($grupo->wasRecentlyCreated) {
                    $nuevos++;
                } else {
                    // Completa datos faltantes si aplica
                    $updates = [];
                    if (is_null($grupo->id_docente)) {
                        $updates['id_docente'] = $pickDocente();
                    }
                    if (empty($grupo->turno)) {
                        $updates['turno'] = $turno;
                    }
                    if ($updates) {
                        $grupo->update($updates);
                        $actualizados++;
                    }
                }
            }

            $this->command->info("✅ {$codigoMateria} ({$materia->nombre}) → " . count($grupos) . " grupos para gestión #{$gestionId}.");
        }

        $this->command->info("🎉 PopularFICCTGruposSeeder listo. Nuevos: {$nuevos} · Actualizados: {$actualizados}");
    }

    /**
     * Infieren turno por prefijo de grupo (ajústalo a tu convención real).
     */
    private function inferirTurno(string $grupo): string
    {
        $g = strtoupper($grupo);
        if (str_starts_with($g, 'N')) return 'Noche';           // N*, NX, NW...
        if (str_starts_with($g, 'I')) return 'Tarde';           // I1, I2...
        if (str_starts_with($g, 'Z')) return 'Fines de semana'; // Z1..Z6
        if (str_starts_with($g, 'W')) return 'Virtual';         // W1
        return 'Mañana';                                        // S*, R1, CI, etc.
    }

    /**
     * Obtiene/crea gestión activa y devuelve id_gestion.
     */
    private function getOrCreateGestionActiva(): int
    {
        $today = Carbon::today()->toDateString();

        $id = DB::table('gestiones')
            ->whereDate('fecha_inicio', '<=', $today)
            ->whereDate('fecha_fin', '>=', $today)
            ->orderByDesc('fecha_inicio')
            ->value('id_gestion');

        if ($id) return (int)$id;

        // Crear una por defecto si no hay
        $year = (int) date('Y');
        $month = (int) date('n');
        $isFirst = $month <= 6;

        $nombre = ($isFirst ? "I-" : "II-") . $year;
        $fechaInicio = $isFirst ? "{$year}-02-01" : "{$year}-08-01";
        $fechaFin    = $isFirst ? "{$year}-06-30" : "{$year}-12-15";

        $id = DB::table('gestiones')->insertGetId([
            'nombre'       => $nombre,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin'    => $fechaFin,
            'publicada'    => false,
            'horarios'     => 'BORRADOR',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        $this->command->info("ℹ️  No había gestión activa. Se creó: {$nombre} (#{$id})");
        return (int)$id;
    }
}
