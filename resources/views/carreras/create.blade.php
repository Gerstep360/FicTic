<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('carreras.index') }}" class="text-slate-400 hover:text-slate-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                {{ __('Nueva Carrera') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
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

        <form method="POST" action="{{ route('carreras.store') }}" class="space-y-6">
            @csrf

            <div class="card p-6 space-y-4">
                <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Información de la Carrera
                </h3>

                <div>
                    <label for="nombre" class="block text-sm font-medium text-slate-300 mb-1">Nombre *</label>
                    <input 
                        type="text" 
                        id="nombre" 
                        name="nombre" 
                        value="{{ old('nombre') }}" 
                        required
                        placeholder="Ej: Ingeniería de Sistemas"
                        class="input w-full"
                    >
                </div>

                <div>
                    <label for="id_facultad" class="block text-sm font-medium text-slate-300 mb-1">Facultad *</label>
                    <select 
                        id="id_facultad" 
                        name="id_facultad" 
                        required
                        class="input w-full"
                    >
                        <option value="">Seleccione una facultad</option>
                        @foreach($facultades as $facultad)
                            <option value="{{ $facultad->id_facultad }}" {{ old('id_facultad') == $facultad->id_facultad ? 'selected' : '' }}>
                                {{ $facultad->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center gap-3">
                <a href="{{ route('carreras.index') }}" class="btn-secondary text-center">
                    Cancelar
                </a>
                <button type="submit" class="btn-primary flex-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Crear Carrera
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
