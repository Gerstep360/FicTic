<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('gestiones.index') }}" class="text-slate-400 hover:text-slate-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                {{ __('Abrir Nueva Gestión Académica') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6" x-data="{ feriados: [] }">
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

        <form method="POST" action="{{ route('gestiones.store') }}" class="space-y-6">
            @csrf

            {{-- Información básica --}}
            <div class="card p-6 space-y-4">
                <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Información de la Gestión
                </h3>

                <div>
                    <label for="nombre" class="block text-sm font-medium text-slate-300 mb-1">Nombre *</label>
                    <input 
                        type="text" 
                        id="nombre" 
                        name="nombre" 
                        value="{{ old('nombre') }}" 
                        required
                        placeholder="Ej: 1/2025"
                        class="input w-full"
                    >
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="fecha_inicio" class="block text-sm font-medium text-slate-300 mb-1">Fecha Inicio *</label>
                        <input 
                            type="date" 
                            id="fecha_inicio" 
                            name="fecha_inicio" 
                            value="{{ old('fecha_inicio') }}" 
                            required
                            class="input w-full"
                        >
                    </div>

                    <div>
                        <label for="fecha_fin" class="block text-sm font-medium text-slate-300 mb-1">Fecha Fin *</label>
                        <input 
                            type="date" 
                            id="fecha_fin" 
                            name="fecha_fin" 
                            value="{{ old('fecha_fin') }}" 
                            required
                            class="input w-full"
                        >
                    </div>
                </div>
            </div>

            {{-- Feriados --}}
            <div class="card p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Feriados
                    </h3>
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
                        <div class="flex gap-2">
                            <input 
                                type="date" 
                                :name="`feriados[${index}][fecha]`" 
                                x-model="feriado.fecha"
                                class="input flex-1"
                                required
                            >
                            <input 
                                type="text" 
                                :name="`feriados[${index}][descripcion]`" 
                                x-model="feriado.descripcion"
                                placeholder="Descripción (opcional)"
                                class="input flex-1"
                            >
                            <button 
                                type="button" 
                                @click="feriados.splice(index, 1)"
                                class="px-3 py-2 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                    <p class="text-sm text-slate-400" x-show="feriados.length === 0">
                        No hay feriados agregados. Haz clic en "Agregar Feriado" para añadir uno.
                    </p>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center gap-3">
                <a href="{{ route('gestiones.index') }}" class="btn-secondary text-center">
                    Cancelar
                </a>
                <button type="submit" class="btn-primary flex-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Crear Gestión
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
