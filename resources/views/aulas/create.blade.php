{{-- resources/views/aulas/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-slate-200 leading-tight">Nueva Aula</h2>
                <p class="text-sm text-slate-400 mt-1">Registra una nueva aula en el catálogo.</p>
            </div>
            <a href="{{ route('aulas.index') }}" class="btn-ghost">Volver</a>
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-slate-200 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Datos del aula
            </h3>

            @if ($errors->any())
                <div class="mb-4 p-4 rounded-xl bg-red-500/10 border border-red-500/20">
                    <h4 class="text-red-400 font-medium mb-1">Corrige los campos:</h4>
                    <ul class="list-disc list-inside text-sm text-red-300 space-y-1">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('aulas.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Código *</label>
                    <input type="text" name="codigo" value="{{ old('codigo') }}" class="input" placeholder="Ej. 236-05" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Tipo *</label>
                    <select name="tipo" class="input" required>
                        <option value="">Selecciona…</option>
                        @foreach($tipos as $t)
                            <option value="{{ $t }}" {{ old('tipo')===$t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Capacidad (opcional)</label>
                        <input type="number" name="capacidad" min="1" max="5000" value="{{ old('capacidad') }}" class="input" placeholder="Ej. 40">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Edificio/Módulo (opcional)</label>
                        <input type="text" name="edificio" value="{{ old('edificio') }}" class="input" placeholder="Ej. Módulo 236">
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center gap-3 pt-2">
                    <a href="{{ route('aulas.index') }}" class="btn-ghost">Cancelar</a>
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Crear aula
                    </button>
                </div>
            </form>
        </div>

        <div class="card p-6">
            <h3 class="text-lg font-semibold text-slate-200 mb-3">Ayuda</h3>
            <ul class="text-sm text-slate-400 space-y-2">
                <li><span class="chip mr-2">Código</span> Usa un formato consistente (p. ej. <code>236-05</code>).</li>
                <li><span class="chip mr-2">Tipo</span> Teórica, Laboratorio, Computación o Auditorio.</li>
                <li><span class="chip mr-2">Capacidad</span> Aforo aproximado de estudiantes.</li>
                <li><span class="chip mr-2">Edificio</span> Indica el módulo o edificio para ubicarla fácilmente.</li>
            </ul>
        </div>
    </div>
</x-app-layout>
