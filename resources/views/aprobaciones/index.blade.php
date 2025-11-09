<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                Aprobación de Horarios
            </h2>
            <button onclick="openModal('createModal')"
                    class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white rounded-lg transition duration-150 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Proceso
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-green-900/50 border border-green-700 text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-yellow-900/50 border border-yellow-700 text-yellow-200">
                    {{ session('warning') }}
                </div>
            @endif

            <!-- Filtros -->
            <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-4 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Gestión</label>
                        <select name="id_gestion" class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                            <option value="">Todas</option>
                            @foreach($gestiones as $g)
                                <option value="{{ $g->id_gestion }}" {{ request('id_gestion') == $g->id_gestion ? 'selected' : '' }}>
                                    {{ $g->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Carrera</label>
                        <select name="id_carrera" class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                            <option value="">Todas</option>
                            @foreach($carreras as $c)
                                <option value="{{ $c->id_carrera }}" {{ request('id_carrera') == $c->id_carrera ? 'selected' : '' }}>
                                    {{ $c->nombre_carrera }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Estado</label>
                        <select name="estado" class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                            <option value="">Todos</option>
                            <option value="borrador" {{ request('estado') == 'borrador' ? 'selected' : '' }}>En Elaboración</option>
                            <option value="pendiente_director" {{ request('estado') == 'pendiente_director' ? 'selected' : '' }}>Pendiente Director</option>
                            <option value="aprobado_director" {{ request('estado') == 'aprobado_director' ? 'selected' : '' }}>Aprobado Director</option>
                            <option value="aprobado_final" {{ request('estado') == 'aprobado_final' ? 'selected' : '' }}>Aprobado Final</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition w-full">
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Lista de aprobaciones -->
            <div class="space-y-4">
                @forelse($aprobaciones as $aprobacion)
                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-3">
                                        <h3 class="text-lg font-semibold text-slate-200">
                                            {{ $aprobacion->gestion->nombre }}
                                        </h3>
                                        <span class="px-3 py-1 text-sm font-medium rounded {{ $aprobacion->color_estado }}">
                                            {{ $aprobacion->icono_estado }} {{ $aprobacion->estado_texto }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                        <div>
                                            <p class="text-sm text-slate-400">Alcance</p>
                                            <p class="text-slate-200 font-medium">{{ $aprobacion->alcance_texto }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-slate-400">Horarios</p>
                                            <p class="text-slate-200 font-medium">{{ $aprobacion->horarios_validados }} / {{ $aprobacion->total_horarios }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-slate-400">Conflictos</p>
                                            <p class="text-slate-200 font-medium {{ $aprobacion->conflictos_pendientes > 0 ? 'text-red-400' : 'text-green-400' }}">
                                                {{ $aprobacion->conflictos_pendientes }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-slate-400">Actualizado</p>
                                            <p class="text-slate-200 font-medium">{{ $aprobacion->tiempo_en_estado }}</p>
                                        </div>
                                    </div>

                                    <!-- Barra de progreso -->
                                    <div class="mb-4">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-sm text-slate-400">Progreso</span>
                                            <span class="text-sm text-slate-300">{{ $aprobacion->porcentaje_progreso }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-700 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: {{ $aprobacion->porcentaje_progreso }}%"></div>
                                        </div>
                                    </div>

                                    <!-- Observaciones si existen -->
                                    @if($aprobacion->observaciones_director && in_array($aprobacion->estado, ['observado_director', 'aprobado_director']))
                                        <div class="bg-orange-900/20 border border-orange-700/50 rounded-lg p-3 mb-3">
                                            <p class="text-sm text-orange-300 font-medium mb-1">Observaciones del Director:</p>
                                            <p class="text-sm text-orange-200/80">{{ $aprobacion->observaciones_director }}</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Acciones -->
                                <div class="flex flex-col gap-2">
                                    <a href="{{ route('aprobaciones.show', $aprobacion->id_aprobacion) }}"
                                       class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg transition text-center text-sm">
                                        Ver Detalle
                                    </a>

                                    @if($aprobacion->puede_enviar_director)
                                        <form action="{{ route('aprobaciones.enviar-director', $aprobacion->id_aprobacion) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    onclick="return confirm('¿Enviar este horario al Director para su aprobación?')"
                                                    class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-sm">
                                                Enviar a Director
                                            </button>
                                        </form>
                                    @endif

                                    @if(in_array($aprobacion->estado, ['observado_director', 'observado_decano']))
                                        <button onclick="openRespuestaModal({{ $aprobacion->id_aprobacion }})"
                                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition text-sm">
                                            Responder
                                        </button>
                                    @endif

                                    @if($aprobacion->estado === 'borrador')
                                        <form action="{{ route('aprobaciones.destroy', $aprobacion->id_aprobacion) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('¿Está seguro de eliminar este proceso?')"
                                                    class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition text-sm">
                                                Eliminar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-12 text-center">
                        <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-slate-400 text-lg">No hay procesos de aprobación</p>
                        <button onclick="openModal('createModal')" class="mt-4 text-blue-400 hover:text-blue-300">
                            Crear el primero
                        </button>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $aprobaciones->links() }}
            </div>

        </div>
    </div>

    <!-- Modal Crear -->
    <div id="createModal" class="fixed inset-0 bg-black/50 z-50 items-center justify-center p-4" style="display: none;">
        <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 max-w-md w-full">
            <div class="px-6 py-4 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-700">
                <h3 class="text-lg font-semibold text-slate-200">Nuevo Proceso de Aprobación</h3>
            </div>
            
            <form action="{{ route('aprobaciones.store') }}" method="POST" class="p-6">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Gestión <span class="text-red-400">*</span>
                        </label>
                        <select name="id_gestion" required class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                            <option value="">-- Seleccione --</option>
                            @foreach($gestiones as $g)
                                <option value="{{ $g->id_gestion }}">{{ $g->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Carrera
                            <span class="text-slate-500 text-xs">(vacío = toda la facultad)</span>
                        </label>
                        <select name="id_carrera" class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                            <option value="">-- Toda la facultad --</option>
                            @foreach($carreras as $c)
                                <option value="{{ $c->id_carrera }}">{{ $c->nombre_carrera }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-700">
                    <button type="button" 
                            onclick="closeModal('createModal')"
                            class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg transition">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white rounded-lg transition">
                        Crear Proceso
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Responder Observaciones -->
    <div id="respuestaModal" class="fixed inset-0 bg-black/50 z-50 items-center justify-center p-4" style="display: none;">
        <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 max-w-md w-full">
            <div class="px-6 py-4 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-700">
                <h3 class="text-lg font-semibold text-slate-200">Responder Observaciones</h3>
            </div>
            
            <form id="respuestaForm" method="POST" class="p-6">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Respuesta</label>
                    <textarea name="respuesta" 
                              rows="4" 
                              required
                              placeholder="Indique los cambios realizados o ajustes aplicados..."
                              class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200"></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-700">
                    <button type="button" 
                            onclick="closeModal('respuestaModal')"
                            class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg transition">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                        Enviar Respuesta
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        function openRespuestaModal(idAprobacion) {
            document.getElementById('respuestaForm').action = `/admin/aprobaciones/${idAprobacion}/responder`;
            openModal('respuestaModal');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal('createModal');
                closeModal('respuestaModal');
            }
        });
    </script>
</x-app-layout>
