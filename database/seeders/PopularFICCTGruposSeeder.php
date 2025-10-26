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
     * Catálogo: materia => [grupos...]
     * Toma los códigos más frecuentes que compartiste.
     * Puedes ampliar/reducir libremente este array.
     */
    private array $catalogo = [
        // 1er semestre
        'FIS100' => ['SA','SB','SC','SD','SG','SI','SP','Z1','Z2','Z3','Z4','Z5','Z6','I1','W1'],
        'INF110' => ['SA','SB','SC','SD','SF','SG','SH','SI','SN','SS','SZ','Z1','Z2','Z3','Z4','Z6','W1'],
        'INF119' => ['SE','SF','SG','SH','SK','Z1','Z2','Z3','Z6'],
        'LIN100' => ['NW','SB','Z1','Z2','Z3','Z4','Z5','Z6'],
        'MAT101' => ['F1','SB','SG','SI','SP','SZ','Z1','Z2','Z3','Z6','I2','W1','CI'],

        // 2do semestre
        'FIS102' => ['BI','NW','NX','SA','SB','R1'],
        'INF120' => ['SA','SB','SF','SH','SC','SD','SG','SI'],
        'LIN101' => ['SB','SE','SC','SZ'],
        'MAT102' => ['I1','SN','SB','SD','SH','R1','C1'],
        'MAT103' => ['SA','SB','SC','SD','SF','SE'],

        // 3ro
        'ADM100' => ['SA','SC'],
        'FIS200' => ['SA','SB','SC'],
        'INF210' => ['SA','SB','SC','SD','SI'],
        'INF211' => ['SA','SB'],
        'NAT207' => ['NX','NW'],
        'MAT207' => ['SA','SC'],

        // 4to
        'ADM200' => ['SA','SB'],
        'INF220' => ['SA','SB','SD','I2'],
        'INF221' => ['SA','SB','SC','SX'],
        'MAT202' => ['SC','SB','SI'],
        'MAT205' => ['SC','SD','SE'],
        'ELC102' => ['SA'],

        // 5to
        'INF310' => ['SA','SB','SX'],
        'INF312' => ['SA','SC'],
        'INF318' => ['SA'],
        'INF319' => ['SA'],
        'MAT302' => ['SI','SB'],
        'ADM330' => ['SA','SC'],
        'ECO300' => ['SA','SB'],

        // 6to
        'ELC103' => ['SA'],
        'INF322' => ['SB','SD'],
        'INF329' => ['SA'],
        'INF323' => ['SA','SC'],
        'INF342' => ['SA','SC'],
        'MAT329' => ['SB','SZ','SS'],
        'ELC005' => ['SA'],
        'ADM320' => ['SA','SC'],

        // 7mo
        'INF412' => ['SA','SB'],
        'INF413' => ['SA','SB'],
        'INF418' => ['SA'],
        'INF432' => ['SA'],
        'INF433' => ['SA','SC'],
        'MAT419' => ['SA','SC'],
        'ELC106' => ['SA'],

        // 8vo
        'ECO449' => ['SA','SI'],
        'ELC008' => ['SA'],
        'ELC107' => ['SA','I2'],
        'INF422' => ['SB','SC'],
        'INF423' => ['SC','R1'],
        'INF428' => ['SB'],
        'INF442' => ['SA','SI'],
        'INF462' => ['SA'],

        // 9no
        'INF511' => ['SA','SC','SS'],
        'INF512' => ['SB'],
        'INF513' => ['SA','SC'],
        'INF552' => ['SA'],

        // Extra (redes/electrónica – opcional, coméntalos si no aplica)
        'RDS210' => ['SA','SB'],
        'RDS220' => ['SZ'],
        'RDS320' => ['SA'],
        'RDS421' => ['SA'],
        'RDS429' => ['SA'],
        'RDS511' => ['SA'],
        'RDS512' => ['SA'],
        'ELT241' => ['SC'],
        'ELT352' => ['ER'],
        'ELT374' => ['SR'],
        'ELT354' => ['R1'],
        'ELT362' => ['R1'],
        'ELC201' => ['SA'],
        'ELC203' => ['R1'],
        'ELC204' => ['SR'],
    ];

   private string $defaultTurno = 'Mañana';
    private string $defaultModalidad = 'Presencial';
    private int    $defaultCupo = 40;
    
    /** Cambia a false si NO quieres asignar docentes en este seeder */
    private bool $asignarDocentes = true;

    public function run(): void
    {
        $gestionId = $this->getOrCreateGestionActiva();

        // ---- Pool de docentes existentes (IDs) ----
        $docenteIds = $this->asignarDocentes
            ? User::role('Docente')->pluck('id')->all()
            : [];

        if ($this->asignarDocentes && empty($docenteIds)) {
            $this->command->warn('⚠️  No hay usuarios con rol Docente. Se crearán los grupos sin docente.');
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
            // Puedes tener la misma materia en más de una carrera; recorremos todas.
            $materias = Materia::where('codigo', $codigoMateria)->get();

            if ($materias->isEmpty()) {
                $this->command->warn("⚠️  Materia {$codigoMateria} no existe. (Se omite)");
                continue;
            }

            foreach ($materias as $materia) {
                foreach ($grupos as $nombre) {
                    // Intenta crear
                    $grupo = Grupo::firstOrCreate(
                        [
                            'id_materia'   => $materia->id_materia,
                            'id_gestion'   => $gestionId,
                            'nombre_grupo' => $nombre,
                            
                        ],
                        [
                            'turno'      => $this->defaultTurno,
                            'modalidad'  => $this->defaultModalidad,
                            'cupo'       => $this->defaultCupo,
                            'id_docente' => $pickDocente(),
                            
                        ]
                    );

                    if ($grupo->wasRecentlyCreated) {
                        $nuevos++;
                    } else {
                        // Si ya existía pero NO tenía docente, se lo asignamos ahora
                        if (is_null($grupo->id_docente)) {
                            $grupo->update(['id_docente' => $pickDocente()]);
                            $actualizados++;
                        }
                    }
                }

                $this->command->info("✅ {$codigoMateria} ({$materia->nombre}) → grupos asegurados para gestión #{$gestionId}.");
            }
        }

        $this->command->info("🎉 PopularFICCTGruposSeeder listo. Nuevos: {$nuevos} · Con docente asignado en existentes: {$actualizados}");
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