<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios por Docente - FicTic</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 min-h-screen">
    
    <div class="container mx-auto px-4 py-8">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-900/50 to-purple-900/50 border border-blue-700/50 rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-200 mb-2">
                        Horarios por Docente
                    </h1>
                    <p class="text-slate-400 text-sm">
                        Consulte los horarios de clases de cualquier docente
                    </p>
                </div>
                <img src="{{ asset('brand/logo.png') }}" alt="Logo" class="h-16">
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6 mb-6">
            <form method="GET" action="{{ route('publicacion.por-docente') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <!-- Gestión -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Gestión
                    </label>
                    <select name="id_gestion" 
                            onchange="this.form.submit()"
                            class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                        <option value="">Seleccione una gestión</option>
                        @foreach($gestiones as $gest)
                            <option value="{{ $gest->id_gestion }}" 
                                    {{ request('id_gestion') == $gest->id_gestion ? 'selected' : '' }}>
                                {{ $gest->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Docente -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Docente
                    </label>
                    <select name="id_docente" 
                            onchange="this.form.submit()"
                            class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200"
                            {{ !request('id_gestion') ? 'disabled' : '' }}>
                        <option value="">Seleccione un docente</option>
                        @foreach($docentes as $doc)
                            <option value="{{ $doc->id }}" 
                                    {{ request('id_docente') == $doc->id ? 'selected' : '' }}>
                                {{ $doc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Botón PDF -->
                <div class="flex items-end">
                    @if($gestionActual && $docenteActual)
                        <a href="{{ route('publicacion.pdf-docente', [$gestionActual->id_gestion, $docenteActual->id]) }}"
                           class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition text-center">
                            📄 Descargar PDF
                        </a>
                    @endif
                </div>

            </form>
        </div>

        @if($gestionActual && $docenteActual && $horarios->isNotEmpty())
            
            <!-- Información del docente -->
            <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-slate-400">Docente</p>
                        <p class="text-lg font-semibold text-slate-200">{{ $docenteActual->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">Correo</p>
                        <p class="text-slate-200">{{ $docenteActual->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">Total de horas</p>
                        <p class="text-lg font-semibold text-slate-200">
                            {{ $horarios->sum(function($h) { return $h->bloque->duracion_horas; }) }} horas/semana
                        </p>
                    </div>
                </div>
            </div>

            <!-- Matriz de horarios -->
            <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 overflow-hidden">
                <div class="p-4 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-700">
                    <h2 class="text-lg font-semibold text-slate-200">Horario Semanal</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-900/50">
                                <th class="px-4 py-3 text-left text-slate-300 font-semibold border-b border-slate-700">
                                    Día
                                </th>
                                @foreach($matrizHorarios['bloques'] ?? [] as $bloqueKey => $bloqueLabel)
                                    <th class="px-4 py-3 text-center text-slate-300 font-semibold border-b border-slate-700 min-w-[150px]">
                                        {{ $bloqueLabel }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
                                $nombresDias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                            @endphp
                            @foreach($dias as $index => $dia)
                                <tr class="border-b border-slate-700 hover:bg-slate-750">
                                    <td class="px-4 py-3 font-medium text-slate-300 bg-slate-900/30">
                                        {{ $nombresDias[$index] }}
                                    </td>
                                    @foreach($matrizHorarios['bloques'] ?? [] as $bloqueKey => $bloqueLabel)
                                        <td class="px-2 py-2 align-top">
                                            @if(isset($matrizHorarios['matriz'][$dia][$bloqueKey]))
                                                @foreach($matrizHorarios['matriz'][$dia][$bloqueKey] as $horario)
                                                    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg p-3 mb-2 shadow-lg">
                                                        <p class="font-semibold text-white text-sm mb-1">
                                                            {{ $horario->grupo->materia->nombre_materia ?? 'N/A' }}
                                                        </p>
                                                        <p class="text-blue-100 text-xs mb-1">
                                                            Grupo: {{ $horario->grupo->codigo_grupo ?? 'N/A' }}
                                                        </p>
                                                        <p class="text-blue-100 text-xs mb-1">
                                                            Aula: {{ $horario->aula->codigo_aula ?? 'N/A' }}
                                                        </p>
                                                        <p class="text-blue-200 text-xs">
                                                            {{ $horario->grupo->materia->carrera->nombre_carrera ?? 'N/A' }}
                                                        </p>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Resumen de materias -->
            <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6 mt-6">
                <h3 class="text-lg font-semibold text-slate-200 mb-4">Materias Asignadas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($horarios->groupBy('grupo.materia.nombre_materia') as $materia => $clases)
                        <div class="bg-slate-700 rounded-lg p-3">
                            <p class="font-medium text-slate-200 mb-1">{{ $materia }}</p>
                            <p class="text-sm text-slate-400">
                                {{ $clases->first()->grupo->codigo_grupo ?? 'N/A' }} - 
                                {{ $clases->sum(function($h) { return $h->bloque->duracion_horas; }) }} horas
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

        @elseif(request('id_gestion') && request('id_docente'))
            
            <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-12 text-center">
                <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-slate-400 text-lg">No hay horarios asignados para este docente</p>
            </div>

        @else

            <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-12 text-center">
                <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="text-slate-400 text-lg">Seleccione una gestión y un docente para ver los horarios</p>
            </div>

        @endif

        <!-- Footer -->
        <div class="mt-8 text-center text-slate-500 text-sm">
            <p>Sistema de Gestión Académica - Universidad</p>
            <p class="mt-1">Horarios oficiales publicados</p>
        </div>

    </div>

</body>
</html>
