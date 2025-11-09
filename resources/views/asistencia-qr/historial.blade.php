<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                Historial de Asistencias
            </h2>
            <a href="{{ route('asistencia-qr.index') }}" 
               class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg transition text-sm">
                ← Volver al Escáner
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Filtro de fecha -->
            <div class="mb-6 bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-4">
                <form method="GET" class="flex items-center gap-4">
                    <label class="text-sm font-medium text-slate-300">Fecha:</label>
                    <input type="date" 
                           name="fecha" 
                           value="{{ $fecha }}"
                           class="rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        Filtrar
                    </button>
                    <a href="{{ route('asistencia-qr.historial') }}" 
                       class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg transition">
                        Hoy
                    </a>
                </form>
            </div>

            <!-- Tabla de asistencias -->
            <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-900/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-slate-300 font-semibold">Hora</th>
                                <th class="px-4 py-3 text-left text-slate-300 font-semibold">Docente</th>
                                <th class="px-4 py-3 text-left text-slate-300 font-semibold">Materia</th>
                                <th class="px-4 py-3 text-left text-slate-300 font-semibold">Grupo</th>
                                <th class="px-4 py-3 text-left text-slate-300 font-semibold">Aula</th>
                                <th class="px-4 py-3 text-center text-slate-300 font-semibold">Bloque</th>
                                <th class="px-4 py-3 text-center text-slate-300 font-semibold">Tipo</th>
                                <th class="px-4 py-3 text-center text-slate-300 font-semibold">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($asistencias as $asistencia)
                                <tr class="border-b border-slate-700 hover:bg-slate-750">
                                    <td class="px-4 py-3 text-slate-300 font-mono">
                                        {{ $asistencia->fecha_hora->format('H:i:s') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-slate-200">{{ $asistencia->docente->name }}</div>
                                        <div class="text-xs text-slate-400">{{ $asistencia->docente->email }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-300">
                                        {{ $asistencia->horario->grupo->materia->nombre }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-300">
                                        {{ $asistencia->horario->grupo->nombre_grupo }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-slate-200 font-medium">{{ $asistencia->horario->aula->codigo }}</div>
                                        @if($asistencia->horario->aula->edificio)
                                            <div class="text-xs text-slate-400">{{ $asistencia->horario->aula->edificio }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center text-slate-300 font-mono text-xs">
                                        {{ $asistencia->horario->bloque->etiqueta ?? 
                                           ($asistencia->horario->bloque->hora_inicio . '-' . $asistencia->horario->bloque->hora_fin) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($asistencia->tipo_marca === 'ENTRADA')
                                            <span class="px-2 py-1 text-xs rounded bg-blue-900/50 border border-blue-700 text-blue-300">
                                                → Entrada
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded bg-purple-900/50 border border-purple-700 text-purple-300">
                                                ← Salida
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($asistencia->estado === 'PRESENTE')
                                            <span class="px-2 py-1 text-xs rounded bg-green-900/50 border border-green-700 text-green-300">
                                                ✓ Presente
                                            </span>
                                        @elseif($asistencia->estado === 'FALTA')
                                            <span class="px-2 py-1 text-xs rounded bg-red-900/50 border border-red-700 text-red-300">
                                                ✗ Falta
                                            </span>
                                        @elseif($asistencia->estado === 'TARDANZA')
                                            <span class="px-2 py-1 text-xs rounded bg-orange-900/50 border border-orange-700 text-orange-300">
                                                ⚠ Tardanza
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded bg-slate-700 text-slate-300">
                                                {{ $asistencia->estado }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-12 text-center">
                                        <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <p class="text-slate-400 text-lg">No hay asistencias registradas para esta fecha</p>
                                        <p class="text-slate-500 text-sm mt-2">{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Paginación -->
            @if($asistencias->hasPages())
                <div class="mt-6">
                    {{ $asistencias->appends(['fecha' => $fecha])->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
