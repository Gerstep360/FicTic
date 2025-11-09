<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PlantillaUsuariosExport implements FromArray, WithHeadings, WithStyles, WithEvents
{
    public function headings(): array
    {
        // SIN datos de ejemplo, solo cabeceras
        return ['ID', 'NOMBRE', 'CORREO', 'CONTRASEÑA', 'ROLES'];
    }

    public function array(): array
    {
        // No data: la hoja se entrega vacía con solo encabezados
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo de encabezado
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->freezePane('A2');

        // Ancho de columnas sugerido
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(28);
        $sheet->getColumnDimension('C')->setWidth(36);
        $sheet->getColumnDimension('D')->setWidth(22);
        $sheet->getColumnDimension('E')->setWidth(28);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Comentarios para las cabeceras (sin usar setText con Run)
                $tips = [
                    'A1' => 'Opcional: si lo pones, actualiza por ID.',
                    'C1' => 'Requerido al crear. Debe ser email válido.',
                    'D1' => 'Opcional: si está vacío al CREAR, se genera temporal y se envía por correo.',
                    'E1' => "Opcional: separar múltiples roles con ';' (p. ej. Docente;Coordinador). Si se indica, reemplaza roles actuales.",
                ];

                foreach ($tips as $cell => $text) {
                    $comment = $sheet->getComment($cell); // crea si no existe
                    $comment->setAuthor('FicTic');
                    $rich = $comment->getText();          // RichText
                    // Agrega el texto como 'run' dentro del RichText existente
                    $rich->createTextRun($text);

                    // Opcional: tamaño del globo del comentario
                    $comment->setWidth('260pt')->setHeight('100pt');
                }
            },
        ];
    }

}
