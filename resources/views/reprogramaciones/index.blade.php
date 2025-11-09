<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Reprogramaciones') }}
            </h2>
            @can('create', App\Models\Reprogramacion::class)
                <a href="{{ route('reprogramaciones.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow transition duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Reprogramación
                </a>
            @endcan
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

            {{-- Filtros --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700">Filtros de Búsqueda</h3>
                    <form method="GET" action="{{ route('reprogramaciones.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        
                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                            <select name="estado" id="estado" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Todos --</option>
                                <option value="PENDIENTE" {{ request('estado') == 'PENDIENTE' ? 'selected' : '' }}>Pendiente</option>
                                <option value="APROBADA" {{ request('estado') == 'APROBADA' ? 'selected' : '' }}>Aprobada</option>
                                <option value="RECHAZADA" {{ request('estado') == 'RECHAZADA' ? 'selected' : '' }}>Rechazada</option>
                            </select>
                        </div>

                        <div>
                            <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo</label>
                            <select name="tipo" id="tipo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Todos --</option>
                                <option value="CAMBIO_AULA" {{ request('tipo') == 'CAMBIO_AULA' ? 'selected' : '' }}>Cambio de Aula</option>
                                <option value="CAMBIO_FECHA" {{ request('tipo') == 'CAMBIO_FECHA' ? 'selected' : '' }}>Cambio de Fecha</option>
                                <option value="AMBOS" {{ request('tipo') == 'AMBOS' ? 'selected' : '' }}>Ambos</option>
                            </select>
                        </div>

                        <div>
                            <label for="fecha_desde" class="block text-sm font-medium text-gray-700">Fecha Desde</label>
                            <input type="date" name="fecha_desde" id="fecha_desde" value="{{ request('fecha_desde') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="fecha_hasta" class="block text-sm font-medium text-gray-700">Fecha Hasta</label>
                            <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ request('fecha_hasta') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="md:col-span-4 flex gap-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Buscar
                            </button>
                            <a href="{{ route('reprogramaciones.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-lg shadow transition">
                                Limpiar Filtros
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabla de reprogramaciones --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    @if($reprogramaciones->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Original</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materia/Grupo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cambio</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($reprogramaciones as $reprog)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $reprog->fecha_original->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                <div class="font-semibold">{{ $reprog->horarioOriginal->grupo->materia->nombre ?? 'N/A' }}</div>
                                                <div class="text-gray-500">Grupo: {{ $reprog->horarioOriginal->grupo->nombre_grupo ?? 'N/A' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($reprog->tipo === 'CAMBIO_AULA')
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Aula</span>
                                                @elseif($reprog->tipo === 'CAMBIO_FECHA')
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Fecha</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">Ambos</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                @if(in_array($reprog->tipo, ['CAMBIO_AULA', 'AMBOS']))
                                                    <div class="text-xs">
                                                        <span class="text-gray-500">De:</span> {{ $reprog->horarioOriginal->aula->codigo ?? 'N/A' }}<br>
                                                        <span class="text-gray-500">A:</span> <strong>{{ $reprog->aulaNueva->codigo ?? 'N/A' }}</strong>
                                                    </div>
                                                @endif
                                                @if(in_array($reprog->tipo, ['CAMBIO_FECHA', 'AMBOS']))
                                                    <div class="text-xs mt-1">
                                                        <span class="text-gray-500">Nueva fecha:</span> <strong>{{ $reprog->fecha_nueva?->format('d/m/Y') ?? 'N/A' }}</strong>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($reprog->estado === 'PENDIENTE')
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                                                @elseif($reprog->estado === 'APROBADA')
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aprobada</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rechazada</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ $reprog->fecha_solicitud->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('reprogramaciones.show', $reprog) }}" class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                                                
                                                @can('delete', $reprog)
                                                    @if($reprog->isPendiente())
                                                        <form action="{{ route('reprogramaciones.destroy', $reprog) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta reprogramación?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                                        </form>
                                                    @endif
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Paginación --}}
                        <div class="mt-4">
                            {{ $reprogramaciones->links() }}
                        </div>
                    @else
                        {{-- Estado vacío --}}
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay reprogramaciones</h3>
                            <p class="mt-1 text-sm text-gray-500">Comienza creando una nueva reprogramación.</p>
                            @can('create', App\Models\Reprogramacion::class)
                                <div class="mt-6">
                                    <a href="{{ route('reprogramaciones.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow transition">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Nueva Reprogramación
                                    </a>
                                </div>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
