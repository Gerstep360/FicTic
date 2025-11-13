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
            <div class="card mb-6">
                <div class="p-6">
                    
                    {{-- Header con Estado --}}
                    <div class="flex items-center justify-between mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Estado de la Solicitud</h3>
                            <div class="flex items-center gap-3">
                                @if($reprogramacion->estado === 'PENDIENTE')
                                    <span class="px-3 py-1 text-sm font-medium rounded bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">Pendiente</span>
                                @elseif($reprogramacion->estado === 'APROBADA')
                                    <span class="px-3 py-1 text-sm font-medium rounded bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Aprobada</span>
                                @else
                                    <span class="px-3 py-1 text-sm font-medium rounded bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">Rechazada</span>
                                @endif
                                
                                @if($reprogramacion->tipo === 'CAMBIO_AULA')
                                    <span class="px-3 py-1 text-sm rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">Cambio de Aula</span>
                                @elseif($reprogramacion->tipo === 'CAMBIO_FECHA')
                                    <span class="px-3 py-1 text-sm rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">Cambio de Fecha</span>
                                @else
                                    <span class="px-3 py-1 text-sm rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">Cambio de Aula y Fecha</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Solicitado el</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $reprogramacion->fecha_solicitud->format('d/m/Y H:i') }}</p>
                            @if($reprogramacion->fecha_aprobacion)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Resuelto el</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $reprogramacion->fecha_aprobacion->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Información de la Clase --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Materia</label>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $reprogramacion->horarioOriginal->grupo->materia->nombre ?? 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Grupo: {{ $reprogramacion->horarioOriginal->grupo->nombre_grupo ?? 'N/A' }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Horario Original</label>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'][$reprogramacion->horarioOriginal->dia_semana] ?? 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ $reprogramacion->horarioOriginal->bloque->hora_inicio ?? 'N/A' }} - {{ $reprogramacion->horarioOriginal->bloque->hora_fin ?? 'N/A' }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Aula Original</label>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $reprogramacion->horarioOriginal->aula->codigo ?? 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Edificio {{ $reprogramacion->horarioOriginal->aula->edificio ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700 my-6">

                    {{-- Cambios Solicitados --}}
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase mb-4">Cambios Solicitados</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            {{-- Fecha Original vs Nueva --}}
                            <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Fecha Original</p>
                                        <p class="text-base font-semibold text-red-600 dark:text-red-400 line-through">
                                            {{ $reprogramacion->fecha_original->format('d/m/Y') }}
                                        </p>
                                    </div>
                                    @if(in_array($reprogramacion->tipo, ['CAMBIO_FECHA', 'AMBOS']))
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                        </svg>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Nueva Fecha</p>
                                            <p class="text-base font-semibold text-green-600 dark:text-green-400">
                                                {{ $reprogramacion->fecha_nueva?->format('d/m/Y') ?? 'N/A' }}
                                            </p>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-500 dark:text-gray-400 italic">Sin cambio</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Aula Original vs Nueva --}}
                            <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Aula Original</p>
                                        <p class="text-base font-semibold text-red-600 dark:text-red-400 line-through">
                                            {{ $reprogramacion->horarioOriginal->aula->codigo ?? 'N/A' }}
                                        </p>
                                    </div>
                                    @if(in_array($reprogramacion->tipo, ['CAMBIO_AULA', 'AMBOS']))
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                        </svg>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Nueva Aula</p>
                                            <p class="text-base font-semibold text-green-600 dark:text-green-400">
                                                {{ $reprogramacion->aulaNueva->codigo ?? 'N/A' }}
                                            </p>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-500 dark:text-gray-400 italic">Sin cambio</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700 my-6">

                    {{-- Motivo --}}
                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Motivo</h3>
                        </div>
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $reprogramacion->motivo }}</p>
                    </div>

                    @if($reprogramacion->observaciones)
                        <div class="mb-6">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Observaciones</h3>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $reprogramacion->observaciones }}</p>
                        </div>
                    @endif

                    <hr class="border-gray-200 dark:border-gray-700 my-6">

                    {{-- Usuarios Involucrados --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Solicitado por</h3>
                            </div>
                            <p class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-1">
                                {{ $reprogramacion->solicitante->name ?? 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $reprogramacion->solicitante->email ?? 'N/A' }}
                            </p>
                        </div>

                        @if($reprogramacion->aprobador)
                            <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($reprogramacion->estado === 'APROBADA')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        @endif
                                    </svg>
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">
                                        {{ $reprogramacion->estado === 'APROBADA' ? 'Aprobado por' : 'Rechazado por' }}
                                    </h3>
                                </div>
                                <p class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-1">
                                    {{ $reprogramacion->aprobador->name ?? 'N/A' }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $reprogramacion->aprobador->email ?? 'N/A' }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="card">
                <div class="p-6 flex flex-wrap gap-3 justify-between items-center">
                    <a href="{{ route('reprogramaciones.index') }}" class="btn-ghost gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Volver
                    </a>

                    <div class="flex flex-wrap gap-3">
                        @can('aprobar', $reprogramacion)
                            @if($reprogramacion->isPendiente())
                                <form action="{{ route('reprogramaciones.aprobar', $reprogramacion) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="btn-primary gap-2 bg-green-600 hover:bg-green-700" onclick="return confirm('¿Aprobar esta reprogramación?');">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Aprobar
                                    </button>
                                </form>

                                <button type="button" onclick="document.getElementById('rechazarModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-800 text-white font-semibold rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    </div>

    {{-- Modal Rechazar con diseño mejorado --}}
    <div id="rechazarModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-2xl max-w-md w-full border border-gray-200 dark:border-gray-700">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Rechazar Reprogramación</h3>
                    <button type="button" onclick="document.getElementById('rechazarModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <form action="{{ route('reprogramaciones.rechazar', $reprogramacion) }}" method="POST">
                @csrf
                <div class="p-6">
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Motivo del Rechazo <span class="text-red-500">*</span>
                    </label>
                    <textarea name="observaciones" 
                              id="observaciones" 
                              rows="4" 
                              required 
                              class="input resize-none" 
                              placeholder="Explique por qué se rechaza esta reprogramación..."></textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Este mensaje será visible para el solicitante</p>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700 flex gap-3 justify-end rounded-b-lg">
                    <button type="button" 
                            onclick="document.getElementById('rechazarModal').classList.add('hidden')" 
                            class="btn-ghost">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Confirmar Rechazo
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
