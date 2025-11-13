<?php

namespace Database\Seeders\Supports;

use App\Models\Materia;
use Illuminate\Support\Facades\DB;

trait SeedMateriasHelper
{
    protected function resolveCarreraId(string $sigla, string $fallbackLike): int
    {
        // Buscar por nombre directamente (la tabla carreras no tiene columna 'sigla')
        $id = DB::table('carreras')->where('nombre', 'like', "%{$fallbackLike}%")->value('id_carrera');
        
        if (!$id) {
            // Fallback: buscar la primera carrera disponible
            $id = DB::table('carreras')->orderBy('id_carrera')->value('id_carrera');
        }
        
        return $id ?? 1; // último recurso
    }

    /**
     * Inserta/actualiza materias y luego crea los prerrequisitos.
     * $materias: [ ['codigo','nombre','nivel','creditos','req'=>['COD1','COD2',...]], ... ]
     * Key única: (codigo,id_carrera)
     */
    protected function upsertMaterias(int $idCarrera, array $materias): void
    {
        // 1) Upsert materias
        foreach ($materias as $m) {
            Materia::updateOrCreate(
                ['codigo' => $m['codigo'], 'id_carrera' => $idCarrera],
                [
                    'nombre'    => $m['nombre'],
                    'nivel'     => $m['nivel'],
                    'creditos'  => $m['creditos'],
                ]
            );
        }

        // 2) Mapear ids por código para esta carrera
        $codes = array_column($materias, 'codigo');
        $idByCode = Materia::where('id_carrera', $idCarrera)
            ->whereIn('codigo', $codes)
            ->pluck('id_materia', 'codigo');

        // 3) Conectar prerrequisitos
        foreach ($materias as $m) {
            $reqCodes = $m['req'] ?? [];
            if (!$reqCodes) continue;

            $materiaId = $idByCode[$m['codigo']] ?? null;
            if (!$materiaId) continue;

            $reqIds = Materia::where('id_carrera', $idCarrera)
                ->whereIn('codigo', $reqCodes)
                ->pluck('id_materia')
                ->all();

            if ($reqIds) {
                Materia::find($materiaId)
                    ->prerrequisitos()
                    ->syncWithoutDetaching($reqIds);
            }
        }
    }
}
