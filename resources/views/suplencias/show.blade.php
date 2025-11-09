<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalle de Suplencia') }}
            </h2>
            <a href="{{ route('suplencias.index') }}" class="btn-ghost gap-2">
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
                <div class="mb-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-4 rounded">
                    <p class="font-bold">Éxito</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="card">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Información General --}}
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg border border-blue-200 dark:border-blue-700">
                            <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Información General
                            </h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de la Clase</dt>
                                    <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $suplencia->fecha_clase->format('d/m/Y') }}
                                        <span class="text-sm">({{ $suplencia->fecha_clase->translatedFormat('l') }})</span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado</dt>
                                    <dd class="mt-1">
                                        @if($suplencia->fecha_clase->isPast())
                                            <span class="px-3 py-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-sm font-medium">Pasada</span>
                                        @elseif($suplencia->fecha_clase->isToday())
                                            <span class="px-3 py-1 bg-blue-200 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 rounded-full text-sm font-medium">Hoy</span>
                                        @else
                                            <span class="px-3 py-1 bg-green-200 dark:bg-green-900/50 text-green-800 dark:text-green-300 rounded-full text-sm font-medium">Próxima</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registrado</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $suplencia->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>

                        {{-- Horario --}}
                        <div class="bg-purple-50 dark:bg-purple-900/20 p-6 rounded-lg border border-purple-200 dark:border-purple-700">
                            <h3 class="text-lg font-semibold text-purple-900 dark:text-purple-100 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Detalles del Horario
                            </h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Materia</dt>
                                    <dd class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">{{ $suplencia->horario->grupo->materia->nombre }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Grupo</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $suplencia->horario->grupo->nombre_grupo }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Aula</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $suplencia->horario->aula->codigo }} - {{ $suplencia->horario->aula->edificio }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Horario</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $suplencia->horario->bloque->hora_inicio }} - {{ $suplencia->horario->bloque->hora_fin }}
                                        @if($suplencia->horario->bloque->etiqueta)
                                            <span class="text-xs text-gray-500 dark:text-gray-400">({{ $suplencia->horario->bloque->etiqueta }})</span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        {{-- Docente Ausente --}}
                        <div class="bg-red-50 dark:bg-red-900/20 p-6 rounded-lg border border-red-200 dark:border-red-700">
                            <h3 class="text-lg font-semibold text-red-900 dark:text-red-100 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Docente Ausente
                            </h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nombre</dt>
                                    <dd class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">{{ $suplencia->docenteAusente->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $suplencia->docenteAusente->email }}</dd>
                                </div>
                            </dl>
                        </div>

                        {{-- Docente Suplente --}}
                        <div class="bg-green-50 dark:bg-green-900/20 p-6 rounded-lg border border-green-200 dark:border-green-700">
                            <h3 class="text-lg font-semibold text-green-900 dark:text-green-100 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Docente Suplente
                            </h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nombre</dt>
                                    <dd class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">{{ $suplencia->docenteSuplente->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $suplencia->docenteSuplente->email }}</dd>
                                </div>
                            </dl>
                        </div>

                    </div>

                    {{-- Observaciones --}}
                    @if($suplencia->observaciones)
                        <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 p-6 rounded-lg border border-yellow-200 dark:border-yellow-700">
                            <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Observaciones
                            </h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $suplencia->observaciones }}</p>
                        </div>
                    @endif

                    {{-- Acciones --}}
                    <div class="mt-6 flex gap-3">
                        <a href="{{ route('suplencias.index') }}" class="btn-ghost gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Volver al Listado
                        </a>

                        @if(!$suplencia->fecha_clase->isPast())
                            <form action="{{ route('suplencias.destroy', $suplencia) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta suplencia?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Eliminar Suplencia
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
