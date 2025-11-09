<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios por Aula - FicTic</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 min-h-screen">
    
    <div class="container mx-auto px-4 py-8">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-orange-900/50 to-red-900/50 border border-orange-700/50 rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-200 mb-2">
                        Horarios por Aula
                    </h1>
                    <p class="text-slate-400 text-sm">
                        Consulte la ocupación de cualquier aula o ambiente
                    </p>
                </div>
                <img src="{{ asset('brand/logo.png') }}" alt="Logo" class="h-16">
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6 mb-6">
            <form method="GET" action="{{ route('publicacion.por-aula') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
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

                <!-- Aula -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Aula
                    </label>
                    <select name="id_aula" 
                            onchange="this.form.submit()"
                            class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200"
                            {{ !request('id_gestion') ? 'disabled' : '' }}>
                        <option value="">Seleccione un aula</option>
                        @foreach($aulas as $aula)
                            <option value="{{ $aula->id_aula }}" 
                                    {{ request('id_aula') == $aula->id_aula ? 'selected' : '' }}>
                                {{ $aula->codigo_aula }} - {{ $aula->tipo_aula }} (Cap: {{ $aula->capacidad }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Botón PDF -->
                <div class="flex items-end">
                    @if($gestionActual && $aulaActual)
                        <a href="{{ route('publicacion.pdf-aula', [$gestionActual->id_gestion, $aulaActual->id_aula]) }}"
                           class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition text-center">
                            📄 Descargar PDF
                        </a>
                    @endif
                </div>

            </form>
        </div>

        @if($gestionActual && $aulaActual && $horarios->isNotEmpty())
            
            <!-- Información del aula -->
            <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-slate-400">Aula</p>
                        <p class="text-lg font-semibold text-slate-200">{{ $aulaActual->codigo_aula }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">Tipo</p>
                        <p class="text-slate-200">{{ $aulaActual->tipo_aula }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">Capacidad</p>
                        <p class="text-slate-200">{{ $aulaActual->capacidad }} estudiantes</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">Ocupación semanal</p>
                        <p class="text-lg font-semibold text-slate-200">
                            {{ $horarios->sum(function($h) { return $h->bloque->duracion_horas; }) }} horas
                        </p>
                    </div>
                </div>
            </div>

            <!-- Matriz de horarios -->
            <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 overflow-hidden">
                <div class="p-4 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-700">
                    <h2 class="text-lg font-semibold text-slate-200">Horario de Ocupación</h2>
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
                                                    <div class="bg-gradient-to-br from-orange-600 to-orange-700 rounded-lg p-3 mb-2 shadow-lg">
                                                        <p class="font-semibold text-white text-sm mb-1">
                                                            {{ $horario->grupo->materia->nombre_materia ?? 'N/A' }}
                                                        </p>
                                                        <p class="text-orange-100 text-xs mb-1">
                                                            Grupo: {{ $horario->grupo->codigo_grupo ?? 'N/A' }}
                                                        </p>
                                                        <p class="text-orange-100 text-xs mb-1">
                                                            Docente: {{ $horario->docente->name ?? 'Sin asignar' }}
                                                        </p>
                                                        <p class="text-orange-200 text-xs">
                                                            {{ $horario->grupo->materia->carrera->nombre_carrera ?? 'N/A' }}
                                                        </p>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="text-center text-slate-500 text-xs py-2">
                                                    Libre
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Estadísticas de ocupación -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                
                <!-- Clases totales -->
                <div class="bg-gradient-to-br from-orange-900/30 to-orange-800/30 border border-orange-700 rounded-lg p-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-orange-600 rounded-full p-3">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-orange-300">Clases Programadas</p>
                            <p class="text-2xl font-bold text-orange-100">{{ $horarios->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Docentes diferentes -->
                <div class="bg-gradient-to-br from-purple-900/30 to-purple-800/30 border border-purple-700 rounded-lg p-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-purple-600 rounded-full p-3">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-purple-300">Docentes Diferentes</p>
                            <p class="text-2xl font-bold text-purple-100">
                                {{ $horarios->pluck('docente')->unique('id')->count() }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Carreras -->
                <div class="bg-gradient-to-br from-teal-900/30 to-teal-800/30 border border-teal-700 rounded-lg p-6">
                    <div class="flex items-center gap-4">
                        <div class="bg-teal-600 rounded-full p-3">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-teal-300">Carreras Atendidas</p>
                            <p class="text-2xl font-bold text-teal-100">
                                {{ $horarios->pluck('grupo.materia.carrera')->unique('id_carrera')->count() }}
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Lista de clases -->
            <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6 mt-6">
                <h3 class="text-lg font-semibold text-slate-200 mb-4">Listado Completo de Clases</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-700">
                                <th class="px-4 py-2 text-left text-slate-300">Día</th>
                                <th class="px-4 py-2 text-left text-slate-300">Horario</th>
                                <th class="px-4 py-2 text-left text-slate-300">Materia</th>
                                <th class="px-4 py-2 text-left text-slate-300">Grupo</th>
                                <th class="px-4 py-2 text-left text-slate-300">Docente</th>
                                <th class="px-4 py-2 text-left text-slate-300">Carrera</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($horarios->sortBy(['dia', 'bloque.hora_inicio']) as $horario)
                                <tr class="border-b border-slate-700 hover:bg-slate-750">
                                    <td class="px-4 py-2 text-slate-300">{{ ucfirst($horario->dia) }}</td>
                                    <td class="px-4 py-2 text-slate-300">
                                        {{ \Carbon\Carbon::parse($horario->bloque->hora_inicio)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($horario->bloque->hora_fin)->format('H:i') }}
                                    </td>
                                    <td class="px-4 py-2 text-slate-200 font-medium">
                                        {{ $horario->grupo->materia->nombre_materia ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-2 text-slate-300">{{ $horario->grupo->codigo_grupo ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 text-slate-300">{{ $horario->docente->name ?? 'Sin asignar' }}</td>
                                    <td class="px-4 py-2 text-slate-400 text-xs">
                                        {{ $horario->grupo->materia->carrera->nombre_carrera ?? 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @elseif(request('id_gestion') && request('id_aula'))
            
            <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-12 text-center">
                <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-slate-400 text-lg">No hay horarios asignados para esta aula</p>
            </div>

        @else

            <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-12 text-center">
                <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="text-slate-400 text-lg">Seleccione una gestión y un aula para ver los horarios</p>
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
