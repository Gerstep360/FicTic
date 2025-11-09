<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mis Justificaciones') }}
            </h2>
            <a href="{{ route('justificaciones.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow transition duration-150">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Justificación
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

            {{-- Resumen de Estado --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gradient-to-r from-yellow-50 to-yellow-100 border-b border-yellow-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-yellow-900">Pendientes</p>
                                <p class="text-2xl font-bold text-yellow-700">{{ $justificaciones->where('estado', 'PENDIENTE')->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gradient-to-r from-green-50 to-green-100 border-b border-green-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-green-900">Aprobadas</p>
                                <p class="text-2xl font-bold text-green-700">{{ $justificaciones->where('estado', 'APROBADA')->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gradient-to-r from-red-50 to-red-100 border-b border-red-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-red-900">Rechazadas</p>
                                <p class="text-2xl font-bold text-red-700">{{ $justificaciones->where('estado', 'RECHAZADA')->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Listado --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700">
                        Historial de Justificaciones ({{ $justificaciones->count() }})
                    </h3>

                    @if($justificaciones->count() > 0)
                        <div class="space-y-4">
                            @foreach($justificaciones as $justificacion)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <h4 class="text-lg font-semibold text-gray-900">
                                                    {{ \Carbon\Carbon::parse($justificacion->fecha_clase)->format('d/m/Y') }}
                                                </h4>
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                    @if($justificacion->estado == 'PENDIENTE') bg-yellow-100 text-yellow-800
                                                    @elseif($justificacion->estado == 'APROBADA') bg-green-100 text-green-800
                                                    @elseif($justificacion->estado == 'RECHAZADA') bg-red-100 text-red-800
                                                    @endif">
                                                    {{ $justificacion->estado }}
                                                </span>
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                    @if($justificacion->tipo == 'ENFERMEDAD') bg-purple-100 text-purple-800
                                                    @elseif($justificacion->tipo == 'EMERGENCIA') bg-red-100 text-red-800
                                                    @elseif($justificacion->tipo == 'TRAMITE') bg-blue-100 text-blue-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ $justificacion->tipo }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-2">
                                                <strong>Motivo:</strong> {{ Str::limit($justificacion->motivo, 150) }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Solicitado: {{ \Carbon\Carbon::parse($justificacion->fecha_solicitud)->format('d/m/Y H:i') }}
                                            </p>
                                            @if($justificacion->estado != 'PENDIENTE' && $justificacion->observaciones)
                                                <div class="mt-2 p-2 bg-gray-50 rounded border-l-4 
                                                    @if($justificacion->estado == 'APROBADA') border-green-400
                                                    @else border-red-400
                                                    @endif">
                                                    <p class="text-xs text-gray-700">
                                                        <strong>Observaciones:</strong> {{ $justificacion->observaciones }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <a href="{{ route('justificaciones.show', $justificacion->id_justificacion) }}" 
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow transition">
                                                Ver Detalle
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay justificaciones</h3>
                            <p class="mt-1 text-sm text-gray-500">Aún no has solicitado ninguna justificación.</p>
                            <div class="mt-6">
                                <a href="{{ route('justificaciones.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Nueva Justificación
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
