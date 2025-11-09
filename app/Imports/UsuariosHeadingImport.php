<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsuariosHeadingImport implements ToCollection, WithHeadingRow
{
    /**
     * Devuelve una Collection de filas (cada fila = array asociativo con keys normalizadas)
     */
    public function collection(Collection $rows)
    {
        return $rows;
    }
}
