{{-- resources/views/bloques/edit.blade.php --}}
@php
    $horaInicio = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $bloque->hora_inicio)->format('H:i');
    $horaFin = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $bloque->hora_fin)->format('H:i');
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                    Editar bloque {{ $bloque->etiqueta ?? '#'.$bloque->id_bloque }}
                </h2>
                <p class="text-sm text-slate-400 mt-1">Ajusta los datos del bloque horario seleccionado.</p>
            </div>
            <a href="{{ route('bloques.show', $bloque) }}" class="btn-ghost">Ver detalle</a>
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-slate-200 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Informacion del bloque
            </h3>

            @if ($errors->any())
                <div class="mb-4 p-4 rounded-xl bg-red-500/10 border border-red-500/20">
                    <h4 class="text-red-400 font-medium mb-1">Corrige los campos:</h4>
                    <ul class="list-disc list-inside text-sm text-red-300 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('bloques.update', $bloque) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Hora inicio</label>
                        <input
                            type="time"
                            name="hora_inicio"
                            value="{{ old('hora_inicio', $horaInicio) }}"
                            class="input"
                            required
                            step="60"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Hora fin</label>
                        <input
                            type="time"
                            name="hora_fin"
                            value="{{ old('hora_fin', $horaFin) }}"
                            class="input"
                            required
                            step="60"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Etiqueta</label>
                    <input
                        type="text"
                        name="etiqueta"
                        value="{{ old('etiqueta', $bloque->etiqueta) }}"
                        class="input"
                        maxlength="20"
                        placeholder="Ej. B1"
                    >
                </div>

                <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center gap-3 pt-2">
                    <a href="{{ route('bloques.show', $bloque) }}" class="btn-ghost">Cancelar</a>
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>

        <div class="card p-6 space-y-4">
            <div>
                <h3 class="text-lg font-semibold text-slate-200 mb-2">Resumen</h3>
                <ul class="text-sm text-slate-400 space-y-2">
                    <li><span class="chip mr-2">ID</span> #{{ $bloque->id_bloque }}</li>
                    <li><span class="chip mr-2">Creado</span> {{ optional($bloque->created_at)->format('d/m/Y H:i') ?? 'Sin registro' }}</li>
                    <li><span class="chip mr-2">Actualizado</span> {{ optional($bloque->updated_at)->format('d/m/Y H:i') ?? 'Sin registro' }}</li>
                </ul>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-slate-200 mb-2">Consejos</h3>
                <ul class="text-sm text-slate-400 space-y-2">
                    <li>Asegurate de mantener coherencia con los demas bloques para evitar traslapes.</li>
                    <li>Si cambias la duracion, valida los horarios asignados a este bloque.</li>
                    <li>La etiqueta es opcional, pero ayuda a identificar el bloque en reportes.</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
