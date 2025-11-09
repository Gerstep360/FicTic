<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                Reglas de Validación
            </h2>
            <div class="flex gap-2">
                <button onclick="openModal('createModal')"
                        class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white rounded-lg transition duration-150 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Regla
                </button>
                <a href="{{ route('validacion-horarios.index') }}"
                   class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition duration-150">
                    Volver a Validación
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-green-900/50 border border-green-700 text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Tabla de reglas -->
            <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-700">
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Código
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Nombre
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Categoría
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Alcance
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Severidad
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Bloqueante
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            @forelse($reglas as $regla)
                                <tr class="hover:bg-slate-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <code class="text-sm text-blue-400 bg-slate-900 px-2 py-1 rounded">
                                            {{ $regla->codigo }}
                                        </code>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-slate-200">{{ $regla->nombre }}</div>
                                        @if($regla->descripcion)
                                            <div class="text-xs text-slate-400 mt-1">{{ Str::limit($regla->descripcion, 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded bg-slate-700 text-slate-300">
                                            {{ ucfirst(str_replace('_', ' ', $regla->categoria)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-slate-300">{{ $regla->alcance_texto }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded {{ $regla->color_severidad }}">
                                            {{ $regla->icono_severidad }} {{ ucfirst($regla->severidad) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <form action="{{ route('validacion-horarios.reglas.toggle', $regla->id_regla) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $regla->activa ? 'bg-green-600' : 'bg-slate-600' }}">
                                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $regla->activa ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($regla->bloqueante)
                                            <svg class="w-5 h-5 text-red-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                        @else
                                            <span class="text-slate-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button onclick="editRegla({{ json_encode($regla) }})"
                                                class="text-blue-400 hover:text-blue-300 mr-3">
                                            Editar
                                        </button>
                                        <form action="{{ route('validacion-horarios.reglas.destroy', $regla->id_regla) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('¿Está seguro de eliminar esta regla?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <p class="text-slate-400 text-lg">No hay reglas configuradas</p>
                                        <button onclick="openModal('createModal')" 
                                                class="mt-4 text-blue-400 hover:text-blue-300">
                                            Crear primera regla
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Crear -->
    <div id="createModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-700">
                <h3 class="text-lg font-semibold text-slate-200">Nueva Regla de Validación</h3>
            </div>
            
            <form action="{{ route('validacion-horarios.reglas.store') }}" method="POST" class="p-6">
                @csrf
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Código <span class="text-red-400">*</span>
                        </label>
                        <input type="text" 
                               name="codigo" 
                               required
                               placeholder="MAX_HORAS_DIA"
                               class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Nombre <span class="text-red-400">*</span>
                        </label>
                        <input type="text" 
                               name="nombre" 
                               required
                               placeholder="Máximo de horas por día"
                               class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-300 mb-2">Descripción</label>
                        <textarea name="descripcion" 
                                  rows="2"
                                  class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Categoría <span class="text-red-400">*</span>
                        </label>
                        <select name="categoria" 
                                required
                                class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20">
                            <option value="carga_docente">Carga Docente</option>
                            <option value="descanso">Descanso</option>
                            <option value="tipo_aula">Tipo de Aula</option>
                            <option value="capacidad">Capacidad</option>
                            <option value="continuidad">Continuidad</option>
                            <option value="preferencias">Preferencias</option>
                            <option value="otras">Otras</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Severidad <span class="text-red-400">*</span>
                        </label>
                        <select name="severidad" 
                                required
                                class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20">
                            <option value="critica">Crítica</option>
                            <option value="alta">Alta</option>
                            <option value="media">Media</option>
                            <option value="baja">Baja</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Facultad</label>
                        <select name="id_facultad" 
                                class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20">
                            <option value="">Global (todas)</option>
                            @foreach($facultades as $facultad)
                                <option value="{{ $facultad->id_facultad }}">{{ $facultad->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Carrera</label>
                        <select name="id_carrera" 
                                class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20">
                            <option value="">Todas las carreras</option>
                            @foreach($carreras as $carrera)
                                <option value="{{ $carrera->id_carrera }}">{{ $carrera->nombre_carrera }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Parámetros JSON (opcional)
                        </label>
                        <textarea name="parametros" 
                                  rows="3"
                                  placeholder='{"max_horas_dia": 4, "minutos_descanso": 30}'
                                  class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200 font-mono text-sm focus:border-blue-500 focus:ring focus:ring-blue-500/20"></textarea>
                    </div>

                    <div class="col-span-2 flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="activa" value="1" checked
                                   class="rounded bg-slate-700 border-slate-600 text-blue-600 focus:ring-blue-500/20">
                            <span class="text-sm text-slate-300">Activa</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="bloqueante" value="1"
                                   class="rounded bg-slate-700 border-slate-600 text-red-600 focus:ring-red-500/20">
                            <span class="text-sm text-slate-300">Bloqueante (impide aplicación)</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-700">
                    <button type="button" 
                            onclick="closeModal('createModal')"
                            class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg transition">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white rounded-lg transition">
                        Crear Regla
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Editar -->
    <div id="editModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-700">
                <h3 class="text-lg font-semibold text-slate-200">Editar Regla de Validación</h3>
            </div>
            
            <form id="editForm" method="POST" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Nombre <span class="text-red-400">*</span>
                        </label>
                        <input type="text" 
                               id="edit_nombre"
                               name="nombre" 
                               required
                               class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-300 mb-2">Descripción</label>
                        <textarea id="edit_descripcion"
                                  name="descripcion" 
                                  rows="2"
                                  class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Severidad <span class="text-red-400">*</span>
                        </label>
                        <select id="edit_severidad"
                                name="severidad" 
                                required
                                class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20">
                            <option value="critica">Crítica</option>
                            <option value="alta">Alta</option>
                            <option value="media">Media</option>
                            <option value="baja">Baja</option>
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Parámetros JSON
                        </label>
                        <textarea id="edit_parametros"
                                  name="parametros" 
                                  rows="3"
                                  class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200 font-mono text-sm focus:border-blue-500 focus:ring focus:ring-blue-500/20"></textarea>
                    </div>

                    <div class="col-span-2 flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="edit_activa" name="activa" value="1"
                                   class="rounded bg-slate-700 border-slate-600 text-blue-600 focus:ring-blue-500/20">
                            <span class="text-sm text-slate-300">Activa</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="edit_bloqueante" name="bloqueante" value="1"
                                   class="rounded bg-slate-700 border-slate-600 text-red-600 focus:ring-red-500/20">
                            <span class="text-sm text-slate-300">Bloqueante</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-700">
                    <button type="button" 
                            onclick="closeModal('editModal')"
                            class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg transition">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white rounded-lg transition">
                        Actualizar Regla
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function editRegla(regla) {
            document.getElementById('editForm').action = `/admin/validacion-horarios/reglas/${regla.id_regla}`;
            document.getElementById('edit_nombre').value = regla.nombre;
            document.getElementById('edit_descripcion').value = regla.descripcion || '';
            document.getElementById('edit_severidad').value = regla.severidad;
            document.getElementById('edit_parametros').value = regla.parametros ? JSON.stringify(regla.parametros, null, 2) : '';
            document.getElementById('edit_activa').checked = regla.activa;
            document.getElementById('edit_bloqueante').checked = regla.bloqueante;
            openModal('editModal');
        }

        // Cerrar modales con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal('createModal');
                closeModal('editModal');
            }
        });
    </script>
</x-app-layout>
