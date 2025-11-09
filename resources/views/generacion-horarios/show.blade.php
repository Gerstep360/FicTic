<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('generacion-horarios.index') }}" class="text-slate-400 hover:text-slate-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                    {{ __('Detalle de Generación #') }}{{ $generacionHorario->id_generacion }}
                </h2>
            </div>

            <div class="flex items-center gap-2">
                @if($generacionHorario->es_completado || $generacionHorario->es_aplicado)
                    <a href="{{ route('generacion-horarios.pdf', $generacionHorario) }}" class="btn-secondary" target="_blank">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Descargar PDF
                    </a>
                @endif

                @if($generacionHorario->puede_aplicarse && (auth()->user()->can('generar_horario_auto') || auth()->user()->hasRole('Admin DTIC')))
                    <form method="POST" action="{{ route('generacion-horarios.aplicar', $generacionHorario) }}"
                          onsubmit="return confirm('¿Está seguro de aplicar estos horarios? Esto eliminará los horarios actuales de esta gestión/carrera y los reemplazará con los generados.');">
                        @csrf
                        <button type="submit" class="btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Aplicar Horarios
                        </button>
                    </form>
                @endif

                @if(!$generacionHorario->es_aplicado && (auth()->user()->can('generar_horario_auto') || auth()->user()->hasRole('Admin DTIC')))
                    <form method="POST" action="{{ route('generacion-horarios.destroy', $generacionHorario) }}"
                          onsubmit="return confirm('¿Eliminar esta generación? Esta acción no se puede deshacer.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn bg-red-600 hover:bg-red-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Eliminar
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Información general --}}
        <div class="card p-6">
            <div class="flex items-start gap-6">
                <div class="p-4 rounded-xl border
                    {{ $generacionHorario->estado === 'completado' ? 'bg-green-500/10 border-green-500/20' : '' }}
                    {{ $generacionHorario->estado === 'aplicado' ? 'bg-blue-500/10 border-blue-500/20' : '' }}
                    {{ $generacionHorario->estado === 'error' ? 'bg-red-500/10 border-red-500/20' : '' }}
                    {{ $generacionHorario->estado === 'procesando' ? 'bg-amber-500/10 border-amber-500/20' : '' }}">
                    <svg class="w-12 h-12
                        {{ $generacionHorario->estado === 'completado' ? 'text-green-400' : '' }}
                        {{ $generacionHorario->estado === 'aplicado' ? 'text-blue-400' : '' }}
                        {{ $generacionHorario->estado === 'error' ? 'text-red-400' : '' }}
                        {{ $generacionHorario->estado === 'procesando' ? 'text-amber-400' : '' }}" 
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>

                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <h3 class="text-2xl font-bold text-slate-200">
                            {{ $generacionHorario->gestion->nombre }}
                        </h3>
                        <span class="px-3 py-1 text-sm rounded-full
                            {{ $generacionHorario->estado === 'completado' ? 'bg-green-500/20 text-green-400' : '' }}
                            {{ $generacionHorario->estado === 'aplicado' ? 'bg-blue-500/20 text-blue-400' : '' }}
                            {{ $generacionHorario->estado === 'error' ? 'bg-red-500/20 text-red-400' : '' }}
                            {{ $generacionHorario->estado === 'procesando' ? 'bg-amber-500/20 text-amber-400' : '' }}
                            {{ $generacionHorario->estado === 'pendiente' ? 'bg-slate-500/20 text-slate-400' : '' }}">
                            {{ ucfirst($generacionHorario->estado) }}
                        </span>
                    </div>

                    <p class="text-slate-400 mb-4">
                        {{ $generacionHorario->alcance_texto }}
                    </p>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="p-3 bg-slate-800/30 rounded-lg">
                            <p class="text-xs text-slate-500 mb-1">Generado por</p>
                            <p class="font-medium text-slate-200">{{ $generacionHorario->usuario->name }}</p>
                        </div>
                        <div class="p-3 bg-slate-800/30 rounded-lg">
                            <p class="text-xs text-slate-500 mb-1">Fecha</p>
                            <p class="font-medium text-slate-200">{{ $generacionHorario->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="p-3 bg-slate-800/30 rounded-lg">
                            <p class="text-xs text-slate-500 mb-1">Duración</p>
                            <p class="font-medium text-slate-200">
                                {{ $generacionHorario->duracion_segundos ? $generacionHorario->duracion_segundos . 's' : 'N/A' }}
                            </p>
                        </div>
                        <div class="p-3 bg-slate-800/30 rounded-lg">
                            <p class="text-xs text-slate-500 mb-1">Puntuación</p>
                            <p class="font-medium text-slate-200">
                                {{ $generacionHorario->puntuacion_optimizacion ?? 'N/A' }}/100
                            </p>
                        </div>
                    </div>

                    @if($generacionHorario->mensaje)
                        <div class="mt-4 p-3 bg-slate-800/50 rounded-lg">
                            <p class="text-sm text-slate-300">{{ $generacionHorario->mensaje }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Métricas --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card p-6">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-blue-500/10 rounded-xl border border-blue-500/20">
                        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-200">{{ $generacionHorario->total_grupos }}</p>
                        <p class="text-sm text-slate-400">Total Grupos</p>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-green-500/10 rounded-xl border border-green-500/20">
                        <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-200">{{ $generacionHorario->grupos_asignados }}</p>
                        <p class="text-sm text-slate-400">Grupos Asignados</p>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-purple-500/10 rounded-xl border border-purple-500/20">
                        <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-200">{{ $generacionHorario->porcentaje_exito }}%</p>
                        <p class="text-sm text-slate-400">Tasa de Éxito</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Matriz de horarios --}}
        @if(!empty($matriz))
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-slate-200 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Vista Previa del Horario Generado
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-800/50">
                                <th class="border border-slate-700/50 px-3 py-2 text-slate-300 text-left sticky left-0 bg-slate-800/50 z-10">
                                    Día
                                </th>
                                @foreach($bloques as $bloque)
                                    <th class="border border-slate-700/50 px-3 py-2 text-slate-300 min-w-[200px]">
                                        <div class="font-medium">{{ $bloque->etiqueta ?? "Bloque {$bloque->id_bloque}" }}</div>
                                        <div class="text-xs text-slate-400 font-normal">
                                            {{ \Carbon\Carbon::parse($bloque->hora_inicio)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($bloque->hora_fin)->format('H:i') }}
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach([1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado'] as $dia => $nombreDia)
                                <tr class="hover:bg-slate-800/30 transition">
                                    <td class="border border-slate-700/50 px-3 py-2 font-medium text-slate-200 sticky left-0 bg-slate-900/90 z-10">
                                        {{ $nombreDia }}
                                    </td>
                                    @foreach($bloques as $bloque)
                                        <td class="border border-slate-700/50 p-2">
                                            @if(isset($matriz[$dia]['bloques'][$bloque->id_bloque]))
                                                <div class="space-y-2">
                                                    @foreach($matriz[$dia]['bloques'][$bloque->id_bloque] as $asignacion)
                                                        <div class="p-2 bg-gradient-to-br from-blue-500/20 to-blue-600/10 border border-blue-500/30 rounded hover:from-blue-500/30 hover:to-blue-600/20 transition">
                                                            <div class="font-medium text-slate-200 text-xs mb-1">
                                                                {{ $asignacion['materia'] }}
                                                            </div>
                                                            <div class="text-xs text-slate-400 space-y-0.5">
                                                                <div>Grupo: {{ $asignacion['nombre_grupo'] }}</div>
                                                                <div>Doc: {{ $asignacion['docente'] }}</div>
                                                                <div>Aula: {{ $asignacion['aula_codigo'] }}</div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="h-20 flex items-center justify-center text-slate-600 text-xs border-2 border-dashed border-slate-800 rounded">
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
        @else
            <div class="card p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-medium text-slate-300 mb-2">No hay horarios generados</h3>
                <p class="text-slate-400">Esta generación no tiene resultados para mostrar</p>
            </div>
        @endif

        {{-- Configuración utilizada --}}
        @if($generacionHorario->configuracion)
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-slate-200 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Configuración Utilizada
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    @foreach($generacionHorario->configuracion as $key => $value)
                        <div class="flex items-center gap-3 p-3 bg-slate-800/30 rounded-lg">
                            @if(is_bool($value))
                                @if($value)
                                    <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-slate-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                            @else
                                <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                            <div class="flex-1">
                                <div class="text-slate-400">{{ ucwords(str_replace('_', ' ', $key)) }}</div>
                                <div class="text-slate-200 font-medium">
                                    @if(is_bool($value))
                                        {{ $value ? 'Sí' : 'No' }}
                                    @elseif(is_array($value))
                                        {{ implode(', ', $value) }}
                                    @else
                                        {{ $value }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
