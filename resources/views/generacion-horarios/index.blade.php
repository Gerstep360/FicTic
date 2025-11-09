<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                {{ __('Generación Automática de Horarios') }}
            </h2>
            @if(auth()->user()->can('generar_horario_auto') || auth()->user()->hasRole('Admin DTIC'))
                <a href="{{ route('generacion-horarios.create') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Generación
                </a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Filtros --}}
        <div class="card p-6">
            <form method="GET" action="{{ route('generacion-horarios.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="id_gestion" class="block text-sm font-medium text-slate-300 mb-1">Gestión</label>
                    <select name="id_gestion" id="id_gestion" class="input w-full">
                        <option value="">Todas</option>
                        @foreach($gestiones as $gestion)
                            <option value="{{ $gestion->id_gestion }}" {{ request('id_gestion') == $gestion->id_gestion ? 'selected' : '' }}>
                                {{ $gestion->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="id_carrera" class="block text-sm font-medium text-slate-300 mb-1">Carrera</label>
                    <select name="id_carrera" id="id_carrera" class="input w-full">
                        <option value="">Todas</option>
                        @foreach($carreras as $carrera)
                            <option value="{{ $carrera->id_carrera }}" {{ request('id_carrera') == $carrera->id_carrera ? 'selected' : '' }}>
                                {{ $carrera->nombre_carrera }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="estado" class="block text-sm font-medium text-slate-300 mb-1">Estado</label>
                    <select name="estado" id="estado" class="input w-full">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="procesando" {{ request('estado') == 'procesando' ? 'selected' : '' }}>Procesando</option>
                        <option value="completado" {{ request('estado') == 'completado' ? 'selected' : '' }}>Completado</option>
                        <option value="error" {{ request('estado') == 'error' ? 'selected' : '' }}>Error</option>
                        <option value="aplicado" {{ request('estado') == 'aplicado' ? 'selected' : '' }}>Aplicado</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="btn-primary w-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filtrar
                    </button>
                </div>
            </form>
        </div>

        {{-- Lista de generaciones --}}
        @if($generaciones->isEmpty())
            <div class="card p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h3 class="text-lg font-medium text-slate-300 mb-2">No hay generaciones de horarios</h3>
                <p class="text-slate-400 mb-6">Comienza creando una nueva generación automática</p>
                @if(auth()->user()->can('generar_horario_auto') || auth()->user()->hasRole('Admin DTIC'))
                    <a href="{{ route('generacion-horarios.create') }}" class="btn-primary inline-flex">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nueva Generación
                    </a>
                @endif
            </div>
        @else
            <div class="space-y-4">
                @foreach($generaciones as $generacion)
                    <div class="card p-6 hover:border-slate-600 transition">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-semibold text-slate-200">
                                        {{ $generacion->gestion->nombre }}
                                        @if($generacion->carrera)
                                            - {{ $generacion->carrera->nombre_carrera }}
                                        @else
                                            - Toda la Facultad
                                        @endif
                                    </h3>
                                    
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $generacion->estado === 'completado' ? 'bg-green-500/20 text-green-400' : '' }}
                                        {{ $generacion->estado === 'aplicado' ? 'bg-blue-500/20 text-blue-400' : '' }}
                                        {{ $generacion->estado === 'error' ? 'bg-red-500/20 text-red-400' : '' }}
                                        {{ $generacion->estado === 'procesando' ? 'bg-amber-500/20 text-amber-400' : '' }}
                                        {{ $generacion->estado === 'pendiente' ? 'bg-slate-500/20 text-slate-400' : '' }}">
                                        {{ ucfirst($generacion->estado) }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-slate-400 mb-3">
                                    <div>
                                        <span class="block text-xs text-slate-500">Generado por</span>
                                        <span class="text-slate-300">{{ $generacion->usuario->name }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-xs text-slate-500">Fecha</span>
                                        <span class="text-slate-300">{{ $generacion->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-xs text-slate-500">Grupos asignados</span>
                                        <span class="text-slate-300">{{ $generacion->grupos_asignados }}/{{ $generacion->total_grupos }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-xs text-slate-500">Puntuación</span>
                                        <span class="text-slate-300">{{ $generacion->puntuacion_optimizacion ?? 'N/A' }}/100</span>
                                    </div>
                                </div>

                                @if($generacion->mensaje)
                                    <p class="text-sm text-slate-400 italic">{{ $generacion->mensaje }}</p>
                                @endif

                                @if($generacion->duracion_segundos)
                                    <p class="text-xs text-slate-500 mt-2">
                                        Duración: {{ $generacion->duracion_segundos }}s
                                    </p>
                                @endif
                            </div>

                            <div class="flex flex-col gap-2">
                                <a href="{{ route('generacion-horarios.show', $generacion) }}" class="btn-secondary text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Ver
                                </a>

                                @if($generacion->puede_aplicarse && (auth()->user()->can('generar_horario_auto') || auth()->user()->hasRole('Admin DTIC')))
                                    <form method="POST" action="{{ route('generacion-horarios.aplicar', $generacion) }}"
                                          onsubmit="return confirm('¿Aplicar estos horarios? Esto eliminará los horarios actuales de esta gestión/carrera.');">
                                        @csrf
                                        <button type="submit" class="btn-primary text-sm w-full">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Aplicar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Paginación --}}
            <div class="mt-6">
                {{ $generaciones->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
