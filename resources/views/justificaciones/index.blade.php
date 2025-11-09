<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gestión de Justificaciones') }}
            </h2>
            @can('gestionar_justificaciones')
                <a href="{{ route('justificaciones.create') }}" class="btn-primary gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Justificación
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 dark:border-green-600 text-green-700 dark:text-green-300 p-4 rounded" role="alert">
                    <p class="font-bold">Éxito</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 dark:border-red-600 text-red-700 dark:text-red-300 p-4 rounded" role="alert">
                    <p class="font-bold">Error</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            {{-- Filtros --}}
            <div class="card overflow-hidden mb-6">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Filtros de Búsqueda</h3>
                    <form method="GET" action="{{ route('justificaciones.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        
                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estado</label>
                            <select name="estado" id="estado" class="input">
                                <option value="">-- Todos --</option>
                                <option value="PENDIENTE" {{ request('estado') == 'PENDIENTE' ? 'selected' : '' }}>Pendiente</option>
                                <option value="APROBADA" {{ request('estado') == 'APROBADA' ? 'selected' : '' }}>Aprobada</option>
                                <option value="RECHAZADA" {{ request('estado') == 'RECHAZADA' ? 'selected' : '' }}>Rechazada</option>
                            </select>
                        </div>

                        <div>
                            <label for="id_docente" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Docente</label>
                            <select name="id_docente" id="id_docente" class="input">
                                <option value="">-- Todos --</option>
                                @foreach($docentes as $docente)
                                    <option value="{{ $docente->id }}" {{ request('id_docente') == $docente->id ? 'selected' : '' }}>
                                        {{ $docente->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="fecha_desde" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fecha Desde</label>
                            <input type="date" name="fecha_desde" id="fecha_desde" value="{{ request('fecha_desde') }}" class="input">
                        </div>

                        <div>
                            <label for="fecha_hasta" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fecha Hasta</label>
                            <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ request('fecha_hasta') }}" class="input">
                        </div>

                        <div class="md:col-span-4 flex gap-2">
                            <button type="submit" class="btn-primary inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Buscar
                            </button>
                            <a href="{{ route('justificaciones.index') }}" class="btn-ghost inline-flex items-center gap-2">
                                Limpiar Filtros
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Listado --}}
            <div class="card overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                        Justificaciones Registradas ({{ $justificaciones->total() }})
                    </h3>

                    @if($justificaciones->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha Clase</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Docente</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Motivo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($justificaciones as $justificacion)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ \Carbon\Carbon::parse($justificacion->fecha_clase)->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $justificacion->docente->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                    @if($justificacion->tipo == 'ENFERMEDAD') bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300
                                                    @elseif($justificacion->tipo == 'EMERGENCIA') bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300
                                                    @elseif($justificacion->tipo == 'TRAMITE') bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300
                                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                                    @endif">
                                                    {{ $justificacion->tipo }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300 max-w-xs truncate">
                                                {{ $justificacion->motivo }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                    @if($justificacion->estado == 'PENDIENTE') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300
                                                    @elseif($justificacion->estado == 'APROBADA') bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300
                                                    @elseif($justificacion->estado == 'RECHAZADA') bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300
                                                    @endif">
                                                    {{ $justificacion->estado }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('justificaciones.show', $justificacion->id_justificacion) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3">
                                                    Ver
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $justificaciones->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No hay justificaciones</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No se encontraron justificaciones con los filtros aplicados.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
