<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class AsistenciaDocenteExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $docente;
    protected $asistencias;
    protected $estadisticas;
    protected $filtros;

    public function __construct($docente, $asistencias, $estadisticas, $filtros)
    {
        $this->docente = $docente;
        $this->asistencias = $asistencias;
        $this->estadisticas = $estadisticas;
        $this->filtros = $filtros;
    }

    public function collection()
    {
        return $this->asistencias;
    }

    public function headings(): array
    {
        return [
            'Fecha/Hora',
            'Materia',
            'Aula',
            'Tipo Marca',
            'Estado',
        ];
    }

    public function map($asistencia): array
    {
        return [
            $asistencia->fecha_hora->format('d/m/Y H:i'),
            $asistencia->horario->grupo->materia->nombre ?? 'N/A',
            $asistencia->horario->aula->codigo ?? 'N/A',
            $asistencia->tipo_marca,
            $asistencia->estado,
        ];
    }

    public function title(): string
    {
        return 'Asistencias';
    }
}
