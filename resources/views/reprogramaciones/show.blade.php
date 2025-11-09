<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalle de Reprogramación') }}
            </h2>
            <a href="{{ route('reprogramaciones.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
                    <p class="font-bold">Éxito</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
                    <p class="font-bold">Error</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            {{-- Información General --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                
                {{-- Estado y Tipo --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-blue-50 border-b border-blue-200">
                        <h3 class="text-lg font-semibold text-blue-900 mb-4">Estado de la Solicitud</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm text-gray-600">Estado:</span>
                                @if($reprogramacion->estado === 'PENDIENTE')
                                    <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                                @elseif($reprogramacion->estado === 'APROBADA')
                                    <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Aprobada</span>
                                @else
                                    <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Rechazada</span>
                                @endif
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Tipo:</span>
                                @if($reprogramacion->tipo === 'CAMBIO_AULA')
                                    <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">Cambio de Aula</span>
                                @elseif($reprogramacion->tipo === 'CAMBIO_FECHA')
                                    <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">Cambio de Fecha</span>
                                @else
                                    <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-indigo-100 text-indigo-800">Cambio de Aula y Fecha</span>
                                @endif
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Fecha Solicitud:</span>
                                <span class="ml-2 text-sm font-medium text-gray-900">{{ $reprogramacion->fecha_solicitud->format('d/m/Y H:i') }}</span>
                            </div>
                            @if($reprogramacion->fecha_aprobacion)
                                <div>
                                    <span class="text-sm text-gray-600">Fecha Resolución:</span>
                                    <span class="ml-2 text-sm font-medium text-gray-900">{{ $reprogramacion->fecha_aprobacion->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Clase Original --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-purple-50 border-b border-purple-200">
                        <h3 class="text-lg font-semibold text-purple-900 mb-4">Clase Original</h3>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm text-gray-600">Materia:</span>
                                <span class="ml-2 text-sm font-medium text-gray-900">{{ $reprogramacion->horarioOriginal->grupo->materia->nombre ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Grupo:</span>
                                <span class="ml-2 text-sm font-medium text-gray-900">{{ $reprogramacion->horarioOriginal->grupo->nombre_grupo ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Aula Original:</span>
                                <span class="ml-2 text-sm font-medium text-gray-900">{{ $reprogramacion->horarioOriginal->aula->codigo ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Día:</span>
                                <span class="ml-2 text-sm font-medium text-gray-900">
                                    {{ ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'][$reprogramacion->horarioOriginal->dia_semana] ?? 'N/A' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Horario:</span>
                                <span class="ml-2 text-sm font-medium text-gray-900">
                                    {{ $reprogramacion->horarioOriginal->bloque->hora_inicio ?? 'N/A' }} - {{ $reprogramacion->horarioOriginal->bloque->hora_fin ?? 'N/A' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Fecha Original:</span>
                                <span class="ml-2 text-sm font-semibold text-red-600">{{ $reprogramacion->fecha_original->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cambios Solicitados --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-green-50 border-b border-green-200">
                    <h3 class="text-lg font-semibold text-green-900 mb-4">Cambios Solicitados</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if(in_array($reprogramacion->tipo, ['CAMBIO_AULA', 'AMBOS']))
                            <div class="bg-white p-4 rounded-lg border border-green-200">
                                <h4 class="font-semibold text-green-800 mb-2">Nueva Aula</h4>
                                <div class="text-2xl font-bold text-green-600">{{ $reprogramacion->aulaNueva->codigo ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-600 mt-1">
                                    Tipo: {{ $reprogramacion->aulaNueva->tipo ?? 'N/A' }}<br>
                                    Capacidad: {{ $reprogramacion->aulaNueva->capacidad ?? 'N/A' }}<br>
                                    Edificio: {{ $reprogramacion->aulaNueva->edificio ?? 'N/A' }}
                                </div>
                            </div>
                        @endif

                        @if(in_array($reprogramacion->tipo, ['CAMBIO_FECHA', 'AMBOS']))
                            <div class="bg-white p-4 rounded-lg border border-green-200">
                                <h4 class="font-semibold text-green-800 mb-2">Nueva Fecha</h4>
                                <div class="text-2xl font-bold text-green-600">{{ $reprogramacion->fecha_nueva?->format('d/m/Y') ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-600 mt-1">
                                    {{ $reprogramacion->fecha_nueva?->format('l') ?? '' }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Motivo y Observaciones --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-yellow-50 border-b border-yellow-200">
                    <h3 class="text-lg font-semibold text-yellow-900 mb-4">Motivo de la Reprogramación</h3>
                    <p class="text-gray-700 whitespace-pre-line">{{ $reprogramacion->motivo }}</p>
                    
                    @if($reprogramacion->observaciones)
                        <div class="mt-4 pt-4 border-t border-yellow-300">
                            <h4 class="font-semibold text-yellow-800 mb-2">Observaciones</h4>
                            <p class="text-gray-700 whitespace-pre-line">{{ $reprogramacion->observaciones }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Información de Usuarios --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                
                {{-- Solicitante --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Solicitado por</h3>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm text-gray-600">Nombre:</span>
                                <span class="ml-2 text-sm font-medium text-gray-900">{{ $reprogramacion->solicitante->name ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Email:</span>
                                <span class="ml-2 text-sm text-gray-900">{{ $reprogramacion->solicitante->email ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Aprobador --}}
                @if($reprogramacion->aprobador)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                {{ $reprogramacion->estado === 'APROBADA' ? 'Aprobado por' : 'Rechazado por' }}
                            </h3>
                            <div class="space-y-2">
                                <div>
                                    <span class="text-sm text-gray-600">Nombre:</span>
                                    <span class="ml-2 text-sm font-medium text-gray-900">{{ $reprogramacion->aprobador->name ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600">Email:</span>
                                    <span class="ml-2 text-sm text-gray-900">{{ $reprogramacion->aprobador->email ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Acciones --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 flex gap-3 justify-end">
                    <a href="{{ route('reprogramaciones.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow transition">
                        Volver al Listado
                    </a>

                    @can('aprobar', $reprogramacion)
                        @if($reprogramacion->isPendiente())
                            <form action="{{ route('reprogramaciones.aprobar', $reprogramacion) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow transition" onclick="return confirm('¿Aprobar esta reprogramación?');">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Aprobar
                                </button>
                            </form>

                            <button type="button" onclick="document.getElementById('rechazarModal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Rechazar Reprogramación</h3>
                <form action="{{ route('reprogramaciones.rechazar', $reprogramacion) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">Motivo del Rechazo *</label>
                        <textarea name="observaciones" id="observaciones" rows="4" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Explique por qué se rechaza esta reprogramación..."></textarea>
                    </div>
                    <div class="flex gap-2 justify-end">
                        <button type="button" onclick="document.getElementById('rechazarModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-lg">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg">
                            Confirmar Rechazo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
