<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                Corregir Asistencia Manual
            </h2>
            <a href="{{ route('asistencia-manual.listado') }}" 
               class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg transition text-sm">
                ← Volver al Listado
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-red-900/50 border border-red-700 text-red-200">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Advertencia -->
            <div class="mb-6 bg-gradient-to-r from-orange-900/50 to-yellow-900/50 border border-orange-700 rounded-lg p-5">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <svg class="w-10 h-10 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-orange-200 mb-2">
                            Esta corrección quedará registrada permanentemente
                        </h3>
                        <p class="text-orange-300/90 text-sm">
                            El sistema agregará automáticamente un registro de edición con su nombre de usuario 
                            y la fecha/hora de modificación al final de la observación. Esta acción será registrada 
                            en la bitácora del sistema con el estado anterior y el nuevo.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Información Original (Read-Only) -->
                <div class="lg:col-span-1">
                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 overflow-hidden sticky top-6">
                        <div class="bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-700 px-5 py-4">
                            <h3 class="text-base font-semibold text-slate-200">📋 Datos Originales</h3>
                        </div>
                        <div class="p-5 space-y-4">
                            
                            <div>
                                <label class="block text-xs font-medium text-slate-400 uppercase tracking-wider mb-1">
                                    Docente
                                </label>
                                <p class="text-sm text-slate-200 font-medium">
                                    {{ $asistencia->docente->name }}
                                </p>
                                <p class="text-xs text-slate-400">
                                    {{ $asistencia->docente->email }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-400 uppercase tracking-wider mb-1">
                                    Materia
                                </label>
                                <p class="text-sm text-slate-200">
                                    {{ $asistencia->horario->grupo->materia->nombre_materia }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-400 uppercase tracking-wider mb-1">
                                    Grupo
                                </label>
                                <p class="text-sm text-slate-200">
                                    {{ $asistencia->horario->grupo->nombre_grupo }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-400 uppercase tracking-wider mb-1">
                                    Aula
                                </label>
                                <p class="text-sm text-slate-200">
                                    {{ $asistencia->horario->aula->codigo }}
                                </p>
                                <p class="text-xs text-slate-400">
                                    {{ $asistencia->horario->aula->edificio }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-400 uppercase tracking-wider mb-1">
                                    Bloque
                                </label>
                                <p class="text-sm text-slate-200">
                                    {{ $asistencia->horario->bloque->nombre_bloque }}
                                </p>
                                <p class="text-xs text-slate-400">
                                    {{ $asistencia->horario->bloque->hora_inicio }} - {{ $asistencia->horario->bloque->hora_fin }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-400 uppercase tracking-wider mb-1">
                                    Fecha y Hora Original
                                </label>
                                <p class="text-sm text-slate-200">
                                    {{ $asistencia->fecha_hora->format('d/m/Y H:i') }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-400 uppercase tracking-wider mb-1">
                                    Tipo de Marca
                                </label>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $asistencia->tipo_marca === 'ENTRADA' 
                                        ? 'bg-blue-900/50 text-blue-200 border border-blue-700' 
                                        : 'bg-purple-900/50 text-purple-200 border border-purple-700' }}">
                                    {{ $asistencia->tipo_marca }}
                                </span>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-400 uppercase tracking-wider mb-1">
                                    Registrado Por
                                </label>
                                <p class="text-sm text-slate-200">
                                    {{ $asistencia->registrador->name }}
                                </p>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Formulario de Edición -->
                <div class="lg:col-span-2">
                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 overflow-hidden">
                        
                        <div class="bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-700 px-6 py-4">
                            <h3 class="text-lg font-semibold text-slate-200">Formulario de Corrección</h3>
                        </div>

                        <form action="{{ route('asistencia-manual.update', $asistencia->id_asistencia) }}" 
                              method="POST" 
                              class="p-6 space-y-6">
                            @csrf
                            @method('PATCH')

                            <!-- Estado Actual vs Nuevo -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    Estado de Asistencia <span class="text-red-400">*</span>
                                </label>
                                <div class="grid grid-cols-3 gap-3">
                                    <label class="relative flex flex-col items-center p-4 rounded-lg border-2 cursor-pointer transition
                                        {{ $asistencia->estado === 'PRESENTE' 
                                            ? 'border-green-600 bg-green-900/30' 
                                            : 'border-slate-600 bg-slate-750 hover:border-green-700' }}">
                                        <input type="radio" 
                                               name="estado" 
                                               value="PRESENTE"
                                               {{ old('estado', $asistencia->estado) === 'PRESENTE' ? 'checked' : '' }}
                                               class="sr-only">
                                        <svg class="w-8 h-8 mb-2 {{ $asistencia->estado === 'PRESENTE' ? 'text-green-400' : 'text-slate-400' }}" 
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-sm font-medium {{ $asistencia->estado === 'PRESENTE' ? 'text-green-200' : 'text-slate-300' }}">
                                            PRESENTE
                                        </span>
                                    </label>

                                    <label class="relative flex flex-col items-center p-4 rounded-lg border-2 cursor-pointer transition
                                        {{ $asistencia->estado === 'TARDANZA' 
                                            ? 'border-orange-600 bg-orange-900/30' 
                                            : 'border-slate-600 bg-slate-750 hover:border-orange-700' }}">
                                        <input type="radio" 
                                               name="estado" 
                                               value="TARDANZA"
                                               {{ old('estado', $asistencia->estado) === 'TARDANZA' ? 'checked' : '' }}
                                               class="sr-only">
                                        <svg class="w-8 h-8 mb-2 {{ $asistencia->estado === 'TARDANZA' ? 'text-orange-400' : 'text-slate-400' }}" 
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-sm font-medium {{ $asistencia->estado === 'TARDANZA' ? 'text-orange-200' : 'text-slate-300' }}">
                                            TARDANZA
                                        </span>
                                    </label>

                                    <label class="relative flex flex-col items-center p-4 rounded-lg border-2 cursor-pointer transition
                                        {{ $asistencia->estado === 'FALTA' 
                                            ? 'border-red-600 bg-red-900/30' 
                                            : 'border-slate-600 bg-slate-750 hover:border-red-700' }}">
                                        <input type="radio" 
                                               name="estado" 
                                               value="FALTA"
                                               {{ old('estado', $asistencia->estado) === 'FALTA' ? 'checked' : '' }}
                                               class="sr-only">
                                        <svg class="w-8 h-8 mb-2 {{ $asistencia->estado === 'FALTA' ? 'text-red-400' : 'text-slate-400' }}" 
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-sm font-medium {{ $asistencia->estado === 'FALTA' ? 'text-red-200' : 'text-slate-300' }}">
                                            FALTA
                                        </span>
                                    </label>
                                </div>
                                @error('estado')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Observación con Preview de Edición -->
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    Observación/Justificación <span class="text-red-400">*</span>
                                </label>
                                <textarea name="observacion" 
                                          rows="6"
                                          required
                                          minlength="10"
                                          maxlength="500"
                                          class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200 font-mono text-sm">{{ old('observacion', $asistencia->observacion) }}</textarea>
                                <p class="text-xs text-slate-400 mt-2">
                                    Mínimo 10 caracteres. La observación actual se mantendrá y se agregará automáticamente:
                                </p>
                                <div class="mt-2 p-3 bg-slate-900 rounded border border-slate-600">
                                    <p class="text-xs text-slate-400 font-mono">
                                        [Editado por <span class="text-orange-400">{{ auth()->user()->name }}</span> 
                                        el <span class="text-orange-400">{{ now()->format('d/m/Y H:i') }}</span>]
                                    </p>
                                </div>
                                @error('observacion')
                                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Info Box -->
                            <div class="bg-blue-900/20 border border-blue-700/50 rounded-lg p-4">
                                <div class="flex gap-3">
                                    <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="flex-1 text-sm text-blue-300">
                                        <p class="font-medium mb-1">Registro de Auditoría</p>
                                        <p class="text-xs text-blue-400/80">
                                            Esta modificación será registrada en la bitácora del sistema incluyendo 
                                            el estado anterior (<strong>{{ $asistencia->estado }}</strong>), 
                                            el nuevo estado que seleccione, y la observación actualizada.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-700">
                                <a href="{{ route('asistencia-manual.listado') }}" 
                                   class="px-5 py-2.5 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg transition">
                                    Cancelar
                                </a>
                                <button type="submit"
                                        class="px-6 py-2.5 bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-700 hover:to-orange-600 text-white rounded-lg transition font-medium flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Guardar Corrección
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <style>
        input[type="radio"]:checked + svg {
            transform: scale(1.1);
        }
    </style>
</x-app-layout>
