<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalle de Reprogramación') }}
            </h2>
            <a href="{{ route('reprogramaciones.index') }}" class="btn-ghost gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-4 rounded" role="alert">
                    <p class="font-bold">Éxito</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 rounded" role="alert">
                    <p class="font-bold">Error</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            {{-- Información General --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                
                {{-- Estado y Tipo --}}
                <div class="card">
                    <div class="p-6 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border-b border-blue-200 dark:border-blue-700">
                        <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-300 mb-4">Estado de la Solicitud</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Estado:</span>
                                @if($reprogramacion->estado === 'PENDIENTE')
                                    <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300">Pendiente</span>
                                @elseif($reprogramacion->estado === 'APROBADA')
                                    <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">Aprobada</span>
                                @else
                                    <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">Rechazada</span>
                                @endif
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Tipo:</span>
                                @if($reprogramacion->tipo === 'CAMBIO_AULA')
                                    <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300">Cambio de Aula</span>
                                @elseif($reprogramacion->tipo === 'CAMBIO_FECHA')
                                    <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">Cambio de Fecha</span>
                                @else
                                    <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300">Cambio de Aula y Fecha</span>
                                @endif
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Fecha Solicitud:</span>
                                <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $reprogramacion->fecha_solicitud->format('d/m/Y H:i') }}</span>
                            </div>
                            @if($reprogramacion->fecha_aprobacion)
                                <div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Fecha Resolución:</span>
                                    <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $reprogramacion->fecha_aprobacion->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Clase Original --}}
                <div class="card">
                    <div class="p-6 bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 border-b border-purple-200 dark:border-purple-700">
                        <h3 class="text-lg font-semibold text-purple-900 dark:text-purple-300 mb-4">Clase Original</h3>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Materia:</span>
                                <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $reprogramacion->horarioOriginal->grupo->materia->nombre ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Grupo:</span>
                                <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $reprogramacion->horarioOriginal->grupo->nombre_grupo ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Aula Original:</span>
                                <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $reprogramacion->horarioOriginal->aula->codigo ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Día:</span>
                                <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'][$reprogramacion->horarioOriginal->dia_semana] ?? 'N/A' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Horario:</span>
                                <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $reprogramacion->horarioOriginal->bloque->hora_inicio ?? 'N/A' }} - {{ $reprogramacion->horarioOriginal->bloque->hora_fin ?? 'N/A' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Fecha Original:</span>
                                <span class="ml-2 text-sm font-semibold text-red-600 dark:text-red-400">{{ $reprogramacion->fecha_original->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cambios Solicitados --}}
            <div class="card mb-6">
                <div class="p-6 bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border-b border-green-200 dark:border-green-700">
                    <h3 class="text-lg font-semibold text-green-900 dark:text-green-300 mb-4">Cambios Solicitados</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if(in_array($reprogramacion->tipo, ['CAMBIO_AULA', 'AMBOS']))
                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-green-200 dark:border-green-700">
                                <h4 class="font-semibold text-green-800 dark:text-green-400 mb-2">Nueva Aula</h4>
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $reprogramacion->aulaNueva->codigo ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Tipo: {{ $reprogramacion->aulaNueva->tipo ?? 'N/A' }}<br>
                                    Capacidad: {{ $reprogramacion->aulaNueva->capacidad ?? 'N/A' }}<br>
                                    Edificio: {{ $reprogramacion->aulaNueva->edificio ?? 'N/A' }}
                                </div>
                            </div>
                        @endif

                        @if(in_array($reprogramacion->tipo, ['CAMBIO_FECHA', 'AMBOS']))
                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-green-200 dark:border-green-700">
                                <h4 class="font-semibold text-green-800 dark:text-green-400 mb-2">Nueva Fecha</h4>
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $reprogramacion->fecha_nueva?->format('d/m/Y') ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ $reprogramacion->fecha_nueva?->format('l') ?? '' }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Motivo y Observaciones --}}
            <div class="card mb-6">
                <div class="p-6 bg-gradient-to-r from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 border-b border-yellow-200 dark:border-yellow-700">
                    <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-300 mb-4">Motivo de la Reprogramación</h3>
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $reprogramacion->motivo }}</p>
                    
                    @if($reprogramacion->observaciones)
                        <div class="mt-4 pt-4 border-t border-yellow-300 dark:border-yellow-700">
                            <h4 class="font-semibold text-yellow-800 dark:text-yellow-400 mb-2">Observaciones</h4>
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $reprogramacion->observaciones }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Información de Usuarios --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                
                {{-- Solicitante --}}
                <div class="card">
                    <div class="p-6 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Solicitado por</h3>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Nombre:</span>
                                <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $reprogramacion->solicitante->name ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Email:</span>
                                <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $reprogramacion->solicitante->email ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Aprobador --}}
                @if($reprogramacion->aprobador)
                    <div class="card">
                        <div class="p-6 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                {{ $reprogramacion->estado === 'APROBADA' ? 'Aprobado por' : 'Rechazado por' }}
                            </h3>
                            <div class="space-y-2">
                                <div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Nombre:</span>
                                    <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $reprogramacion->aprobador->name ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Email:</span>
                                    <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">{{ $reprogramacion->aprobador->email ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Acciones --}}
            <div class="card">
                <div class="p-6 flex gap-3 justify-end">
                    <a href="{{ route('reprogramaciones.index') }}" class="btn-ghost gap-2">
                        Volver al Listado
                    </a>

                    @can('aprobar', $reprogramacion)
                        @if($reprogramacion->isPendiente())
                            <form action="{{ route('reprogramaciones.aprobar', $reprogramacion) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow transition" onclick="return confirm('¿Aprobar esta reprogramación?');">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Aprobar
                                </button>
                            </form>

                            <button type="button" onclick="document.getElementById('rechazarModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Rechazar
                            </button>
                        @endif
                    @endcan

                    @can('delete', $reprogramacion)
                        @if($reprogramacion->isPendiente())
                            <form action="{{ route('reprogramaciones.destroy', $reprogramacion) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta reprogramación?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Eliminar
                                </button>
                            </form>
                        @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Rechazar --}}
    <div id="rechazarModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border border-gray-200 dark:border-gray-700 w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">Rechazar Reprogramación</h3>
                <form action="{{ route('reprogramaciones.rechazar', $reprogramacion) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Motivo del Rechazo *</label>
                        <textarea name="observaciones" id="observaciones" rows="4" required class="input" placeholder="Explique por qué se rechaza esta reprogramación..."></textarea>
                    </div>
                    <div class="flex gap-2 justify-end">
                        <button type="button" onclick="document.getElementById('rechazarModal').classList.add('hidden')" class="btn-ghost">
                            Cancelar
                        </button>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg">
                            Confirmar Rechazo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
