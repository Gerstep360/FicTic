<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-200 leading-tight">
            CU-21. Registro Manual de Asistencia
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
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

            <!-- Información -->
            <div class="mb-6 bg-gradient-to-r from-orange-900/50 to-yellow-900/50 border border-orange-700 rounded-lg p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <svg class="w-12 h-12 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-orange-200 mb-2">
                            Registro Manual - Modo Contingencia
                        </h3>
                        <p class="text-orange-300/90 text-sm mb-3">
                            Este formulario permite registrar asistencias manualmente en caso de falla del escáner QR 
                            o para realizar correcciones posteriores. Todos los registros manuales quedan registrados 
                            en la bitácora con el usuario que los realizó.
                        </p>
                        <div class="flex items-center gap-4 text-xs text-orange-300/80">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Trazabilidad completa
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Requiere justificación
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Auditable
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Formulario Principal -->
                <div class="lg:col-span-2">
                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 overflow-hidden">
                        
                        <div class="bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-700 px-6 py-4">
                            <h3 class="text-lg font-semibold text-slate-200">Formulario de Registro</h3>
                        </div>

                        <form action="{{ route('asistencia-manual.store') }}" method="POST" class="p-6 space-y-6">
                            @csrf

                            <!-- Selección de Docente -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    Docente <span class="text-red-400">*</span>
                                </label>
                                <select name="id_docente" 
                                        id="select-docente"
                                        onchange="cargarHorarios()"
                                        required
                                        class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                                    <option value="">Seleccione un docente</option>
                                    @foreach($docentes as $docente)
                                        <option value="{{ $docente->id }}">{{ $docente->name }} ({{ $docente->email }})</option>
                                    @endforeach
                                </select>
                                @error('id_docente')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Fecha y Hora -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">
                                        Fecha <span class="text-red-400">*</span>
                                    </label>
                                    <input type="date" 
                                           name="fecha" 
                                           id="input-fecha"
                                           value="{{ old('fecha', date('Y-m-d')) }}"
                                           onchange="cargarHorarios()"
                                           required
                                           max="{{ date('Y-m-d') }}"
                                           class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                                    @error('fecha')
                                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">
                                        Hora <span class="text-red-400">*</span>
                                    </label>
                                    <input type="time" 
                                           name="hora" 
                                           value="{{ old('hora', date('H:i')) }}"
                                           required
                                           class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                                    @error('hora')
                                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Horario/Clase -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    Clase/Horario <span class="text-red-400">*</span>
                                </label>
                                <select name="id_horario" 
                                        id="select-horario"
                                        required
                                        class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                                    <option value="">Primero seleccione docente y fecha</option>
                                </select>
                                <p class="text-xs text-slate-400 mt-1">
                                    Se mostrarán las clases del docente para el día seleccionado
                                </p>
                                @error('id_horario')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tipo de Marca y Estado -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">
                                        Tipo de Marca <span class="text-red-400">*</span>
                                    </label>
                                    <select name="tipo_marca" 
                                            required
                                            class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                                        <option value="ENTRADA">Entrada</option>
                                        <option value="SALIDA">Salida</option>
                                    </select>
                                    @error('tipo_marca')
                                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-300 mb-2">
                                        Estado <span class="text-red-400">*</span>
                                    </label>
                                    <select name="estado" 
                                            required
                                            class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                                        <option value="PRESENTE">Presente</option>
                                        <option value="TARDANZA">Tardanza</option>
                                        <option value="FALTA">Falta</option>
                                    </select>
                                    @error('estado')
                                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Observación/Justificación -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    Observación/Justificación <span class="text-red-400">*</span>
                                </label>
                                <textarea name="observacion" 
                                          rows="4"
                                          required
                                          minlength="10"
                                          maxlength="500"
                                          placeholder="Indique el motivo del registro manual (ej: 'Escáner QR no operativo', 'Corrección por evidencia presentada', 'Docente olvidó su código QR')..."
                                          class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200">{{ old('observacion') }}</textarea>
                                <p class="text-xs text-slate-400 mt-1">
                                    Mínimo 10 caracteres. Esta observación quedará registrada permanentemente.
                                </p>
                                @error('observacion')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Botones -->
                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-700">
                                <a href="{{ route('asistencia-manual.listado') }}" 
                                   class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg transition">
                                    Ver Listado
                                </a>
                                <button type="submit"
                                        class="px-6 py-2 bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-600 text-white rounded-lg transition font-medium">
                                    Registrar Asistencia
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Panel Lateral: Guía -->
                <div class="space-y-6">
                    
                    <!-- Cuándo usar -->
                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6">
                        <h4 class="text-sm font-semibold text-slate-300 uppercase tracking-wider mb-4">
                            ⚠️ Cuándo Usar
                        </h4>
                        <ul class="space-y-2 text-sm text-slate-400">
                            <li class="flex items-start gap-2">
                                <span class="text-orange-400 mt-1">•</span>
                                <span>Escáner QR no disponible o defectuoso</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-orange-400 mt-1">•</span>
                                <span>Docente olvidó su código QR</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-orange-400 mt-1">•</span>
                                <span>Corrección de registro erróneo</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-orange-400 mt-1">•</span>
                                <span>Docente presenta evidencia posterior de asistencia</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Instrucciones -->
                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6">
                        <h4 class="text-sm font-semibold text-slate-300 uppercase tracking-wider mb-4">
                            📋 Instrucciones
                        </h4>
                        <ol class="space-y-2 text-sm text-slate-400">
                            <li class="flex gap-2">
                                <span class="text-orange-400 font-bold">1.</span>
                                <span>Seleccione el docente</span>
                            </li>
                            <li class="flex gap-2">
                                <span class="text-orange-400 font-bold">2.</span>
                                <span>Indique fecha y hora del evento</span>
                            </li>
                            <li class="flex gap-2">
                                <span class="text-orange-400 font-bold">3.</span>
                                <span>Elija la clase correspondiente</span>
                            </li>
                            <li class="flex gap-2">
                                <span class="text-orange-400 font-bold">4.</span>
                                <span>Defina tipo (entrada/salida) y estado</span>
                            </li>
                            <li class="flex gap-2">
                                <span class="text-orange-400 font-bold">5.</span>
                                <span><strong>Importante:</strong> Escriba una justificación clara y detallada</span>
                            </li>
                        </ol>
                    </div>

                    <!-- Advertencia -->
                    <div class="bg-gradient-to-br from-red-900/30 to-orange-900/30 border border-red-700 rounded-lg p-6">
                        <h4 class="text-sm font-semibold text-red-200 uppercase tracking-wider mb-2">
                            🔒 Importante
                        </h4>
                        <p class="text-sm text-red-300/90">
                            Todos los registros manuales quedan permanentemente identificados en el sistema 
                            con el nombre del usuario que los creó y la observación proporcionada. 
                            Úselo responsablemente.
                        </p>
                    </div>

                </div>
            </div>

            <!-- Tabla de Asistencias Registradas -->
            <div class="mt-8">
                <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 overflow-hidden">
                    
                    <div class="bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-700 px-6 py-4">
                        <h3 class="text-lg font-semibold text-slate-200">Historial de Asistencias</h3>
                        <p class="text-sm text-slate-400 mt-1">Todas las asistencias registradas (QR y Manual)</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-900/50 border-b border-slate-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Docente</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Materia</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Aula</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Fecha/Hora</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Tipo</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Estado</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Método</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">Registrado por</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700">
                                @forelse($asistencias as $asistencia)
                                    <tr class="hover:bg-slate-700/50 transition">
                                        <td class="px-4 py-3 text-sm text-slate-200">
                                            {{ $asistencia->docente->name }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-300">
                                            {{ $asistencia->horario->grupo->materia->nombre ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-300">
                                            <span class="px-2 py-1 bg-slate-700 rounded text-xs font-mono">
                                                {{ $asistencia->horario->aula->codigo ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-300">
                                            <div class="flex flex-col">
                                                <span>{{ $asistencia->fecha_hora->format('d/m/Y') }}</span>
                                                <span class="text-xs text-slate-500">{{ $asistencia->fecha_hora->format('H:i') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($asistencia->tipo_marca === 'ENTRADA')
                                                <span class="px-2 py-1 bg-blue-900/50 border border-blue-700 text-blue-300 rounded text-xs font-medium">
                                                    ENTRADA
                                                </span>
                                            @else
                                                <span class="px-2 py-1 bg-purple-900/50 border border-purple-700 text-purple-300 rounded text-xs font-medium">
                                                    SALIDA
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($asistencia->estado === 'PRESENTE')
                                                <span class="px-2 py-1 bg-green-900/50 border border-green-700 text-green-300 rounded text-xs font-medium">
                                                    PRESENTE
                                                </span>
                                            @elseif($asistencia->estado === 'TARDANZA')
                                                <span class="px-2 py-1 bg-yellow-900/50 border border-yellow-700 text-yellow-300 rounded text-xs font-medium">
                                                    TARDANZA
                                                </span>
                                            @else
                                                <span class="px-2 py-1 bg-red-900/50 border border-red-700 text-red-300 rounded text-xs font-medium">
                                                    {{ $asistencia->estado }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($asistencia->es_manual)
                                                <span class="px-2 py-1 bg-orange-900/50 border border-orange-700 text-orange-300 rounded text-xs font-medium flex items-center gap-1 w-fit">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                                    </svg>
                                                    MANUAL
                                                </span>
                                            @else
                                                <span class="px-2 py-1 bg-indigo-900/50 border border-indigo-700 text-indigo-300 rounded text-xs font-medium flex items-center gap-1 w-fit">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm2 2V5h1v1H5zM3 13a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zm2 2v-1h1v1H5zM13 3a1 1 0 00-1 1v3a1 1 0 001 1h3a1 1 0 001-1V4a1 1 0 00-1-1h-3zm1 2v1h1V5h-1z" clip-rule="evenodd"/>
                                                        <path d="M11 4a1 1 0 10-2 0v1a1 1 0 002 0V4zM10 7a1 1 0 011 1v1h2a1 1 0 110 2h-3a1 1 0 01-1-1V8a1 1 0 011-1zM16 9a1 1 0 100 2 1 1 0 000-2zM9 13a1 1 0 011-1h1a1 1 0 110 2v2a1 1 0 11-2 0v-3zM7 11a1 1 0 100-2H4a1 1 0 100 2h3zM17 13a1 1 0 01-1 1h-2a1 1 0 110-2h2a1 1 0 011 1zM16 17a1 1 0 100-2h-3a1 1 0 100 2h3z"/>
                                                    </svg>
                                                    QR
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-400">
                                            @if($asistencia->es_manual && $asistencia->registrador)
                                                <div class="flex flex-col">
                                                    <span class="text-slate-300">{{ $asistencia->registrador->name }}</span>
                                                    @if($asistencia->observacion)
                                                        <span class="text-xs text-slate-500 italic" title="{{ $asistencia->observacion }}">
                                                            {{ Str::limit($asistencia->observacion, 30) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-slate-500">Sistema QR</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                            No hay asistencias registradas
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($asistencias->hasPages())
                        <div class="px-6 py-4 bg-slate-900/50 border-t border-slate-700">
                            {{ $asistencias->links() }}
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>

    <script>
        async function cargarHorarios() {
            const docenteId = document.getElementById('select-docente').value;
            const fecha = document.getElementById('input-fecha').value;
            const selectHorario = document.getElementById('select-horario');
            
            if (!docenteId || !fecha) {
                selectHorario.innerHTML = '<option value="">Primero seleccione docente y fecha</option>';
                return;
            }
            
            selectHorario.innerHTML = '<option value="">Cargando...</option>';
            
            try {
                const response = await fetch(`{{ route('asistencia-manual.horarios-docente') }}?id_docente=${docenteId}&fecha=${fecha}`);
                const data = await response.json();
                
                if (data.horarios.length === 0) {
                    selectHorario.innerHTML = '<option value="">No hay clases programadas para este día</option>';
                    return;
                }
                
                selectHorario.innerHTML = '<option value="">Seleccione una clase</option>';
                data.horarios.forEach(h => {
                    const option = document.createElement('option');
                    option.value = h.id_horario;
                    option.textContent = `${h.materia} - ${h.grupo} | ${h.aula} | ${h.bloque}`;
                    selectHorario.appendChild(option);
                });
            } catch (error) {
                console.error('Error al cargar horarios:', error);
                selectHorario.innerHTML = '<option value="">Error al cargar horarios</option>';
            }
        }
    </script>
</x-app-layout>
