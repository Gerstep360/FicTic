<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                Validación de Horarios
            </h2>
            <a href="{{ route('validacion-horarios.reglas') }}"
               class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition duration-150 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Gestionar Reglas
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

            <!-- Información del sistema -->
            <div class="bg-gradient-to-r from-blue-900/50 to-blue-800/50 border border-blue-700 rounded-lg p-6 mb-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <svg class="w-12 h-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-blue-200 mb-2">
                            Sistema de Validación Inteligente
                        </h3>
                        <p class="text-blue-300/90 text-sm mb-3">
                            Este módulo ejecuta un conjunto de validaciones sobre los horarios asignados, 
                            detectando conflictos, sobrecargas y violaciones de políticas académicas.
                        </p>
                        <ul class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-blue-300/80">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Conflictos de aulas y docentes
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Límites de carga horaria
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Tiempos de descanso
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Capacidad de aulas
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Formulario de validación -->
            <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-700">
                    <h3 class="text-lg font-semibold text-slate-200">
                        Ejecutar Validación
                    </h3>
                    <p class="text-sm text-slate-400 mt-1">
                        Seleccione el alcance de la validación
                    </p>
                </div>

                <form action="{{ route('validacion-horarios.validar') }}" method="POST" class="p-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Gestión -->
                        <div>
                            <label for="id_gestion" class="block text-sm font-medium text-slate-300 mb-2">
                                Gestión <span class="text-red-400">*</span>
                            </label>
                            <select id="id_gestion" 
                                    name="id_gestion" 
                                    required
                                    class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20">
                                <option value="">-- Seleccione una gestión --</option>
                                @foreach($gestiones as $gestion)
                                    <option value="{{ $gestion->id_gestion }}" {{ old('id_gestion') == $gestion->id_gestion ? 'selected' : '' }}>
                                        {{ $gestion->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_gestion')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Carrera -->
                        <div>
                            <label for="id_carrera" class="block text-sm font-medium text-slate-300 mb-2">
                                Carrera
                                <span class="text-slate-500 text-xs">(opcional - vacío valida toda la facultad)</span>
                            </label>
                            <select id="id_carrera" 
                                    name="id_carrera"
                                    class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200 focus:border-blue-500 focus:ring focus:ring-blue-500/20">
                                <option value="">-- Todas las carreras --</option>
                                @foreach($carreras as $carrera)
                                    <option value="{{ $carrera->id_carrera }}" {{ old('id_carrera') == $carrera->id_carrera ? 'selected' : '' }}>
                                        {{ $carrera->nombre_carrera }} ({{ $carrera->facultad->nombre }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_carrera')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Advertencia de tiempo -->
                    <div class="mt-6 bg-amber-900/30 border border-amber-700/50 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm text-amber-300 font-medium">Nota importante</p>
                                <p class="text-sm text-amber-200/80 mt-1">
                                    El proceso puede tardar varios segundos dependiendo de la cantidad de horarios. 
                                    Las reglas activas se aplicarán automáticamente según la configuración establecida.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Botón de acción -->
                    <div class="mt-6 flex items-center justify-end gap-3">
                        <a href="{{ route('dashboard') }}" 
                           class="px-6 py-2.5 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg transition duration-150">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white rounded-lg transition duration-150 flex items-center gap-2 shadow-lg shadow-blue-900/50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Ejecutar Validación
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
