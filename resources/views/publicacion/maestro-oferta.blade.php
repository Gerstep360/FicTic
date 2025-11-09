<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maestro de Oferta Académica - FicTic</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 min-h-screen">
    
    <div class="container mx-auto px-4 py-8">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-900/50 to-indigo-900/50 border border-purple-700/50 rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-200 mb-2">
                        Maestro de Oferta Académica
                    </h1>
                    <p class="text-slate-400 text-sm">
                        {{ $gestion->nombre }}
                    </p>
                    <p class="text-slate-500 text-xs mt-1">
                        Del {{ $gestion->fecha_inicio->format('d/m/Y') }} al {{ $gestion->fecha_fin->format('d/m/Y') }}
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('publicacion.pdf-maestro', $gestion->id_gestion) }}"
                       class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                        📄 Descargar PDF
                    </a>
                    <img src="{{ asset('brand/logo.png') }}" alt="Logo" class="h-16">
                </div>
            </div>
        </div>

        <!-- Información de publicación -->
        @if($gestion->publicada)
            <div class="bg-green-900/30 border border-green-700 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-green-300 font-medium">Horarios Oficiales Publicados</p>
                        <p class="text-green-400 text-sm">
                            Publicado el {{ $gestion->fecha_publicacion?->format('d/m/Y H:i') }}
                            por {{ $gestion->usuarioPublicador->name ?? 'N/A' }}
                        </p>
                    </div>
                </div>
                @if($gestion->nota_publicacion)
                    <p class="text-green-200 text-sm mt-2 pl-9">
                        {{ $gestion->nota_publicacion }}
                    </p>
                @endif
            </div>
        @endif

        <!-- Resumen general -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            
            <div class="bg-gradient-to-br from-blue-900/30 to-blue-800/30 border border-blue-700 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-600 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-blue-300">Carreras</p>
                        <p class="text-2xl font-bold text-blue-100">{{ $carreras->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-900/30 to-green-800/30 border border-green-700 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <div class="bg-green-600 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-green-300">Materias</p>
                        <p class="text-2xl font-bold text-green-100">
                            {{ $carreras->sum(function($c) { return $c->grupos->pluck('materia')->unique('id_materia')->count(); }) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-900/30 to-purple-800/30 border border-purple-700 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <div class="bg-purple-600 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-purple-300">Grupos</p>
                        <p class="text-2xl font-bold text-purple-100">
                            {{ $carreras->sum(function($c) { return $c->grupos->count(); }) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-900/30 to-orange-800/30 border border-orange-700 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <div class="bg-orange-600 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-orange-300">Docentes</p>
                        <p class="text-2xl font-bold text-orange-100">
                            {{ $carreras->flatMap(function($c) { 
                                return $c->grupos->flatMap(function($g) { 
                                    return $g->horarios->pluck('docente'); 
                                }); 
                            })->unique('id')->count() }}
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Carreras con acordeón -->
        <div class="space-y-4">
            @foreach($carreras as $carrera)
                <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 overflow-hidden">
                    
                    <!-- Header de carrera -->
                    <div class="bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-700 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-200">
                                    {{ $carrera->nombre_carrera }}
                                </h2>
                                <p class="text-sm text-slate-400 mt-1">
                                    {{ $carrera->grupos->count() }} grupos - 
                                    {{ $carrera->grupos->pluck('materia')->unique('id_materia')->count() }} materias
                                </p>
                            </div>
                            <button onclick="toggleCarrera({{ $carrera->id_carrera }})"
                                    class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg transition">
                                Ver Detalles
                            </button>
                        </div>
                    </div>

                    <!-- Contenido de carrera (colapsable) -->
                    <div id="carrera-{{ $carrera->id_carrera }}" class="hidden p-6">
                        
                        @if($carrera->grupos->isNotEmpty())
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-slate-700">
                                            <th class="px-4 py-3 text-left text-slate-300 font-semibold">Materia</th>
                                            <th class="px-4 py-3 text-left text-slate-300 font-semibold">Grupo</th>
                                            <th class="px-4 py-3 text-left text-slate-300 font-semibold">Docente</th>
                                            <th class="px-4 py-3 text-center text-slate-300 font-semibold">Cupo</th>
                                            <th class="px-4 py-3 text-center text-slate-300 font-semibold">Turno</th>
                                            <th class="px-4 py-3 text-center text-slate-300 font-semibold">Horas</th>
                                            <th class="px-4 py-3 text-center text-slate-300 font-semibold">Horarios</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($carrera->grupos->sortBy('materia.nombre_materia') as $grupo)
                                            <tr class="border-b border-slate-700 hover:bg-slate-750">
                                                <td class="px-4 py-3 text-slate-200 font-medium">
                                                    {{ $grupo->materia->nombre_materia ?? 'N/A' }}
                                                </td>
                                                <td class="px-4 py-3 text-slate-300">
                                                    {{ $grupo->codigo_grupo }}
                                                </td>
                                                <td class="px-4 py-3 text-slate-300">
                                                    @php
                                                        $docentes = $grupo->horarios->pluck('docente')->unique('id')->filter();
                                                    @endphp
                                                    @if($docentes->isNotEmpty())
                                                        @foreach($docentes as $docente)
                                                            <span class="block">{{ $docente->name }}</span>
                                                        @endforeach
                                                    @else
                                                        <span class="text-slate-500">Sin asignar</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center text-slate-300">
                                                    {{ $grupo->cupo }}
                                                </td>
                                                <td class="px-4 py-3 text-center text-slate-300">
                                                    <span class="px-2 py-1 rounded text-xs {{ $grupo->turno == 'mañana' ? 'bg-yellow-900/50 text-yellow-300' : 'bg-blue-900/50 text-blue-300' }}">
                                                        {{ ucfirst($grupo->turno) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center text-slate-300">
                                                    {{ $grupo->horarios->sum(function($h) { return $h->bloque->duracion_horas ?? 0; }) }} hrs/sem
                                                </td>
                                                <td class="px-4 py-3 text-slate-400 text-xs">
                                                    @php
                                                        $horariosPorDia = $grupo->horarios->groupBy('dia');
                                                    @endphp
                                                    @foreach($horariosPorDia as $dia => $hrs)
                                                        <div class="mb-1">
                                                            <span class="font-medium">{{ ucfirst($dia) }}:</span>
                                                            @foreach($hrs as $h)
                                                                <span class="whitespace-nowrap">
                                                                    {{ \Carbon\Carbon::parse($h->bloque->hora_inicio)->format('H:i') }}-{{ \Carbon\Carbon::parse($h->bloque->hora_fin)->format('H:i') }}
                                                                    ({{ $h->aula->codigo_aula ?? 'N/A' }})
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center text-slate-500 py-4">No hay grupos registrados para esta carrera</p>
                        @endif

                    </div>

                </div>
            @endforeach
        </div>

        @if($carreras->isEmpty())
            <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-12 text-center">
                <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-slate-400 text-lg">No hay oferta académica registrada para esta gestión</p>
            </div>
        @endif

        <!-- Footer -->
        <div class="mt-8 text-center text-slate-500 text-sm">
            <p>Sistema de Gestión Académica - Universidad</p>
            <p class="mt-1">Maestro de Oferta Académica Oficial</p>
        </div>

    </div>

    <script>
        function toggleCarrera(id) {
            const elemento = document.getElementById(`carrera-${id}`);
            if (elemento.classList.contains('hidden')) {
                elemento.classList.remove('hidden');
            } else {
                elemento.classList.add('hidden');
            }
        }
    </script>

</body>
</html>
