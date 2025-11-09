<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalle de Justificación') }}
            </h2>
            <a href="{{ route('justificaciones.index') }}" class="btn-ghost gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
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

            {{-- Estado y Tipo --}}
            <div class="card mb-6">
                <div class="p-6 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border-b border-blue-200 dark:border-blue-700">
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">Estado y Tipo de Justificación</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm text-blue-700 dark:text-blue-300 font-medium">Estado:</span>
                            <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full 
                                @if($justificacion->estado == 'PENDIENTE') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300
                                @elseif($justificacion->estado == 'APROBADA') bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300
                                @elseif($justificacion->estado == 'RECHAZADA') bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300
                                @endif">
                                {{ $justificacion->estado }}
                            </span>
                        </div>
                        <div>
                            <span class="text-sm text-blue-700 dark:text-blue-300 font-medium">Tipo:</span>
                            <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full
                                @if($justificacion->tipo == 'ENFERMEDAD') bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300
                                @elseif($justificacion->tipo == 'EMERGENCIA') bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300
                                @elseif($justificacion->tipo == 'TRAMITE') bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                @endif">
                                {{ $justificacion->tipo }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Información del Docente --}}
            <div class="card mb-6">
                <div class="p-6 bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 border-b border-purple-200 dark:border-purple-700">
                    <h3 class="text-lg font-semibold text-purple-900 dark:text-purple-100 mb-4">Información del Docente</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-purple-700 dark:text-purple-300"><strong>Nombre:</strong> {{ $justificacion->docente->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-purple-700 dark:text-purple-300"><strong>Email:</strong> {{ $justificacion->docente->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detalles de la Clase --}}
            <div class="card mb-6">
                <div class="p-6 bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border-b border-green-200 dark:border-green-700">
                    <h3 class="text-lg font-semibold text-green-900 dark:text-green-100 mb-4">Detalles de la Clase</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-green-700 dark:text-green-300"><strong>Fecha de Clase:</strong> {{ \Carbon\Carbon::parse($justificacion->fecha_clase)->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-green-700 dark:text-green-300"><strong>Fecha de Solicitud:</strong> {{ \Carbon\Carbon::parse($justificacion->fecha_solicitud)->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Motivo --}}
            <div class="card mb-6">
                <div class="p-6 bg-gradient-to-r from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 border-b border-yellow-200 dark:border-yellow-700">
                    <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-4">Motivo de la Justificación</h3>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300 whitespace-pre-wrap">{{ $justificacion->motivo }}</p>
                </div>
            </div>

            {{-- Documento Adjunto --}}
            @if($justificacion->documento_ruta)
                <div class="card mb-6">
                    <div class="p-6 bg-gradient-to-r from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 border-b border-indigo-200 dark:border-indigo-700">
                        <h3 class="text-lg font-semibold text-indigo-900 dark:text-indigo-100 mb-4">Documento Adjunto</h3>
                        <a href="{{ Storage::url($justificacion->documento_ruta) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z"/>
                            </svg>
                            Descargar Documento
                        </a>
                    </div>
                </div>
            @endif

            {{-- Resolución --}}
            @if($justificacion->estado != 'PENDIENTE')
                <div class="card mb-6">
                    <div class="p-6 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Resolución</h3>
                        <div class="space-y-3">
                            @if($justificacion->observaciones)
                                <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Observaciones:</strong> {{ $justificacion->observaciones }}</p>
                            @endif
                            <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Resuelto por:</strong> {{ $justificacion->resolutor->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Fecha de Resolución:</strong> {{ $justificacion->fecha_resolucion ? \Carbon\Carbon::parse($justificacion->fecha_resolucion)->format('d/m/Y H:i') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Botones de Acción --}}
            @if($justificacion->estado == 'PENDIENTE')
                @can('aprobar', $justificacion)
                    <div class="card mb-6">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Acciones</h3>
                            <div class="flex gap-4">
                                <form action="{{ route('justificaciones.aprobar', $justificacion->id_justificacion) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow transition">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Aprobar
                                    </button>
                                </form>
                                
                                <button onclick="document.getElementById('rechazarModal').classList.remove('hidden')" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Rechazar
                                </button>
                            </div>
                        </div>
                    </div>
                @endcan
            @endif
        </div>
    </div>

    {{-- Modal para Rechazar --}}
    <div id="rechazarModal" class="hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" onclick="document.getElementById('rechazarModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('justificaciones.rechazar', $justificacion->id_justificacion) }}" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">Rechazar Justificación</h3>
                        <div class="mb-4">
                            <label for="observaciones" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Observaciones (requeridas)</label>
                            <textarea name="observaciones" id="observaciones" rows="4" required class="input" placeholder="Explique el motivo del rechazo..."></textarea>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Rechazar
                        </button>
                        <button type="button" onclick="document.getElementById('rechazarModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
