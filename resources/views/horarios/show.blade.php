<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('horarios.index') }}" class="text-slate-400 hover:text-slate-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                    {{ __('Detalle del Horario') }}
                </h2>
            </div>

            @if(auth()->user()->can('asignar_horarios') || auth()->user()->hasRole('Admin DTIC'))
                <div class="flex items-center gap-2">
                    <a href="{{ route('horarios.edit', $horario) }}" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar
                    </a>
                    <form method="POST" action="{{ route('horarios.destroy', $horario) }}" 
                          onsubmit="return confirm('¿Estás seguro de eliminar esta asignación de horario? Esta acción no se puede deshacer.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn bg-red-600 hover:bg-red-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Eliminar
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-6">
        {{-- Información principal --}}
        <div class="card p-8">
            <div class="flex items-start gap-6">
                {{-- Ícono --}}
                <div class="p-4 bg-blue-500/10 rounded-xl border border-blue-500/20">
                    <svg class="w-12 h-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                {{-- Detalles --}}
                <div class="flex-1 space-y-4">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-200">
                            {{ $horario->grupo->materia->nombre }}
                        </h3>
                        <p class="text-slate-400 mt-1">
                            Grupo {{ $horario->grupo->nombre_grupo }} - {{ $horario->grupo->turno }} ({{ $horario->grupo->modalidad }})
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex items-center gap-3 p-3 bg-slate-800/50 rounded-lg">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-xs text-slate-400">Día</p>
                                <p class="font-medium text-slate-200">{{ $dias[$horario->dia_semana] }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 bg-slate-800/50 rounded-lg">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-xs text-slate-400">Horario</p>
                                <p class="font-medium text-slate-200">
                                    {{ \Carbon\Carbon::parse($horario->bloque->hora_inicio)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($horario->bloque->hora_fin)->format('H:i') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 bg-slate-800/50 rounded-lg">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <div>
                                <p class="text-xs text-slate-400">Aula</p>
                                <p class="font-medium text-slate-200">{{ $horario->aula->codigo }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Información académica --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Grupo/Materia --}}
            <div class="card p-6 space-y-4">
                <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Información de la Materia
                </h3>

                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-slate-400">Materia:</span>
                        <span class="text-slate-200 font-medium ml-2">{{ $horario->grupo->materia->nombre }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400">Código:</span>
                        <span class="text-slate-200 font-medium ml-2">{{ $horario->grupo->materia->codigo }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400">Carrera:</span>
                        <span class="text-slate-200 font-medium ml-2">{{ $horario->grupo->materia->carrera->nombre_carrera }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400">Grupo:</span>
                        <span class="text-slate-200 font-medium ml-2">{{ $horario->grupo->nombre_grupo }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400">Turno:</span>
                        <span class="text-slate-200 font-medium ml-2">{{ $horario->grupo->turno }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400">Modalidad:</span>
                        <span class="text-slate-200 font-medium ml-2">{{ $horario->grupo->modalidad }}</span>
                    </div>
                </div>
            </div>

            {{-- Docente --}}
            <div class="card p-6 space-y-4">
                <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Docente Asignado
                </h3>

                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-emerald-500/10 rounded-full flex items-center justify-center border-2 border-emerald-500/20">
                        <span class="text-2xl font-bold text-emerald-400">
                            {{ strtoupper(substr($horario->docente->name, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-slate-200">{{ $horario->docente->name }}</p>
                        <p class="text-sm text-slate-400">{{ $horario->docente->email }}</p>
                        @if($horario->docente->phone)
                            <p class="text-sm text-slate-400">{{ $horario->docente->phone }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Aula --}}
            <div class="card p-6 space-y-4">
                <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Información del Aula
                </h3>

                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-slate-400">Código:</span>
                        <span class="text-slate-200 font-medium ml-2">{{ $horario->aula->codigo }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400">Tipo:</span>
                        <span class="text-slate-200 font-medium ml-2">{{ $horario->aula->tipo }}</span>
                    </div>
                    @if($horario->aula->capacidad)
                        <div>
                            <span class="text-slate-400">Capacidad:</span>
                            <span class="text-slate-200 font-medium ml-2">{{ $horario->aula->capacidad }} estudiantes</span>
                        </div>
                    @endif
                    @if($horario->aula->edificio)
                        <div>
                            <span class="text-slate-400">Edificio:</span>
                            <span class="text-slate-200 font-medium ml-2">{{ $horario->aula->edificio }}</span>
                        </div>
                    @endif
                    @if($horario->aula->piso)
                        <div>
                            <span class="text-slate-400">Piso:</span>
                            <span class="text-slate-200 font-medium ml-2">{{ $horario->aula->piso }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Horario --}}
            <div class="card p-6 space-y-4">
                <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Horario
                </h3>

                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-slate-400">Día:</span>
                        <span class="text-slate-200 font-medium ml-2">{{ $dias[$horario->dia_semana] }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400">Bloque:</span>
                        <span class="text-slate-200 font-medium ml-2">
                            {{ $horario->bloque->etiqueta ?? "Bloque {$horario->id_bloque}" }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-400">Hora inicio:</span>
                        <span class="text-slate-200 font-medium ml-2">
                            {{ \Carbon\Carbon::parse($horario->bloque->hora_inicio)->format('H:i') }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-400">Hora fin:</span>
                        <span class="text-slate-200 font-medium ml-2">
                            {{ \Carbon\Carbon::parse($horario->bloque->hora_fin)->format('H:i') }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-400">Duración:</span>
                        <span class="text-slate-200 font-medium ml-2">
                            {{ \Carbon\Carbon::parse($horario->bloque->hora_inicio)->diffInMinutes(\Carbon\Carbon::parse($horario->bloque->hora_fin)) }} minutos
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Asistencias recientes --}}
        @if($horario->asistencias->count() > 0)
            <div class="card p-6 space-y-4">
                <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Asistencias Registradas
                    <span class="ml-auto text-sm text-slate-400">({{ $horario->asistencias->count() }} registros)</span>
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-800/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-slate-300">Fecha</th>
                                <th class="px-4 py-3 text-left text-slate-300">Estudiante</th>
                                <th class="px-4 py-3 text-left text-slate-300">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            @foreach($horario->asistencias->take(5) as $asistencia)
                                <tr class="hover:bg-slate-800/30">
                                    <td class="px-4 py-3 text-slate-300">
                                        {{ \Carbon\Carbon::parse($asistencia->fecha)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-300">
                                        {{ $asistencia->estudiante->name }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs rounded-full
                                            {{ $asistencia->estado === 'presente' ? 'bg-green-500/20 text-green-400' : '' }}
                                            {{ $asistencia->estado === 'ausente' ? 'bg-red-500/20 text-red-400' : '' }}
                                            {{ $asistencia->estado === 'tardanza' ? 'bg-amber-500/20 text-amber-400' : '' }}
                                            {{ $asistencia->estado === 'justificado' ? 'bg-blue-500/20 text-blue-400' : '' }}">
                                            {{ ucfirst($asistencia->estado) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
