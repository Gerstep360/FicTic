{{-- resources/views/bloques/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-slate-200 leading-tight">Nuevo bloque</h2>
                <p class="text-sm text-slate-400 mt-1">Registra un bloque horario dentro del catalogo.</p>
            </div>
            <a href="{{ route('bloques.index') }}" class="btn-ghost">Volver</a>
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-slate-200 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Datos del bloque
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

            <form method="POST" action="{{ route('bloques.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Hora inicio *</label>
                        <input
                            type="time"
                            name="hora_inicio"
                            value="{{ old('hora_inicio') }}"
                            class="input"
                            required
                            step="60"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Hora fin *</label>
                        <input
                            type="time"
                            name="hora_fin"
                            value="{{ old('hora_fin') }}"
                            class="input"
                            required
                            step="60"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Etiqueta (opcional)</label>
                    <input
                        type="text"
                        name="etiqueta"
                        value="{{ old('etiqueta') }}"
                        class="input"
                        maxlength="20"
                        placeholder="Ej. B1, Manana 1"
                    >
                </div>

                <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center gap-3 pt-2">
                    <a href="{{ route('bloques.index') }}" class="btn-ghost">Cancelar</a>
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Crear bloque
                    </button>
                </div>
            </form>
        </div>

        <div class="card p-6">
            <h3 class="text-lg font-semibold text-slate-200 mb-3">Sugerencias</h3>
            <ul class="text-sm text-slate-400 space-y-2">
                <li><span class="chip mr-2">Hora inicio/fin</span> Usa un intervalo valido y que no se traslape con bloques existentes.</li>
                <li><span class="chip mr-2">Duracion</span> Asegura que la duracion total responde al plan de clases.</li>
                <li><span class="chip mr-2">Etiqueta</span> Facilita la identificacion rapida, por ejemplo <code>B1</code> o <code>Turno A</code>.</li>
                <li><span class="chip mr-2">Orden</span> Los listados se ordenan por hora de inicio automaticamente.</li>
            </ul>
        </div>
    </div>
</x-app-layout>
