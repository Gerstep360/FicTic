<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('cargas-docentes.index') }}" class="text-slate-400 hover:text-slate-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                {{ __('Registrar Carga Docente') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        @if($errors->any())
            <div class="card p-4 bg-red-500/10 border-red-500/20">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-red-400 font-medium mb-2">Hay errores en el formulario:</h3>
                        <ul class="list-disc list-inside text-sm text-red-300 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('cargas-docentes.store') }}" class="space-y-6" x-data="{ restricciones: {} }">
            @csrf

            {{-- Información básica --}}
            <div class="card p-6 space-y-4">
                <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Información del Docente
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label for="id_docente" class="block text-sm font-medium text-slate-300 mb-1">Docente *</label>
                        <select id="id_docente" name="id_docente" required class="input w-full">
                            <option value="">Seleccione un docente</option>
                            @foreach($docentes as $docente)
                                <option value="{{ $docente->id }}" {{ old('id_docente') == $docente->id ? 'selected' : '' }}>
                                    {{ $docente->name }} ({{ $docente->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="id_gestion" class="block text-sm font-medium text-slate-300 mb-1">Gestión *</label>
                        <select id="id_gestion" name="id_gestion" required class="input w-full">
                            <option value="">Seleccione una gestión</option>
                            @foreach($gestiones as $gestion)
                                <option value="{{ $gestion->id_gestion }}" {{ old('id_gestion') == $gestion->id_gestion ? 'selected' : '' }}>
                                    {{ $gestion->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="id_carrera" class="block text-sm font-medium text-slate-300 mb-1">Carrera (Opcional)</label>
                        <select id="id_carrera" name="id_carrera" class="input w-full">
                            <option value="">General (todas las carreras)</option>
                            @foreach($carreras as $carrera)
                                <option value="{{ $carrera->id_carrera }}" {{ old('id_carrera') == $carrera->id_carrera ? 'selected' : '' }}>
                                    {{ $carrera->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Carga horaria --}}
            <div class="card p-6 space-y-4">
                <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Carga Horaria
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="horas_contratadas" class="block text-sm font-medium text-slate-300 mb-1">Horas Contratadas/Semana *</label>
                        <input 
                            type="number" 
                            id="horas_contratadas" 
                            name="horas_contratadas" 
                            value="{{ old('horas_contratadas') }}" 
                            required
                            min="1"
                            max="168"
                            placeholder="Ej: 40"
                            class="input w-full"
                        >
                        <p class="mt-1 text-xs text-slate-400">Número de horas semanales según contrato</p>
                    </div>

                    <div>
                        <label for="tipo_contrato" class="block text-sm font-medium text-slate-300 mb-1">Tipo de Contrato</label>
                        <select id="tipo_contrato" name="tipo_contrato" class="input w-full">
                            <option value="">Seleccione...</option>
                            <option value="Tiempo Completo" {{ old('tipo_contrato') == 'Tiempo Completo' ? 'selected' : '' }}>Tiempo Completo</option>
                            <option value="Medio Tiempo" {{ old('tipo_contrato') == 'Medio Tiempo' ? 'selected' : '' }}>Medio Tiempo</option>
                            <option value="Hora Cátedra" {{ old('tipo_contrato') == 'Hora Cátedra' ? 'selected' : '' }}>Hora Cátedra</option>
                            <option value="Invitado" {{ old('tipo_contrato') == 'Invitado' ? 'selected' : '' }}>Invitado</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="categoria" class="block text-sm font-medium text-slate-300 mb-1">Categoría</label>
                        <select id="categoria" name="categoria" class="input w-full">
                            <option value="">Seleccione...</option>
                            <option value="Titular" {{ old('categoria') == 'Titular' ? 'selected' : '' }}>Titular</option>
                            <option value="Adjunto" {{ old('categoria') == 'Adjunto' ? 'selected' : '' }}>Adjunto</option>
                            <option value="Jefe de Trabajos Prácticos" {{ old('categoria') == 'Jefe de Trabajos Prácticos' ? 'selected' : '' }}>Jefe de Trabajos Prácticos</option>
                            <option value="Auxiliar" {{ old('categoria') == 'Auxiliar' ? 'selected' : '' }}>Auxiliar</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Observaciones --}}
            <div class="card p-6 space-y-4">
                <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                    Observaciones
                </h3>

                <div>
                    <label for="observaciones" class="block text-sm font-medium text-slate-300 mb-1">Notas adicionales</label>
                    <textarea 
                        id="observaciones" 
                        name="observaciones" 
                        rows="3"
                        placeholder="Restricciones, días no disponibles, etc."
                        class="input w-full"
                    >{{ old('observaciones') }}</textarea>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center gap-3">
                <a href="{{ route('cargas-docentes.index') }}" class="btn-secondary text-center">
                    Cancelar
                </a>
                <button type="submit" class="btn-primary flex-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Registrar Carga
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
