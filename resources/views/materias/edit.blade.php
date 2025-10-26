{{-- resources/views/materias/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('carreras.materias.index', $carrera) }}" class="text-slate-400 hover:text-slate-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                Editar Asignatura — {{ $carrera->nombre }}
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

        <form method="POST" action="{{ route('carreras.materias.update', [$carrera,$materia]) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="card p-6 space-y-4">
                <h3 class="text-lg font-semibold text-slate-200">Información de la Asignatura</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Código *</label>
                        <input type="text" name="codigo" value="{{ old('codigo', $materia->codigo) }}" class="input" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Créditos *</label>
                        <input type="number" min="0" max="99" name="creditos" value="{{ old('creditos', $materia->creditos) }}" class="input" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Nombre *</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $materia->nombre) }}" class="input" required>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Nivel</label>
                        <input type="text" name="nivel" value="{{ old('nivel', $materia->nivel) }}" class="input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Prerrequisitos</label>
                        <select name="prerrequisitos[]" multiple class="input h-48">
                            @foreach($materiasDeCarrera as $m)
                                <option value="{{ $m->id_materia }}"
                                    @if(collect(old('prerrequisitos', $prerrequisitosSeleccionados))->contains($m->id_materia)) selected @endif>
                                    {{ $m->codigo }} — {{ $m->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-500 mt-2">Mantén presionadas Ctrl/⌘ para seleccionar varios.</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center gap-3">
                <a href="{{ route('carreras.materias.index', $carrera) }}" class="btn-ghost text-center">
                    Cancelar
                </a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
