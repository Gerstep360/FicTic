<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                    Detalle de Aprobación
                </h2>
                <p class="text-sm text-slate-400 mt-1">
                    {{ $aprobacion->gestion->nombre }} - {{ $aprobacion->alcance_texto }}
                </p>
            </div>
            <div class="flex gap-2">
                @if($aprobacion->puede_enviar_director)
                    <form action="{{ route('aprobaciones.enviar-director', $aprobacion->id_aprobacion) }}" method="POST">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('¿Enviar al Director para aprobación?')"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                            Enviar a Director
                        </button>
                    </form>
                @endif
                <a href="{{ route('aprobaciones.index') }}"
                   class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition">
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-green-900/50 border border-green-700 text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Columna principal -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Tarjetas de métricas -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                            <p class="text-sm text-slate-400 mb-1">Total Horarios</p>
                            <p class="text-2xl font-bold text-slate-200">{{ $aprobacion->total_horarios }}</p>
                        </div>
                        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                            <p class="text-sm text-slate-400 mb-1">Validados</p>
                            <p class="text-2xl font-bold text-green-400">{{ $aprobacion->horarios_validados }}</p>
                        </div>
                        <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                            <p class="text-sm text-slate-400 mb-1">Conflictos</p>
                            <p class="text-2xl font-bold {{ $aprobacion->conflictos_pendientes > 0 ? 'text-red-400' : 'text-green-400' }}">
                                {{ $aprobacion->conflictos_pendientes }}
                            </p>
                        </div>
                    </div>

                    <!-- Timeline del flujo -->
                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6">
                        <h3 class="text-lg font-semibold text-slate-200 mb-4">Flujo de Aprobación</h3>
                        
                        <div class="space-y-6">
                            <!-- Borrador/Coordinador -->
                            <div class="flex gap-4">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full {{ in_array($aprobacion->estado, ['borrador', 'observado_director', 'observado_decano']) ? 'bg-blue-600' : 'bg-green-600' }} flex items-center justify-center">
                                        @if(in_array($aprobacion->estado, ['borrador', 'observado_director', 'observado_decano']))
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-slate-200">Elaboración - Coordinador</p>
                                    <p class="text-xs text-slate-400 mt-1">
                                        @if($aprobacion->coordinador)
                                            Por: {{ $aprobacion->coordinador->name }}
                                        @endif
                                        @if($aprobacion->created_at)
                                            - {{ $aprobacion->created_at->format('d/m/Y H:i') }}
                                        @endif
                                    </p>
                                    @if($aprobacion->observaciones_coordinador && $aprobacion->estado === 'borrador')
                                        <div class="mt-2 bg-blue-900/20 border border-blue-700/50 rounded px-3 py-2">
                                            <p class="text-sm text-blue-300">{{ $aprobacion->observaciones_coordinador }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Línea conectora -->
                            @if(in_array($aprobacion->estado, ['pendiente_director', 'observado_director', 'aprobado_director', 'pendiente_decano', 'observado_decano', 'aprobado_final']))
                                <div class="flex gap-4">
                                    <div class="flex-shrink-0 w-10 flex justify-center">
                                        <div class="w-0.5 h-8 bg-slate-600"></div>
                                    </div>
                                </div>
                            @endif

                            <!-- Director -->
                            @if(in_array($aprobacion->estado, ['pendiente_director', 'observado_director', 'aprobado_director', 'pendiente_decano', 'observado_decano', 'aprobado_final']))
                                <div class="flex gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full {{ $aprobacion->estado === 'pendiente_director' ? 'bg-yellow-600' : ($aprobacion->estado === 'observado_director' ? 'bg-orange-600' : 'bg-green-600') }} flex items-center justify-center">
                                            @if($aprobacion->estado === 'pendiente_director')
                                                <svg class="w-5 h-5 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @elseif($aprobacion->estado === 'observado_director')
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-slate-200">
                                            Revisión - Director de Carrera
                                            @if($aprobacion->estado === 'observado_director')
                                                <span class="text-orange-400">(Observado)</span>
                                            @elseif($aprobacion->estado === 'aprobado_director' || $aprobacion->estado === 'pendiente_decano' || $aprobacion->estado === 'aprobado_final')
                                                <span class="text-green-400">(Aprobado)</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-slate-400 mt-1">
                                            @if($aprobacion->director)
                                                Por: {{ $aprobacion->director->name }}
                                            @endif
                                            @if($aprobacion->fecha_respuesta_director)
                                                - {{ $aprobacion->fecha_respuesta_director->format('d/m/Y H:i') }}
                                            @elseif($aprobacion->fecha_envio_director)
                                                Enviado: {{ $aprobacion->fecha_envio_director->format('d/m/Y H:i') }}
                                            @endif
                                        </p>
                                        @if($aprobacion->observaciones_director)
                                            <div class="mt-2 bg-orange-900/20 border border-orange-700/50 rounded px-3 py-2">
                                                <p class="text-sm text-orange-300">{{ $aprobacion->observaciones_director }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Línea conectora -->
                            @if(in_array($aprobacion->estado, ['pendiente_decano', 'observado_decano', 'aprobado_final']))
                                <div class="flex gap-4">
                                    <div class="flex-shrink-0 w-10 flex justify-center">
                                        <div class="w-0.5 h-8 bg-slate-600"></div>
                                    </div>
                                </div>
                            @endif

                            <!-- Decano -->
                            @if(in_array($aprobacion->estado, ['pendiente_decano', 'observado_decano', 'aprobado_final']))
                                <div class="flex gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full {{ $aprobacion->estado === 'pendiente_decano' ? 'bg-yellow-600' : ($aprobacion->estado === 'observado_decano' ? 'bg-orange-600' : 'bg-green-600') }} flex items-center justify-center">
                                            @if($aprobacion->estado === 'pendiente_decano')
                                                <svg class="w-5 h-5 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @elseif($aprobacion->estado === 'observado_decano')
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-slate-200">
                                            Aprobación Final - Decano
                                            @if($aprobacion->estado === 'observado_decano')
                                                <span class="text-orange-400">(Observado)</span>
                                            @elseif($aprobacion->estado === 'aprobado_final')
                                                <span class="text-green-400">(Aprobado)</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-slate-400 mt-1">
                                            @if($aprobacion->decano)
                                                Por: {{ $aprobacion->decano->name }}
                                            @endif
                                            @if($aprobacion->fecha_respuesta_decano)
                                                - {{ $aprobacion->fecha_respuesta_decano->format('d/m/Y H:i') }}
                                            @elseif($aprobacion->fecha_envio_decano)
                                                Enviado: {{ $aprobacion->fecha_envio_decano->format('d/m/Y H:i') }}
                                            @endif
                                        </p>
                                        @if($aprobacion->observaciones_decano)
                                            <div class="mt-2 bg-orange-900/20 border border-orange-700/50 rounded px-3 py-2">
                                                <p class="text-sm text-orange-300">{{ $aprobacion->observaciones_decano }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Horarios -->
                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6" x-data="{ mostrarTodos: false }">
                        <h3 class="text-lg font-semibold text-slate-200 mb-4">
                            Horarios Asignados ({{ $horarios->count() }})
                        </h3>
                        
                        @if($horarios->isEmpty())
                            <p class="text-slate-400 text-center py-8">No hay horarios asignados aún.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-slate-700">
                                            <th class="text-left py-2 text-slate-300">Materia</th>
                                            <th class="text-left py-2 text-slate-300">Grupo</th>
                                            <th class="text-left py-2 text-slate-300">Docente</th>
                                            <th class="text-left py-2 text-slate-300">Aula</th>
                                            <th class="text-left py-2 text-slate-300">Horario</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-700">
                                        @foreach($horarios as $index => $horario)
                                            <tr class="hover:bg-slate-700/50" 
                                                x-show="mostrarTodos || {{ $index }} < 10"
                                                x-transition>
                                                <td class="py-2 text-slate-200">{{ $horario->grupo->materia->nombre ?? '-' }}</td>
                                                <td class="py-2 text-slate-300">{{ $horario->grupo->nombre_grupo ?? '-' }}</td>
                                                <td class="py-2 text-slate-300">{{ $horario->docente->name ?? 'Sin asignar' }}</td>
                                                <td class="py-2 text-slate-300">{{ $horario->aula->codigo ?? '-' }}</td>
                                                <td class="py-2 text-slate-300">
                                                    {{ ucfirst($horario->dia_semana) }} {{ $horario->bloque->hora_inicio ?? '' }} - {{ $horario->bloque->hora_fin ?? '' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if($horarios->count() > 10)
                                    <div class="mt-4 text-center">
                                        <button @click="mostrarTodos = !mostrarTodos"
                                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-sm">
                                            <span x-show="!mostrarTodos">📋 Ver todos los {{ $horarios->count() }} horarios</span>
                                            <span x-show="mostrarTodos" x-cloak>🔼 Mostrar menos</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Estado actual -->
                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6">
                        <h3 class="text-lg font-semibold text-slate-200 mb-4">Estado Actual</h3>
                        <div class="space-y-3">
                            <div class="px-4 py-3 rounded-lg {{ $aprobacion->color_estado }}">
                                <p class="text-center font-medium">
                                    {{ $aprobacion->icono_estado }} {{ $aprobacion->estado_texto }}
                                </p>
                            </div>
                            <div class="text-sm text-slate-400 text-center">
                                Actualizado {{ $aprobacion->tiempo_en_estado }}
                            </div>
                        </div>
                    </div>

                    <!-- Progreso -->
                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6">
                        <h3 class="text-lg font-semibold text-slate-200 mb-4">Progreso</h3>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-blue-400 mb-2">
                                {{ $aprobacion->porcentaje_progreso }}%
                            </div>
                            <div class="w-full bg-slate-700 rounded-full h-2 mb-4">
                                <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: {{ $aprobacion->porcentaje_progreso }}%"></div>
                            </div>
                            <p class="text-sm text-slate-400">
                                {{ $aprobacion->horarios_validados }} de {{ $aprobacion->total_horarios }} horarios
                            </p>
                        </div>
                    </div>

                    <!-- Acciones rápidas -->
                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6">
                        <h3 class="text-lg font-semibold text-slate-200 mb-4">Acciones</h3>
                        <div class="space-y-2">
                            <a href="{{ route('validacion-horarios.index') }}"
                               class="block px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg transition text-center text-sm">
                                Ejecutar Validación
                            </a>
                            <a href="{{ route('horarios.index') }}"
                               class="block px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg transition text-center text-sm">
                                Ver Horarios
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
