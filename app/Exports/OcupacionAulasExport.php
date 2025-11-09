<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class OcupacionAulasExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $ocupacion;
    protected $totalSlots;

    public function __construct($ocupacion, $totalSlots)
    {
        $this->ocupacion = $ocupacion;
        $this->totalSlots = $totalSlots;
    }

    public function collection()
    {
        return collect($this->ocupacion);
    }

    public function headings(): array
    {
        return [
            'Aula',
            'Tipo',
            'Capacidad',
            'Edificio',
            'Slots Ocupados',
            'Total Slots',
            'Porcentaje Ocupación (%)',
        ];
    }

    public function map($aula): array
    {
        return [
            $aula['codigo'],
            $aula['tipo'],
            $aula['capacidad'] ?? 'N/A',
            $aula['edificio'] ?? 'N/A',
            $aula['slots_ocupados'],
            $aula['total_slots'],
            $aula['porcentaje'] . '%',
        ];
    }

    public function title(): string
    {
        return 'Ocupación Aulas';
    }
}
