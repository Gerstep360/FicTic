<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gestión de Suplencias') }}
            </h2>
            <a href="{{ route('suplencias.create') }}" class="btn-primary gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Suplencia
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

            {{-- Filtros --}}
            <div class="card mb-6">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Filtros de Búsqueda</h3>
                    <form method="GET" action="{{ route('suplencias.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        
                        <div>
                            <label for="id_docente_ausente" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Docente Ausente</label>
                            <select name="id_docente_ausente" id="id_docente_ausente" class="input mt-1">
                                <option value="">-- Todos --</option>
                                @foreach($docentes as $docente)
                                    <option value="{{ $docente->id }}" {{ request('id_docente_ausente') == $docente->id ? 'selected' : '' }}>
                                        {{ $docente->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="id_docente_suplente" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Docente Suplente</label>
                            <select name="id_docente_suplente" id="id_docente_suplente" class="input mt-1">
                                <option value="">-- Todos --</option>
                                @foreach($docentes as $docente)
                                    <option value="{{ $docente->id }}" {{ request('id_docente_suplente') == $docente->id ? 'selected' : '' }}>
                                        {{ $docente->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="fecha_desde" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha Desde</label>
                            <input type="date" name="fecha_desde" id="fecha_desde" value="{{ request('fecha_desde') }}" class="input mt-1">
                        </div>

                        <div>
                            <label for="fecha_hasta" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha Hasta</label>
                            <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ request('fecha_hasta') }}" class="input mt-1">
                        </div>

                        <div class="md:col-span-4 flex gap-2">
                            <button type="submit" class="btn-primary inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Buscar
                            </button>
                            <a href="{{ route('suplencias.index') }}" class="btn-ghost inline-flex items-center gap-2">
                                Limpiar Filtros
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Listado --}}
            <div class="card">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                        Suplencias Registradas ({{ $suplencias->total() }})
                    </h3>

                    @if($suplencias->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Docente Ausente</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Docente Suplente</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Materia</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aula</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($suplencias as $suplencia)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $suplencia->fecha_clase->format('d/m/Y') }}
                                                @if($suplencia->fecha_clase->isPast())
                                                    <span class="ml-2 px-2 py-1 text-xs bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded">Pasada</span>
                                                @elseif($suplencia->fecha_clase->isToday())
                                                    <span class="ml-2 px-2 py-1 text-xs bg-blue-200 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 rounded">Hoy</span>
                                                @else
                                                    <span class="ml-2 px-2 py-1 text-xs bg-green-200 dark:bg-green-900/50 text-green-800 dark:text-green-300 rounded">Próxima</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $suplencia->docenteAusente->name }}
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-semibold text-blue-600 dark:text-blue-400">
                                                        {{ $suplencia->nombre_suplente }}
                                                    </span>
                                                    @if($suplencia->id_docente_externo)
                                                        <span class="text-xs text-gray-500 dark:text-gray-400 italic">(externo)</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                                {{ $suplencia->horario->grupo->materia->nombre ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                                {{ $suplencia->horario->aula->codigo ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('suplencias.show', $suplencia) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3">
                                                    Ver
                                                </a>
                                                @if(!$suplencia->fecha_clase->isPast())
                                                    <form action="{{ route('suplencias.destroy', $suplencia) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta suplencia?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $suplencias->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No hay suplencias registradas</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comienza creando una nueva suplencia.</p>
                            <div class="mt-6">
                                <a href="{{ route('suplencias.create') }}" class="btn-primary inline-flex items-center gap-2">
                                    Nueva Suplencia
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
