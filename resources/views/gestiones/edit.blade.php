{{-- resources/views/gestiones/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('gestiones.show', $gestion) }}" class="text-slate-400 hover:text-slate-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                {{ __('Editar Gestión Académica') }}
            </h2>
        </div>
    </x-slot>

    @php
        $fi = \Carbon\Carbon::parse($gestion->fecha_inicio)->format('Y-m-d');
        $ff = \Carbon\Carbon::parse($gestion->fecha_fin)->format('Y-m-d');

        // Cargamos feriados existentes (si no los pasas por relación)
        $feriados = \App\Models\Feriado::where('id_gestion', $gestion->id_gestion)
            ->orderBy('fecha')
            ->get(['fecha','descripcion']);
        $feriadosArray = $feriados->map(fn($f)=>[
            'fecha' => \Carbon\Carbon::parse($f->fecha)->format('Y-m-d'),
            'descripcion' => $f->descripcion,
        ])->values();
    @endphp

    <div class="max-w-3xl mx-auto space-y-6"
         x-data="{
            enviarFeriados: false,
            feriados: @js($feriadosArray),
            rango: { ini: '{{ $fi }}', fin: '{{ $ff }}' }
         }"
    >
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

        <form method="POST" action="{{ route('gestiones.update', $gestion) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Información básica --}}
            <div class="card p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Información de la Gestión
                    </h3>
                    <div>
                        @if($gestion->publicada)
                            <span class="chip bg-emerald-500/20 text-emerald-400 border-emerald-500/30">
                                Publicada
                            </span>
                        @else
                            <span class="chip bg-slate-500/20 text-slate-400 border-slate-500/30">
                                No Publicada
                            </span>
                        @endif
                    </div>
                </div>

                <div>
                    <label for="nombre" class="block text-sm font-medium text-slate-300 mb-1">Nombre *</label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        value="{{ old('nombre', $gestion->nombre) }}"
                        required
                        class="input w-full"
                    >
                    <p class="text-xs text-slate-500 mt-1">Ej: I-2025 o II-2025</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="fecha_inicio" class="block text-sm font-medium text-slate-300 mb-1">Fecha Inicio *</label>
                        <input
                            type="date"
                            id="fecha_inicio"
                            name="fecha_inicio"
                            value="{{ old('fecha_inicio', $fi) }}"
                            class="input w-full"
                            x-on:change="rango.ini = $event.target.value"
                        >
                    </div>

                    <div>
                        <label for="fecha_fin" class="block text-sm font-medium text-slate-300 mb-1">Fecha Fin *</label>
                        <input
                            type="date"
                            id="fecha_fin"
                            name="fecha_fin"
                            value="{{ old('fecha_fin', $ff) }}"
                            class="input w-full"
                            x-on:change="rango.fin = $event.target.value"
                        >
                    </div>
                </div>
            </div>

            {{-- Feriados (reemplazo opcional) --}}
            <div class="card p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Feriados
                        </h3>
                        <p class="text-sm text-slate-400 mt-1">
                            Marca la casilla para <strong>reemplazar</strong> todos los feriados de esta gestión. Si no la marcas, los feriados actuales se mantienen sin cambios.
                        </p>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-300">
                        <input type="checkbox" class="toggle" x-model="enviarFeriados">
                        <span>Actualizar feriados</span>
                    </label>
                </div>

                {{-- Resumen de los feriados actuales (solo informativo cuando enviarFeriados = false) --}}
                <div x-show="!enviarFeriados" class="space-y-2">
                    @if($feriados->isEmpty())
                        <p class="text-sm text-slate-400">No hay feriados registrados actualmente para esta gestión.</p>
                    @else
                        <ul class="text-sm text-slate-300 space-y-1">
                            @foreach($feriados as $f)
                                <li class="flex items-center gap-2">
                                    <span class="chip">{{ \Carbon\Carbon::parse($f->fecha)->format('d/m/Y') }}</span>
                                    <span class="text-slate-400">{{ $f->descripcion }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                {{-- Editor de feriados (solo se envía si enviarFeriados = true) --}}
                <div x-show="enviarFeriados" x-cloak>
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-sm text-slate-400">
                            Rango válido: <span class="chip" x-text="`${rango.ini || '—'} → ${rango.fin || '—'}`"></span>
                        </div>
                        <button
                            type="button"
                            @click="feriados.push({ fecha: '', descripcion: '' })"
                            class="text-sm text-blue-400 hover:text-blue-300 flex items-center gap-1"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Agregar Feriado
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(feriado, index) in feriados" :key="index">
                            <div class="flex gap-2 items-start">
                                <div class="flex-1">
                                    <input
                                        type="date"
                                        class="input w-full"
                                        x-model="feriado.fecha"
                                        :name="enviarFeriados ? `feriados[${index}][fecha]` : null"
                                        required
                                    >
                                </div>
                                <div class="flex-1">
                                    <input
                                        type="text"
                                        class="input w-full"
                                        placeholder="Descripción (opcional)"
                                        x-model="feriado.descripcion"
                                        :name="enviarFeriados ? `feriados[${index}][descripcion]` : null"
                                    >
                                </div>
                                <button
                                    type="button"
                                    @click="feriados.splice(index, 1)"
                                    class="px-3 py-2 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30"
                                    title="Quitar"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </template>

                        <p class="text-xs text-slate-500" x-show="feriados.length === 0">
                            No hay feriados en edición. Haz clic en “Agregar Feriado”.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center gap-3">
                <a href="{{ route('gestiones.index', $gestion) }}" class="btn-secondary text-center">
                    Cancelar
                </a>
                <button type="submit" class="btn-primary flex-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
