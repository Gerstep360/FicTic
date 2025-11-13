<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Editar Carga Docente') }}
            </h2>
            <a href="{{ route('cargas-docentes.show', $cargaDocente) }}" class="btn-ghost gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if($errors->any())
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 rounded">
                    <p class="font-bold">Errores en el formulario:</p>
                    <ul class="list-disc list-inside mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    
                    <form action="{{ route('cargas-docentes.update', $cargaDocente) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Docente --}}
                        <div class="mb-6">
                            <label for="id_docente" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Docente *
                            </label>
                            <select name="id_docente" id="id_docente" required class="input">
                                <option value="">-- Seleccione un docente --</option>
                                @foreach($docentes as $docente)
                                    <option value="{{ $docente->id }}" {{ old('id_docente', $cargaDocente->id_docente) == $docente->id ? 'selected' : '' }}>
                                        {{ $docente->name }} ({{ $docente->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_docente')
                                <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Gestión --}}
                        <div class="mb-6">
                            <label for="id_gestion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Gestión *
                            </label>
                            <select name="id_gestion" id="id_gestion" required class="input">
                                <option value="">-- Seleccione una gestión --</option>
                                @foreach($gestiones as $gestion)
                                    <option value="{{ $gestion->id_gestion }}" {{ old('id_gestion', $cargaDocente->id_gestion) == $gestion->id_gestion ? 'selected' : '' }}>
                                        {{ $gestion->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_gestion')
                                <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Carrera (opcional) --}}
                        <div class="mb-6">
                            <label for="id_carrera" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Carrera (Opcional)
                            </label>
                            <select name="id_carrera" id="id_carrera" class="input">
                                <option value="">-- Sin carrera específica --</option>
                                @foreach($carreras as $carrera)
                                    <option value="{{ $carrera->id_carrera }}" {{ old('id_carrera', $cargaDocente->id_carrera) == $carrera->id_carrera ? 'selected' : '' }}>
                                        {{ $carrera->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Dejar en blanco si el docente puede trabajar en cualquier carrera</p>
                            @error('id_carrera')
                                <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            {{-- Horas Contratadas --}}
                            <div>
                                <label for="horas_contratadas" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Horas Contratadas *
                                </label>
                                <input type="number" name="horas_contratadas" id="horas_contratadas" 
                                       value="{{ old('horas_contratadas', $cargaDocente->horas_contratadas) }}"
                                       min="1" max="168" required class="input">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Horas semanales contratadas (máx. 168)</p>
                                @error('horas_contratadas')
                                    <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Horas Asignadas (informativo, puede editarse) --}}
                            <div>
                                <label for="horas_asignadas" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Horas Asignadas
                                </label>
                                <input type="number" name="horas_asignadas" id="horas_asignadas" 
                                       value="{{ old('horas_asignadas', $cargaDocente->horas_asignadas) }}"
                                       min="0" class="input">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Se actualiza automáticamente con los horarios</p>
                                @error('horas_asignadas')
                                    <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            {{-- Tipo de Contrato --}}
                            <div>
                                <label for="tipo_contrato" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Tipo de Contrato
                                </label>
                                <input type="text" name="tipo_contrato" id="tipo_contrato" 
                                       value="{{ old('tipo_contrato', $cargaDocente->tipo_contrato) }}"
                                       maxlength="50" class="input"
                                       placeholder="Ej: Tiempo Completo, Medio Tiempo">
                                @error('tipo_contrato')
                                    <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Categoría --}}
                            <div>
                                <label for="categoria" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Categoría
                                </label>
                                <input type="text" name="categoria" id="categoria" 
                                       value="{{ old('categoria', $cargaDocente->categoria) }}"
                                       maxlength="50" class="input"
                                       placeholder="Ej: Titular, Auxiliar, Invitado">
                                @error('categoria')
                                    <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Restricciones de Horario (JSON) --}}
                        <div class="mb-6">
                            <label for="restricciones_horario" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Restricciones de Horario (JSON)
                            </label>
                            <textarea name="restricciones_horario" id="restricciones_horario" rows="4" class="input"
                                      placeholder='{"dias_no_disponibles": ["sabado", "domingo"], "bloques_no_disponibles": ["19:00-21:00"]}'>{{ old('restricciones_horario', $cargaDocente->restricciones_horario) }}</textarea>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Formato JSON con restricciones de días y horarios</p>
                            @error('restricciones_horario')
                                <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Observaciones --}}
                        <div class="mb-6">
                            <label for="observaciones" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Observaciones
                            </label>
                            <textarea name="observaciones" id="observaciones" rows="4" class="input"
                                      placeholder="Información adicional sobre la carga docente...">{{ old('observaciones', $cargaDocente->observaciones) }}</textarea>
                            @error('observaciones')
                                <p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Botones --}}
                        <div class="flex gap-3 justify-end">
                            <a href="{{ route('cargas-docentes.show', $cargaDocente) }}" class="btn-ghost">
                                Cancelar
                            </a>
                            <button type="submit" class="btn-primary gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Actualizar Carga Docente
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Información adicional --}}
            <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 dark:border-blue-600 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            <strong>Nota:</strong> Las horas asignadas se actualizan automáticamente cuando se crean o modifican horarios de clase. 
                            La combinación de docente, gestión y carrera debe ser única.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
