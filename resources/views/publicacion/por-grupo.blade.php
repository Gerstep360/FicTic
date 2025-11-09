<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios por Grupo - FicTic</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 min-h-screen">
    
    <div class="container mx-auto px-4 py-8">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-900/50 to-teal-900/50 border border-green-700/50 rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-200 mb-2">
                        Horarios por Grupo
                    </h1>
                    <p class="text-slate-400 text-sm">
                        Consulte los horarios de clases de cualquier grupo o materia
                    </p>
                </div>
                <img src="{{ asset('brand/logo.png') }}" alt="Logo" class="h-16">
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6 mb-6">
            <form method="GET" action="{{ route('publicacion.por-grupo') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                
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

                <!-- Carrera -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Carrera
                    </label>
                    <select name="id_carrera" 
                            onchange="this.form.submit()"
                            class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200"
                            {{ !request('id_gestion') ? 'disabled' : '' }}>
                        <option value="">Seleccione una carrera</option>
                        @foreach($carreras as $carrera)
                            <option value="{{ $carrera->id_carrera }}" 
                                    {{ request('id_carrera') == $carrera->id_carrera ? 'selected' : '' }}>
                                {{ $carrera->nombre_carrera }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Grupo -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Grupo
                    </label>
                    <select name="id_grupo" 
                            onchange="this.form.submit()"
                            class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200"
                            {{ !request('id_carrera') ? 'disabled' : '' }}>
                        <option value="">Seleccione un grupo</option>
                        @foreach($grupos as $grupo)
                            <option value="{{ $grupo->id_grupo }}" 
                                    {{ request('id_grupo') == $grupo->id_grupo ? 'selected' : '' }}>
                                {{ $grupo->materia->nombre_materia ?? 'N/A' }} - {{ $grupo->codigo_grupo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Botón PDF -->
                <div class="flex items-end">
                    @if($grupoActual)
                        <a href="{{ route('publicacion.pdf-grupo', $grupoActual->id_grupo) }}"
                           class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition text-center">
                            📄 Descargar PDF
                        </a>
                    @endif
                </div>

            </form>
        </div>

        @if($grupoActual && $horarios->isNotEmpty())
            
            <!-- Información del grupo -->
            <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-slate-400">Materia</p>
                        <p class="text-lg font-semibold text-slate-200">
                            {{ $grupoActual->materia->nombre_materia ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">Grupo</p>
                        <p class="text-lg font-semibold text-slate-200">{{ $grupoActual->codigo_grupo }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">Cupo</p>
                        <p class="text-slate-200">{{ $grupoActual->cupo }} estudiantes</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-400">Turno</p>
                        <p class="text-slate-200">{{ ucfirst($grupoActual->turno) }}</p>
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
                                                    <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-lg p-3 mb-2 shadow-lg">
                                                        <p class="font-semibold text-white text-sm mb-1">
                                                            {{ $horario->docente->name ?? 'Sin asignar' }}
                                                        </p>
                                                        <p class="text-green-100 text-xs mb-1">
                                                            Aula: {{ $horario->aula->codigo_aula ?? 'N/A' }}
                                                        </p>
                                                        <p class="text-green-100 text-xs">
                                                            {{ $horario->aula->tipo_aula ?? '' }} (Cap: {{ $horario->aula->capacidad ?? 'N/A' }})
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

            <!-- Información adicional -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                
                <!-- Docentes -->
                <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6">
                    <h3 class="text-lg font-semibold text-slate-200 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                        </svg>
                        Docentes Asignados
                    </h3>
                    <div class="space-y-2">
                        @foreach($horarios->pluck('docente')->unique('id') as $docente)
                            @if($docente)
                                <div class="bg-slate-700 rounded-lg p-3">
                                    <p class="font-medium text-slate-200">{{ $docente->name }}</p>
                                    <p class="text-sm text-slate-400">{{ $docente->email }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Aulas -->
                <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6">
                    <h3 class="text-lg font-semibold text-slate-200 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                        </svg>
                        Aulas Utilizadas
                    </h3>
                    <div class="space-y-2">
                        @foreach($horarios->pluck('aula')->unique('id_aula') as $aula)
                            @if($aula)
                                <div class="bg-slate-700 rounded-lg p-3">
                                    <p class="font-medium text-slate-200">{{ $aula->codigo_aula }}</p>
                                    <p class="text-sm text-slate-400">
                                        {{ $aula->tipo_aula }} - Capacidad: {{ $aula->capacidad }}
                                    </p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

            </div>

        @elseif(request('id_gestion') && request('id_carrera') && request('id_grupo'))
            
            <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-12 text-center">
                <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-slate-400 text-lg">No hay horarios asignados para este grupo</p>
            </div>

        @else

            <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-12 text-center">
                <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="text-slate-400 text-lg">Seleccione una gestión, carrera y grupo para ver los horarios</p>
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
