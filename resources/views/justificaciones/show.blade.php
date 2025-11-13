<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                📄 Detalle de Justificación
            </h2>
            <a href="{{ route('justificaciones.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-lg shadow-sm transition gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-4 rounded-lg shadow-sm" role="alert">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="font-semibold">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 rounded-lg shadow-sm" role="alert">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <p class="font-semibold">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            {{-- Estado y Tipo --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Estado y Tipo de Justificación
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Estado:</p>
                            @if($justificacion->estado == 'PENDIENTE')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-200 border border-yellow-200 dark:border-yellow-800">
                                    ⏳ PENDIENTE
                                </span>
                            @elseif($justificacion->estado == 'APROBADA')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200 border border-green-200 dark:border-green-800">
                                    ✅ APROBADA
                                </span>
                            @elseif($justificacion->estado == 'RECHAZADA')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200 border border-red-200 dark:border-red-800">
                                    ❌ RECHAZADA
                                </span>
                            @endif
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Tipo:</p>
                            @if($justificacion->tipo == 'ENFERMEDAD')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-200 border border-purple-200 dark:border-purple-800">
                                    🏥 ENFERMEDAD
                                </span>
                            @elseif($justificacion->tipo == 'EMERGENCIA')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200 border border-red-200 dark:border-red-800">
                                    🚨 EMERGENCIA
                                </span>
                            @elseif($justificacion->tipo == 'TRAMITE')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200 border border-blue-200 dark:border-blue-800">
                                    📄 TRÁMITE
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600">
                                    📋 {{ strtoupper($justificacion->tipo ?? 'OTRO') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Información del Docente --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-gray-800 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Información del Docente
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                {{ strtoupper(substr($justificacion->docente->name ?? 'N', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Nombre completo</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $justificacion->docente->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <div class="flex-shrink-0">
                                <svg class="w-10 h-10 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Correo electrónico</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $justificacion->docente->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detalles de la Clase --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-gray-800 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Detalles de la Clase
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Fecha de Clase:</p>
                            <p class="text-base font-bold text-gray-900 dark:text-gray-100">
                                {{ \Carbon\Carbon::parse($justificacion->fecha_clase)->format('d/m/Y') }}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Fecha de Solicitud:</p>
                            <p class="text-base font-bold text-gray-900 dark:text-gray-100">
                                {{ $justificacion->fecha_solicitud->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Motivo --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-orange-50 to-amber-50 dark:from-gray-800 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Motivo de la Justificación
                    </h3>
                </div>
                <div class="p-6">
                    <div class="bg-orange-50 dark:bg-orange-900/10 border-l-4 border-orange-400 dark:border-orange-600 rounded-r-lg p-4">
                        <p class="text-gray-800 dark:text-gray-200 leading-relaxed">
                            {{ $justificacion->motivo }}
                        </p>
                    </div>
                </div>
            </div>

            @if($justificacion->evidencia_url)
            {{-- Evidencia --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-gray-800 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        Evidencia Adjunta
                    </h3>
                </div>
                <div class="p-6">
                    <a href="{{ asset('storage/' . $justificacion->evidencia_url) }}" 
                       target="_blank" 
                       class="inline-flex items-center gap-3 px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md transition-all transform hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Ver/Descargar Evidencia
                    </a>
                </div>
            </div>
            @endif

            @if($justificacion->estado == 'RECHAZADA' && $justificacion->motivo_rechazo)
            {{-- Motivo de Rechazo --}}
            <div class="bg-red-50 dark:bg-red-900/20 rounded-xl shadow-sm border-2 border-red-300 dark:border-red-700 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-red-100 to-rose-100 dark:from-red-900/30 dark:to-rose-900/30 px-6 py-4 border-b border-red-200 dark:border-red-700">
                    <h3 class="text-lg font-semibold text-red-900 dark:text-red-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Motivo del Rechazo
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-red-800 dark:text-red-200 bg-red-100 dark:bg-red-900/30 border-l-4 border-red-600 rounded-r-lg p-4 font-medium">
                        {{ $justificacion->motivo_rechazo }}
                    </p>
                </div>
            </div>
            @endif

            {{-- Resolución --}}
            @if($justificacion->estado != 'PENDIENTE')
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-gray-50 to-slate-50 dark:from-gray-800 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Información de Resolución
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @if($justificacion->observaciones)
                        <div class="bg-blue-50 dark:bg-blue-900/10 border-l-4 border-blue-400 dark:border-blue-600 rounded-r-lg p-4">
                            <p class="text-xs font-semibold text-blue-700 dark:text-blue-300 mb-1">Observaciones:</p>
                            <p class="text-sm text-gray-800 dark:text-gray-200">{{ $justificacion->observaciones }}</p>
                        </div>
                        @endif
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Resuelto por:</p>
                                <p class="text-base font-bold text-gray-900 dark:text-gray-100">{{ $justificacion->resolutor->name ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Fecha de Resolución:</p>
                                <p class="text-base font-bold text-gray-900 dark:text-gray-100">
                                    {{ $justificacion->fecha_resolucion ? \Carbon\Carbon::parse($justificacion->fecha_resolucion)->format('d/m/Y H:i') : 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Botones de Acción --}}
            @if($justificacion->estado == 'PENDIENTE')
                @can('aprobar', $justificacion)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                    <div class="bg-gradient-to-r from-teal-50 to-cyan-50 dark:from-gray-800 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                            Acciones Disponibles
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <form action="{{ route('justificaciones.aprobar', $justificacion->id_justif) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition-all transform hover:scale-105">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Aprobar Justificación
                                </button>
                            </form>
                            
                            <button onclick="document.getElementById('rechazarModal').classList.remove('hidden')" 
                                    class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-md transition-all transform hover:scale-105">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Rechazar Justificación
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
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200 dark:border-gray-700">
                <form action="{{ route('justificaciones.rechazar', $justificacion->id_justif) }}" method="POST">
                    @csrf
                    <div class="bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 px-6 py-4 border-b border-red-200 dark:border-red-700">
                        <h3 class="text-lg leading-6 font-semibold text-red-900 dark:text-red-100 flex items-center gap-2">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Rechazar Justificación
                        </h3>
                    </div>
                    <div class="bg-white dark:bg-gray-800 px-6 py-6">
                        <div class="mb-4">
                            <label for="observaciones" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Motivo del rechazo <span class="text-red-500">*</span>
                            </label>
                            <textarea name="observaciones" 
                                      id="observaciones" 
                                      rows="5" 
                                      required 
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-gray-100 resize-none" 
                                      placeholder="Explique detalladamente por qué se rechaza esta justificación..."></textarea>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Este motivo será visible para el docente que solicitó la justificación.
                            </p>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <button type="button" 
                                onclick="document.getElementById('rechazarModal').classList.add('hidden')" 
                                class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-semibold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-5 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Confirmar Rechazo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
