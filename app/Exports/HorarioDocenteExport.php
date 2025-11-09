<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HorarioDocenteExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $docente;
    protected $horarios;

    public function __construct($docente, $horarios)
    {
        $this->docente = $docente;
        $this->horarios = $horarios;
    }

    public function collection()
    {
        return $this->horarios;
    }

    public function headings(): array
    {
        return [
            'Docente: ' . $this->docente->name,
            '',
            '',
            '',
            '',
            '',
        ];
    }

    public function map($horario): array
    {
        $dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        
        return [
            $dias[$horario->dia_semana] ?? 'N/A',
            $horario->bloque->hora_inicio . ' - ' . $horario->bloque->hora_fin,
            $horario->grupo->materia->nombre ?? 'N/A',
            $horario->grupo->nombre_grupo ?? 'N/A',
            $horario->aula->codigo ?? 'N/A',
            $horario->grupo->gestion->nombre ?? 'N/A',
        ];
    }

    public function title(): string
    {
        return 'Horario Docente';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true]],
        ];
    }
}
