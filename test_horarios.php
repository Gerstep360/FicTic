<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST DE HORARIOS CORREGIDO ===\n\n";

// Obtener primer docente
$docente = \App\Models\User::role('Docente')->first();
if ($docente) {
    echo "Docente: {$docente->name} (ID: {$docente->id})\n";
    echo "Total horarios: " . $docente->horarioClasesComoDocente()->count() . "\n\n";
    
    echo "Primeros 5 horarios con nombres correctos:\n";
    $horarios = $docente->horarioClasesComoDocente()
                        ->with(['grupo.materia', 'bloque', 'aula'])
                        ->take(5)
                        ->get();
    
    foreach ($horarios as $h) {
        $materia = optional($h->grupo)->materia->nombre ?? 'Sin materia';
        $grupo = optional($h->grupo)->nombre_grupo ?? 'Sin grupo';
        $bloque = optional($h->bloque)->etiqueta ?? 'Sin bloque';
        $aula = optional($h->aula)->nombre_aula ?? 'Sin aula';
        $dia = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'][$h->dia_semana] ?? 'N/A';
        
        echo "  ✓ {$materia} - {$grupo} - {$bloque} ({$dia}) - Aula: {$aula}\n";
    }
    
    echo "\n=== PRUEBA DE ENDPOINT API ===\n";
    // Simular lo que hace el controlador
    $horariosApi = $docente->horarioClasesComoDocente()
                           ->with(['grupo.materia', 'bloque', 'aula'])
                           ->orderBy('dia_semana')
                           ->orderBy('id_bloque')
                           ->get()
                           ->map(function ($horario) {
                               return [
                                   'id_horario' => $horario->id_horario,
                                   'materia' => optional($horario->grupo)->materia->nombre ?? 'Sin materia',
                                   'grupo' => optional($horario->grupo)->nombre_grupo ?? 'Sin grupo',
                                   'bloque' => optional($horario->bloque)->etiqueta ?? 'Sin bloque',
                                   'dia_semana' => $horario->dia_semana,
                                   'aula' => optional($horario->aula)->nombre_aula ?? 'Sin aula',
                               ];
                           });
    
    echo "Total horarios en API: " . $horariosApi->count() . "\n";
    echo "Primeros 3 horarios (formato API):\n";
    foreach ($horariosApi->take(3) as $h) {
        $dia = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'][$h['dia_semana']] ?? 'N/A';
        echo "  ✓ {$h['materia']} - {$h['grupo']} - {$h['bloque']} ({$dia}) - Aula: {$h['aula']}\n";
    }
}

echo "\nDone!\n";
