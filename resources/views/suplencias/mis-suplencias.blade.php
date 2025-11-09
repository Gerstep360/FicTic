<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mis Suplencias Asignadas') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Clases que debes cubrir</h3>
                        <p class="text-sm text-gray-600">Listado de suplencias donde has sido asignado como docente suplente.</p>
                    </div>

                    @if($suplencias->count() > 0)
                        <div class="space-y-4">
                            @foreach($suplencias as $suplencia)
                                <div class="border rounded-lg p-4 hover:shadow-md transition {{ $suplencia->fecha_clase->isToday() ? 'bg-blue-50 border-blue-300' : ($suplencia->fecha_clase->isFuture() ? 'bg-white' : 'bg-gray-50') }}">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <h4 class="text-lg font-semibold text-gray-900">
                                                    {{ $suplencia->horario->grupo->materia->nombre }}
                                                </h4>
                                                @if($suplencia->fecha_clase->isToday())
                                                    <span class="px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full">HOY</span>
                                                @elseif($suplencia->fecha_clase->isFuture())
                                                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">PRÓXIMA</span>
                                                @else
                                                    <span class="px-3 py-1 bg-gray-300 text-gray-700 text-xs font-semibold rounded-full">PASADA</span>
                                                @endif
                                            </div>

                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm text-gray-600 mb-3">
                                                <div>
                                                    <span class="font-medium">Fecha:</span>
                                                    <span class="block text-gray-900">{{ $suplencia->fecha_clase->format('d/m/Y') }}</span>
                                                    <span class="text-xs">({{ $suplencia->fecha_clase->translatedFormat('l') }})</span>
                                                </div>
                                                <div>
                                                    <span class="font-medium">Horario:</span>
                                                    <span class="block text-gray-900">
                                                        {{ $suplencia->horario->bloque->hora_inicio }} - {{ $suplencia->horario->bloque->hora_fin }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="font-medium">Aula:</span>
                                                    <span class="block text-gray-900">{{ $suplencia->horario->aula->codigo }}</span>
                                                    <span class="text-xs">{{ $suplencia->horario->aula->edificio }}</span>
                                                </div>
                                                <div>
                                                    <span class="font-medium">Grupo:</span>
                                                    <span class="block text-gray-900">{{ $suplencia->horario->grupo->nombre_grupo }}</span>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2 text-sm">
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                <span class="text-gray-600">Reemplazas a:</span>
                                                <span class="font-semibold text-gray-900">{{ $suplencia->docenteAusente->name }}</span>
                                            </div>

                                            @if($suplencia->observaciones)
                                                <div class="mt-3 p-3 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                                                    <p class="text-xs font-medium text-yellow-800">Observaciones:</p>
                                                    <p class="text-sm text-yellow-900">{{ $suplencia->observaciones }}</p>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="ml-4">
                                            <a href="{{ route('suplencias.show', $suplencia) }}" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                                                Ver Detalle
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $suplencias->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">No tienes suplencias asignadas</h3>
                            <p class="mt-2 text-sm text-gray-500">Actualmente no hay clases que debas cubrir como suplente.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Resumen --}}
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600">Suplencias Hoy</p>
                            <p class="text-2xl font-bold text-blue-900">{{ $suplencias->where('fecha_clase', today())->count() }}</p>
                        </div>
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>

                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-600">Próximas</p>
                            <p class="text-2xl font-bold text-green-900">{{ $suplencias->where('fecha_clase', '>', today())->count() }}</p>
                        </div>
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Completadas</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $suplencias->where('fecha_clase', '<', today())->count() }}</p>
                        </div>
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
