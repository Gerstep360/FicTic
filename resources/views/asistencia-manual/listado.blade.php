<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                Historial de Registros Manuales
            </h2>
            <a href="{{ route('asistencia-manual.index') }}" 
               class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition text-sm">
                + Nuevo Registro
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-green-900/50 border border-green-700 text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-red-900/50 border border-red-700 text-red-200">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filtros -->
            <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-700 px-6 py-4">
                    <h3 class="text-lg font-semibold text-slate-200">Filtros de Búsqueda</h3>
                </div>
                
                <form method="GET" action="{{ route('asistencia-manual.listado') }}" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Docente</label>
                            <select name="id_docente" class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                                <option value="">Todos</option>
                                @foreach($docentes as $docente)
                                    <option value="{{ $docente->id }}" {{ request('id_docente') == $docente->id ? 'selected' : '' }}>
                                        {{ $docente->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Estado</label>
                            <select name="estado" class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                                <option value="">Todos</option>
                                <option value="PRESENTE" {{ request('estado') == 'PRESENTE' ? 'selected' : '' }}>Presente</option>
                                <option value="TARDANZA" {{ request('estado') == 'TARDANZA' ? 'selected' : '' }}>Tardanza</option>
                                <option value="FALTA" {{ request('estado') == 'FALTA' ? 'selected' : '' }}>Falta</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Desde</label>
                            <input type="date" 
                                   name="fecha_desde" 
                                   value="{{ request('fecha_desde') }}"
                                   class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Hasta</label>
                            <input type="date" 
                                   name="fecha_hasta" 
                                   value="{{ request('fecha_hasta') }}"
                                   class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mt-4">
                        <button type="submit" 
                                class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition">
                            Filtrar
                        </button>
                        <a href="{{ route('asistencia-manual.listado') }}" 
                           class="px-6 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg transition">
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>

            <!-- Tabla de Resultados -->
            <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 overflow-hidden">
                
                <div class="bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-700 px-6 py-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-200">Registros Manuales</h3>
                        <p class="text-sm text-slate-400">Total: {{ $asistencias->total() }} registros</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-700">
                        <thead class="bg-slate-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Fecha/Hora
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Docente
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Materia
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Aula
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Tipo
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Registrado Por
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Observación
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-slate-800 divide-y divide-slate-700">
                            @forelse($asistencias as $asistencia)
                                <tr class="hover:bg-slate-750 transition">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-slate-200">
                                            {{ $asistencia->fecha_hora->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-slate-400">
                                            {{ $asistencia->fecha_hora->format('H:i') }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-slate-200">
                                            {{ $asistencia->docente->name }}
                                        </div>
                                        <div class="text-xs text-slate-400">
                                            {{ $asistencia->docente->email }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm text-slate-200">
                                            {{ $asistencia->horario->grupo->materia->nombre_materia }}
                                        </div>
                                        <div class="text-xs text-slate-400">
                                            {{ $asistencia->horario->grupo->nombre_grupo }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm text-slate-200">
                                            {{ $asistencia->horario->aula->codigo }}
                                        </div>
                                        <div class="text-xs text-slate-400">
                                            {{ $asistencia->horario->aula->edificio }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $asistencia->tipo_marca === 'ENTRADA' 
                                                ? 'bg-blue-900/50 text-blue-200 border border-blue-700' 
                                                : 'bg-purple-900/50 text-purple-200 border border-purple-700' }}">
                                            {{ $asistencia->tipo_marca }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $asistencia->estado === 'PRESENTE' 
                                                ? 'bg-green-900/50 text-green-200 border border-green-700' 
                                                : ($asistencia->estado === 'TARDANZA' 
                                                    ? 'bg-orange-900/50 text-orange-200 border border-orange-700' 
                                                    : 'bg-red-900/50 text-red-200 border border-red-700') }}">
                                            {{ $asistencia->estado }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm text-slate-200">
                                            {{ $asistencia->registrador->name }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm text-slate-300 max-w-xs truncate" 
                                             title="{{ $asistencia->observacion }}">
                                            {{ Str::limit($asistencia->observacion, 50) }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('asistencia-manual.edit', $asistencia->id_asistencia) }}" 
                                               class="text-blue-400 hover:text-blue-300 transition"
                                               title="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <button onclick="confirmarEliminacion({{ $asistencia->id_asistencia }})"
                                                    class="text-red-400 hover:text-red-300 transition"
                                                    title="Eliminar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-slate-400">
                                        <svg class="w-16 h-16 mx-auto mb-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-lg">No se encontraron registros manuales</p>
                                        <p class="text-sm text-slate-500 mt-1">Intente ajustar los filtros o crear un nuevo registro</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($asistencias->hasPages())
                    <div class="bg-slate-900 px-6 py-4 border-t border-slate-700">
                        {{ $asistencias->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Modal de Confirmación de Eliminación -->
    <div id="modal-eliminar" class="hidden fixed inset-0 bg-black bg-opacity-75 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-slate-800 border-slate-700">
            <form id="form-eliminar" method="POST">
                @csrf
                @method('DELETE')
                
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-slate-200 mb-2">
                        Confirmar Eliminación
                    </h3>
                    <p class="text-sm text-slate-400">
                        Esta acción no se puede deshacer. Por favor, indique el motivo de la eliminación:
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Motivo de Eliminación <span class="text-red-400">*</span>
                    </label>
                    <textarea name="motivo_eliminacion" 
                              id="motivo-eliminacion"
                              rows="3"
                              required
                              minlength="10"
                              maxlength="200"
                              placeholder="Ej: Registro duplicado por error de sistema..."
                              class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200"></textarea>
                    <p class="text-xs text-slate-500 mt-1">Mínimo 10 caracteres</p>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <button type="button" 
                            onclick="cerrarModal()"
                            class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg transition">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                        Eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function confirmarEliminacion(id) {
            const modal = document.getElementById('modal-eliminar');
            const form = document.getElementById('form-eliminar');
            const textarea = document.getElementById('motivo-eliminacion');
            
            form.action = `/asistencia/manual/${id}`;
            textarea.value = '';
            modal.classList.remove('hidden');
        }

        function cerrarModal() {
            const modal = document.getElementById('modal-eliminar');
            modal.classList.add('hidden');
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('modal-eliminar').addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModal();
            }
        });
    </script>
</x-app-layout>
