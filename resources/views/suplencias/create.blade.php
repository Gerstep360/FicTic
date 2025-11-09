<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Registrar Nueva Suplencia') }}
            </h2>
            <a href="{{ route('suplencias.index') }}" class="btn-ghost gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Cancelar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('error'))
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 rounded">
                    <p class="font-bold">Error</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 rounded">
                    <p class="font-bold">Por favor corrige los siguientes errores:</p>
                    <ul class="list-disc list-inside mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="p-6">
                    <form method="POST" action="{{ route('suplencias.store') }}" class="space-y-6">
                        @csrf

                        {{-- Docente Ausente --}}
                        <div>
                            <label for="id_docente_ausente" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Docente Ausente <span class="text-red-500">*</span>
                            </label>
                            <select name="id_docente_ausente" id="id_docente_ausente" required 
                                    class="input @error('id_docente_ausente') border-red-500 @enderror"
                                    onchange="cargarHorarios()">
                                <option value="">-- Seleccione un docente --</option>
                                @foreach($docentes as $docente)
                                    <option value="{{ $docente->id }}" {{ old('id_docente_ausente') == $docente->id ? 'selected' : '' }}>
                                        {{ $docente->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_docente_ausente')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Fecha de la Clase --}}
                        <div>
                            <label for="fecha_clase" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Fecha de la Clase <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="fecha_clase" id="fecha_clase" value="{{ old('fecha_clase') }}" required
                                   min="{{ date('Y-m-d') }}"
                                   class="input @error('fecha_clase') border-red-500 @enderror"
                                   onchange="buscarDocentesDisponibles()">
                            @error('fecha_clase')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Horario (se llena después de seleccionar docente) --}}
                        <div id="horario-container" style="display: none;">
                            <label for="id_horario" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Horario de la Clase <span class="text-red-500">*</span>
                            </label>
                            <select name="id_horario" id="id_horario" 
                                    class="input @error('id_horario') border-red-500 @enderror"
                                    onchange="buscarDocentesDisponibles()">
                                <option value="">-- Seleccione un horario --</option>
                            </select>
                            @error('id_horario')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Seleccione el horario de la clase que necesita suplencia</p>
                        </div>

                        {{-- Docente Suplente --}}
                        <div id="suplente-container" style="display: none;">
                            <label for="id_docente_suplente" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Docente Suplente <span class="text-red-500">*</span>
                            </label>
                            <select name="id_docente_suplente" id="id_docente_suplente" 
                                    class="input @error('id_docente_suplente') border-red-500 @enderror">
                                <option value="">-- Seleccione un docente suplente --</option>
                            </select>
                            @error('id_docente_suplente')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="disponibles-info"></p>
                        </div>

                        {{-- Observaciones --}}
                        <div>
                            <label for="observaciones" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Observaciones
                            </label>
                            <textarea name="observaciones" id="observaciones" rows="3" 
                                      class="input @error('observaciones') border-red-500 @enderror"
                                      placeholder="Información adicional sobre la suplencia...">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Botones --}}
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('suplencias.index') }}" class="btn-ghost">
                                Cancelar
                            </a>
                            <button type="submit" class="btn-primary">
                                Registrar Suplencia
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Cargar horarios del docente ausente
        async function cargarHorarios() {
            const docenteId = document.getElementById('id_docente_ausente').value;
            const horarioSelect = document.getElementById('id_horario');
            const horarioContainer = document.getElementById('horario-container');
            
            if (!docenteId) {
                horarioContainer.style.display = 'none';
                return;
            }

            // TODO: Hacer petición AJAX para obtener horarios del docente
            // Por ahora mostrar el contenedor
            horarioContainer.style.display = 'block';
        }

        // Buscar docentes disponibles
        async function buscarDocentesDisponibles() {
            const fechaClase = document.getElementById('fecha_clase').value;
            const idHorario = document.getElementById('id_horario').value;
            const idDocenteAusente = document.getElementById('id_docente_ausente').value;
            
            if (!fechaClase || !idHorario || !idDocenteAusente) {
                return;
            }

            const supleContainer = document.getElementById('suplente-container');
            const supleSelect = document.getElementById('id_docente_suplente');
            const infoDiv = document.getElementById('disponibles-info');

            try {
                const response = await fetch('{{ route("suplencias.docentes-disponibles") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        fecha_clase: fechaClase,
                        id_horario: idHorario,
                        id_docente_ausente: idDocenteAusente
                    })
                });

                const data = await response.json();

                if (data.success) {
                    supleSelect.innerHTML = '<option value="">-- Seleccione un docente suplente --</option>';
                    
                    data.docentes.forEach(docente => {
                        const option = document.createElement('option');
                        option.value = docente.id;
                        option.textContent = docente.name;
                        supleSelect.appendChild(option);
                    });

                    supleContainer.style.display = 'block';
                    infoDiv.textContent = `${data.total} docente(s) disponible(s) para este horario`;
                    infoDiv.className = 'mt-1 text-xs text-green-600';
                } else {
                    alert('Error al buscar docentes disponibles');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error de conexión al buscar docentes disponibles');
            }
        }
    </script>
    @endpush
</x-app-layout>
