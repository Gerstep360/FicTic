{{-- resources/views/grupos/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-slate-200 leading-tight">Nuevo Grupo</h2>
                <p class="text-sm text-slate-400 mt-1">
                    {{ $carrera->nombre }} · {{ $materia->codigo }} — {{ $materia->nombre }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="chip">Gestión #{{ $gestionId }}</span>
                <a href="{{ route('carreras.materias.grupos.index', [$carrera, $materia]) }}" class="btn-ghost">Volver</a>
            </div>
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-slate-200 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Datos del grupo
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

            <form method="POST" action="{{ route('carreras.materias.grupos.store', [$carrera, $materia]) }}" class="space-y-5">
                @csrf
                <input type="hidden" name="id_gestion" value="{{ $gestionId }}"/>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Nombre del grupo *</label>
                    <input type="text" name="nombre_grupo" value="{{ old('nombre_grupo') }}" placeholder="GR1, A, B..."
                           class="input" required>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Turno *</label>
                        <select name="turno" class="input" required>
                            <option value="">Selecciona…</option>
                            @foreach($turnos as $t)
                                <option value="{{ $t }}" {{ old('turno')===$t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Modalidad *</label>
                        <select name="modalidad" class="input" required>
                            <option value="">Selecciona…</option>
                            @foreach($modalidades as $m)
                                <option value="{{ $m }}" {{ old('modalidad')===$m ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Cupo (opcional)</label>
                    <input type="number" name="cupo" value="{{ old('cupo') }}" min="1" max="1000" class="input" placeholder="Ej. 40">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Docente (opcional)</label>
                    @php
                        $docentes = \App\Models\User::role('Docente')->orderBy('name')->get(['id','name','email']);
                    @endphp
                    <select name="id_docente" class="input">
                        <option value="">— Sin docente asignado —</option>
                        @foreach($docentes as $d)
                            <option value="{{ $d->id }}" {{ old('id_docente') == $d->id ? 'selected' : '' }}>
                                {{ $d->name }} — {{ $d->email }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-2">Puedes dejarlo vacío y asignarlo más tarde.</p>
                </div>

                <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center gap-3 pt-2">
                    <a href="{{ route('carreras.materias.grupos.index', [$carrera, $materia]) }}" class="btn-ghost">Cancelar</a>
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Crear grupo
                    </button>
                </div>
            </form>
        </div>

        {{-- Info lateral --}}
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-slate-200 mb-3">Ayuda</h3>
            <ul class="text-sm text-slate-400 space-y-2">
                <li><span class="chip mr-2">Turno</span> Selecciona Mañana, Tarde o Noche.</li>
                <li><span class="chip mr-2">Modalidad</span> Presencial, Virtual o Laboratorio.</li>
                <li><span class="chip mr-2">Cupo</span> Número esperado de estudiantes (opcional).</li>
                <li><span class="chip mr-2">Docente</span> Puedes dejarlo sin asignar por ahora.</li>
            </ul>
        </div>
    </div>
</x-app-layout>
