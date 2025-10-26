{{-- resources/views/grupos/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-slate-200 leading-tight">Editar Grupo</h2>
                <p class="text-sm text-slate-400 mt-1">
                    {{ $carrera->nombre }} · {{ $materia->codigo }} — {{ $materia->nombre }}
                </p>
            </div>
            <a href="{{ route('carreras.materias.grupos.index', [$carrera, $materia]) }}" class="btn-ghost">Volver</a>
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-slate-200 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Actualizar datos
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

            <form method="POST" action="{{ route('carreras.materias.grupos.update', [$carrera, $materia, $grupo]) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Nombre del grupo *</label>
                    <input type="text" name="nombre_grupo" value="{{ old('nombre_grupo', $grupo->nombre_grupo) }}" class="input" required>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Turno *</label>
                        <select name="turno" class="input" required>
                            <option value="">Selecciona…</option>
                            @foreach($turnos as $t)
                                <option value="{{ $t }}" {{ old('turno', $grupo->turno)===$t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Modalidad *</label>
                        <select name="modalidad" class="input" required>
                            <option value="">Selecciona…</option>
                            @foreach($modalidades as $m)
                                <option value="{{ $m }}" {{ old('modalidad', $grupo->modalidad)===$m ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Cupo (opcional)</label>
                    <input type="number" name="cupo" value="{{ old('cupo', $grupo->cupo) }}" min="1" max="1000" class="input" placeholder="Ej. 40">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Docente (opcional)</label>
                    @php
                        $docentes = \App\Models\User::role('Docente')->orderBy('name')->get(['id','name','email']);
                    @endphp
                    <select name="id_docente" class="input">
                        <option value="">— Sin docente asignado —</option>
                        @foreach($docentes as $d)
                            <option value="{{ $d->id }}" {{ (string)old('id_docente', $grupo->id_docente) === (string)$d->id ? 'selected' : '' }}>
                                {{ $d->name }} — {{ $d->email }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center gap-3 pt-2">
                    <a href="{{ route('carreras.materias.grupos.index', [$carrera, $materia]) }}" class="btn-ghost">Cancelar</a>
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>

        {{-- Info lateral --}}
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-slate-200 mb-3">Detalle</h3>
            <div class="space-y-2 text-sm text-slate-400">
                <p><span class="chip mr-2">Grupo</span> {{ $grupo->nombre_grupo }}</p>
                <p><span class="chip mr-2">Turno</span> {{ $grupo->turno }}</p>
                <p><span class="chip mr-2">Modalidad</span> {{ $grupo->modalidad }}</p>
                <p><span class="chip mr-2">Cupo</span> {{ $grupo->cupo ?? '—' }}</p>
                <p>
                    <span class="chip mr-2">Docente</span>
                    @if($grupo->docente)
                        <span class="text-slate-200">{{ $grupo->docente->name }}</span>
                        <span class="text-slate-500">({{ $grupo->docente->email }})</span>
                    @else
                        <span class="text-slate-500">No asignado</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
