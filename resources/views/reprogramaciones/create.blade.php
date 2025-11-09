<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Nueva Reprogramación') }}
            </h2>
            <a href="{{ route('reprogramaciones.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if($errors->any())
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                    <p class="font-bold">Errores en el formulario:</p>
                    <ul class="list-disc list-inside mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form action="{{ route('reprogramaciones.store') }}" method="POST">
                        @csrf

                        {{-- Selección del Horario Original --}}
                        <div class="mb-6">
                            <label for="id_horario_original" class="block text-sm font-medium text-gray-700 mb-2">
                                Horario a Reprogramar *
                            </label>
                            <select name="id_horario_original" id="id_horario_original" required 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    onchange="cargarDetallesHorario()">
                                <option value="">-- Seleccione un horario --</option>
                                @foreach($horarios as $horario)
                                    <option value="{{ $horario->id_horario }}" 
                                            data-dia="{{ $horario->dia_semana }}"
                                            data-bloque="{{ $horario->id_bloque }}"
                                            data-aula="{{ $horario->aula->codigo ?? 'N/A' }}"
                                            data-materia="{{ $horario->grupo->materia->nombre ?? 'N/A' }}"
                                            data-grupo="{{ $horario->grupo->nombre_grupo ?? 'N/A' }}"
                                            data-hora-inicio="{{ $horario->bloque->hora_inicio ?? '' }}"
                                            data-hora-fin="{{ $horario->bloque->hora_fin ?? '' }}"
                                            {{ old('id_horario_original') == $horario->id_horario ? 'selected' : '' }}>
                                        {{ ['', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'][$horario->dia_semana] ?? '' }}
                                        {{ $horario->bloque->hora_inicio ?? '' }} - 
                                        {{ $horario->grupo->materia->nombre ?? 'N/A' }} 
                                        ({{ $horario->grupo->nombre_grupo ?? 'N/A' }}) - 
                                        Aula: {{ $horario->aula->codigo ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_horario_original')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Detalles del horario seleccionado --}}
                        <div id="detallesHorario" class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200 hidden">
                            <h3 class="font-semibold text-blue-900 mb-3">Detalles del Horario Seleccionado</h3>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div><span class="text-gray-600">Materia:</span> <span id="detalle-materia" class="font-medium"></span></div>
                                <div><span class="text-gray-600">Grupo:</span> <span id="detalle-grupo" class="font-medium"></span></div>
                                <div><span class="text-gray-600">Aula Actual:</span> <span id="detalle-aula" class="font-medium"></span></div>
                                <div><span class="text-gray-600">Horario:</span> <span id="detalle-horario" class="font-medium"></span></div>
                            </div>
                        </div>

                        {{-- Fecha Original --}}
                        <div class="mb-6">
                            <label for="fecha_original" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de la Clase Original *
                            </label>
                            <input type="date" name="fecha_original" id="fecha_original" required
                                   value="{{ old('fecha_original') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Fecha en la que está programada actualmente la clase</p>
                            @error('fecha_original')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tipo de Reprogramación --}}
                        <div class="mb-6">
                            <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Reprogramación *
                            </label>
                            <select name="tipo" id="tipo" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    onchange="toggleCampos()">
                                <option value="">-- Seleccione --</option>
                                <option value="CAMBIO_AULA" {{ old('tipo') == 'CAMBIO_AULA' ? 'selected' : '' }}>Solo Cambio de Aula</option>
                                <option value="CAMBIO_FECHA" {{ old('tipo') == 'CAMBIO_FECHA' ? 'selected' : '' }}>Solo Cambio de Fecha</option>
                                <option value="AMBOS" {{ old('tipo') == 'AMBOS' ? 'selected' : '' }}>Cambio de Aula y Fecha</option>
                            </select>
                            @error('tipo')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nueva Aula (condicional) --}}
                        <div id="campo-aula" class="mb-6 hidden">
                            <label for="id_aula_nueva" class="block text-sm font-medium text-gray-700 mb-2">
                                Nueva Aula *
                            </label>
                            <button type="button" onclick="buscarAulasDisponibles()" 
                                    class="mb-2 inline-flex items-center px-3 py-1 text-sm bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Buscar Aulas Disponibles
                            </button>
                            <select name="id_aula_nueva" id="id_aula_nueva"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Primero busque disponibilidad --</option>
                            </select>
                            <p id="mensaje-aulas" class="text-xs text-gray-500 mt-1"></p>
                            @error('id_aula_nueva')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nueva Fecha (condicional) --}}
                        <div id="campo-fecha" class="mb-6 hidden">
                            <label for="fecha_nueva" class="block text-sm font-medium text-gray-700 mb-2">
                                Nueva Fecha *
                            </label>
                            <input type="date" name="fecha_nueva" id="fecha_nueva"
                                   value="{{ old('fecha_nueva') }}"
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Fecha a la que se moverá la clase</p>
                            @error('fecha_nueva')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Motivo --}}
                        <div class="mb-6">
                            <label for="motivo" class="block text-sm font-medium text-gray-700 mb-2">
                                Motivo de la Reprogramación *
                            </label>
                            <textarea name="motivo" id="motivo" rows="4" required
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="Ej: El aula X está inhabilitada por mantenimiento, se reprograma al aula Y disponible.">{{ old('motivo') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Explique detalladamente la razón del cambio (mantenimiento, feriado imprevisto, intercambio, etc.)</p>
                            @error('motivo')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Observaciones --}}
                        <div class="mb-6">
                            <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                                Observaciones (Opcional)
                            </label>
                            <textarea name="observaciones" id="observaciones" rows="3"
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="Información adicional...">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Botones --}}
                        <div class="flex gap-3 justify-end">
                            <a href="{{ route('reprogramaciones.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-lg shadow transition">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Solicitar Reprogramación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleCampos() {
            const tipo = document.getElementById('tipo').value;
            const campoAula = document.getElementById('campo-aula');
            const campoFecha = document.getElementById('campo-fecha');
            const aulaSelect = document.getElementById('id_aula_nueva');
            const fechaInput = document.getElementById('fecha_nueva');

            // Reset
            campoAula.classList.add('hidden');
            campoFecha.classList.add('hidden');
            aulaSelect.removeAttribute('required');
            fechaInput.removeAttribute('required');

            // Mostrar según tipo
            if (tipo === 'CAMBIO_AULA' || tipo === 'AMBOS') {
                campoAula.classList.remove('hidden');
                aulaSelect.setAttribute('required', 'required');
            }

            if (tipo === 'CAMBIO_FECHA' || tipo === 'AMBOS') {
                campoFecha.classList.remove('hidden');
                fechaInput.setAttribute('required', 'required');
            }
        }

        function cargarDetallesHorario() {
            const select = document.getElementById('id_horario_original');
            const option = select.options[select.selectedIndex];
            
            if (option.value) {
                document.getElementById('detalle-materia').textContent = option.dataset.materia;
                document.getElementById('detalle-grupo').textContent = option.dataset.grupo;
                document.getElementById('detalle-aula').textContent = option.dataset.aula;
                document.getElementById('detalle-horario').textContent = 
                    option.dataset.horaInicio + ' - ' + option.dataset.horaFin;
                document.getElementById('detallesHorario').classList.remove('hidden');
            } else {
                document.getElementById('detallesHorario').classList.add('hidden');
            }
        }

        async function buscarAulasDisponibles() {
            const horarioId = document.getElementById('id_horario_original').value;
            const fechaOriginal = document.getElementById('fecha_original').value;
            const tipo = document.getElementById('tipo').value;
            const fechaNueva = document.getElementById('fecha_nueva').value;

            if (!horarioId) {
                alert('Primero seleccione un horario');
                return;
            }

            // Determinar qué fecha usar
            let fechaValidar = fechaOriginal;
            if (tipo === 'AMBOS' && fechaNueva) {
                fechaValidar = fechaNueva;
            } else if (tipo === 'CAMBIO_FECHA' && fechaNueva) {
                fechaValidar = fechaNueva;
            }

            if (!fechaValidar) {
                alert('Primero seleccione la fecha');
                return;
            }

            const mensajeAulas = document.getElementById('mensaje-aulas');
            mensajeAulas.textContent = 'Buscando aulas disponibles...';
            mensajeAulas.className = 'text-xs text-blue-500 mt-1';

            try {
                const response = await fetch('{{ route('reprogramaciones.aulas-disponibles') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id_horario: horarioId,
                        fecha: fechaValidar
                    })
                });

                const data = await response.json();

                if (data.success) {
                    const select = document.getElementById('id_aula_nueva');
                    select.innerHTML = '<option value="">-- Seleccione un aula --</option>';
                    
                    if (data.aulas.length > 0) {
                        data.aulas.forEach(aula => {
                            const option = document.createElement('option');
                            option.value = aula.id_aula;
                            option.textContent = `${aula.codigo} - ${aula.tipo} (Cap: ${aula.capacidad || 'N/A'}) - ${aula.edificio || ''}`;
                            select.appendChild(option);
                        });
                        mensajeAulas.textContent = `${data.aulas.length} aula(s) disponible(s)`;
                        mensajeAulas.className = 'text-xs text-green-600 mt-1';
                    } else {
                        mensajeAulas.textContent = 'No hay aulas disponibles en ese horario';
                        mensajeAulas.className = 'text-xs text-red-500 mt-1';
                    }
                } else {
                    mensajeAulas.textContent = 'Error al buscar aulas';
                    mensajeAulas.className = 'text-xs text-red-500 mt-1';
                }
            } catch (error) {
                console.error('Error:', error);
                mensajeAulas.textContent = 'Error de conexión';
                mensajeAulas.className = 'text-xs text-red-500 mt-1';
            }
        }

        // Inicializar campos si hay valor old()
        document.addEventListener('DOMContentLoaded', function() {
            toggleCampos();
            const horarioSelect = document.getElementById('id_horario_original');
            if (horarioSelect.value) {
                cargarDetallesHorario();
            }
        });
    </script>
</x-app-layout>
