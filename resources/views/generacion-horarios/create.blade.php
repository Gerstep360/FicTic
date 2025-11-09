<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('generacion-horarios.index') }}" class="text-slate-400 hover:text-slate-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                {{ __('Nueva Generación Automática de Horarios') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
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

        {{-- Información --}}
        <div class="card p-4 bg-blue-500/10 border-blue-500/20">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1 text-sm text-slate-300">
                    <p class="font-medium text-blue-400 mb-1">¿Qué es la generación automática?</p>
                    <p class="mb-2">El sistema ejecutará un motor de optimización que asignará automáticamente todos los grupos a horarios y aulas válidos, sin conflictos de docente ni de aula.</p>
                    <p>Puede configurar criterios como minimizar huecos, balancear carga diaria, respetar preferencias, etc. El resultado será una propuesta completa que podrá revisar antes de aplicar.</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('generacion-horarios.store') }}" class="space-y-6">
            @csrf

            {{-- Alcance --}}
            <div class="card p-6 space-y-4">
                <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    Alcance de la Generación
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                        <p class="mt-1 text-xs text-slate-400">Período académico para el que se generarán los horarios</p>
                    </div>

                    <div>
                        <label for="id_carrera" class="block text-sm font-medium text-slate-300 mb-1">Carrera (Opcional)</label>
                        <select id="id_carrera" name="id_carrera" class="input w-full">
                            <option value="">Toda la Facultad</option>
                            @foreach($carreras as $carrera)
                                <option value="{{ $carrera->id_carrera }}" {{ old('id_carrera') == $carrera->id_carrera ? 'selected' : '' }}>
                                    {{ $carrera->nombre_carrera }} - {{ $carrera->facultad->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-400">Dejar vacío para generar horarios de toda la facultad</p>
                    </div>
                </div>
            </div>

            {{-- Configuración de optimización --}}
            <div class="card p-6 space-y-4">
                <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    Criterios de Optimización
                </h3>

                <div class="space-y-3">
                    <label class="flex items-start gap-3 p-3 bg-slate-800/30 rounded-lg cursor-pointer hover:bg-slate-800/50 transition">
                        <input type="checkbox" name="minimizar_huecos" value="1" checked class="mt-1 rounded border-slate-600 text-blue-600 focus:ring-blue-500">
                        <div class="flex-1">
                            <span class="text-slate-200 font-medium">Minimizar huecos</span>
                            <p class="text-xs text-slate-400 mt-1">Intenta evitar horas ociosas entre clases para cada docente</p>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3 bg-slate-800/30 rounded-lg cursor-pointer hover:bg-slate-800/50 transition">
                        <input type="checkbox" name="balancear_carga_diaria" value="1" checked class="mt-1 rounded border-slate-600 text-blue-600 focus:ring-blue-500">
                        <div class="flex-1">
                            <span class="text-slate-200 font-medium">Balancear carga diaria</span>
                            <p class="text-xs text-slate-400 mt-1">Distribuye las clases de cada docente equilibradamente entre los días de la semana</p>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3 bg-slate-800/30 rounded-lg cursor-pointer hover:bg-slate-800/50 transition">
                        <input type="checkbox" name="respetar_preferencias" value="1" checked class="mt-1 rounded border-slate-600 text-blue-600 focus:ring-blue-500">
                        <div class="flex-1">
                            <span class="text-slate-200 font-medium">Respetar preferencias horarias</span>
                            <p class="text-xs text-slate-400 mt-1">Considera las preferencias de horario de los docentes si están configuradas</p>
                        </div>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-slate-700/50">
                    <div>
                        <label for="max_horas_dia_docente" class="block text-sm font-medium text-slate-300 mb-1">Máximo horas por día</label>
                        <input type="number" id="max_horas_dia_docente" name="max_horas_dia_docente" 
                               value="{{ old('max_horas_dia_docente', 4) }}" 
                               min="1" max="8" class="input w-full">
                        <p class="mt-1 text-xs text-slate-400">Máximo de horas que un docente puede dictar en un día</p>
                    </div>

                    <div>
                        <label for="intentos_por_grupo" class="block text-sm font-medium text-slate-300 mb-1">Intentos por grupo</label>
                        <input type="number" id="intentos_por_grupo" name="intentos_por_grupo" 
                               value="{{ old('intentos_por_grupo', 100) }}" 
                               min="10" max="500" class="input w-full">
                        <p class="mt-1 text-xs text-slate-400">Número de intentos para asignar cada grupo antes de pasar al siguiente</p>
                    </div>
                </div>
            </div>

            {{-- Advertencia --}}
            <div class="card p-4 bg-amber-500/10 border-amber-500/20">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="flex-1 text-sm text-slate-300">
                        <p class="font-medium text-amber-400 mb-1">Importante</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>Este proceso puede tardar varios segundos o minutos dependiendo de la cantidad de grupos</li>
                            <li>Asegúrate de que los grupos tengan docentes asignados</li>
                            <li>Verifica que existan aulas y bloques horarios configurados</li>
                            <li>El resultado será una propuesta que deberás revisar antes de aplicar</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center gap-3">
                <a href="{{ route('generacion-horarios.index') }}" class="btn-secondary text-center">
                    Cancelar
                </a>
                <button type="submit" class="btn-primary flex-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Generar Horarios Automáticamente
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
