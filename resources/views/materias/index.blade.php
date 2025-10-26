{{-- resources/views/materias/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('carreras.show', $carrera) }}" class="text-slate-400 hover:text-slate-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                        Asignaturas — {{ $carrera->nombre }}
                    </h2>
                    <p class="text-sm text-slate-400">
                        Catálogo de materias de la carrera
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if (session('status'))
                    <span class="chip">{{ session('status') }}</span>
                @endif
                <a href="{{ route('carreras.materias.create', $carrera) }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Asignatura
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Filtros --}}
        <div class="card p-4 sm:p-6">
            <form method="GET" action="{{ route('carreras.materias.index', $carrera) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Buscar por código o nombre…"
                    class="input"
                />

                <select name="per_page" class="input">
                    @foreach([20,50,100] as $n)
                        <option value="{{ $n }}" {{ (int)request('per_page',20) === $n ? 'selected' : '' }}>{{ $n }} por página</option>
                    @endforeach
                </select>

                <label class="flex items-center gap-3 rounded-xl border border-white/10 bg-slate-900/70 px-4">
                    <input type="checkbox" name="with_trash" value="1" class="checkbox"
                           {{ request('with_trash') ? 'checked' : '' }}>
                    <span class="text-sm text-slate-300">Incluir eliminadas</span>
                </label>

                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Buscar
                </button>

                <a href="{{ route('carreras.materias.index', $carrera) }}" class="btn-ghost">
                    Limpiar
                </a>
            </form>
        </div>

        {{-- Listado Desktop --}}
        <div class="hidden lg:block card overflow-hidden">
            <table class="w-full">
                <thead class="bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Créditos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Nivel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($materias as $m)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-200">{{ $m->codigo }}</td>
                            <td class="px-6 py-4 text-slate-300">{{ $m->nombre }}</td>
                            <td class="px-6 py-4 text-slate-300">{{ $m->creditos }}</td>
                            <td class="px-6 py-4 text-slate-300">{{ $m->nivel }}</td>
                            <td class="px-6 py-4">
                                @if($m->deleted_at)
                                    <span class="chip bg-red-500/10 border-red-500/20 text-red-300">Eliminada</span>
                                @else
                                    <span class="chip bg-emerald-500/10 border-emerald-500/20 text-emerald-300">Activa</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                @if(!$m->deleted_at)
                                    <a href="{{ route('carreras.materias.edit', [$carrera,$m]) }}" class="text-amber-400 hover:text-amber-300">Editar</a>
                                    <form method="POST" action="{{ route('carreras.materias.destroy', [$carrera,$m]) }}" class="inline-block ml-3"
                                          onsubmit="return confirm('¿Eliminar esta materia? (Baja lógica)');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300">Eliminar</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('carreras.materias.restore', [$carrera,$m->id_materia]) }}" class="inline-block"
                                          onsubmit="return confirm('¿Restaurar esta materia?');">
                                        @csrf
                                        <button type="submit" class="text-emerald-400 hover:text-emerald-300">Restaurar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                No se encontraron asignaturas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Listado Mobile --}}
        <div class="lg:hidden space-y-4">
            @forelse($materias as $m)
                <div class="card p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="font-semibold text-slate-200">{{ $m->codigo }}</div>
                            <div class="text-sm text-slate-400">{{ $m->nombre }}</div>
                            <div class="mt-2 flex items-center gap-2">
                                <span class="chip">Créditos: {{ $m->creditos }}</span>
                                <span class="chip">{{ $m->nivel }}</span>
                                @if($m->deleted_at)
                                    <span class="chip bg-red-500/10 border-red-500/20 text-red-300">Eliminada</span>
                                @else
                                    <span class="chip bg-emerald-500/10 border-emerald-500/20 text-emerald-300">Activa</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            @if(!$m->deleted_at)
                                <a href="{{ route('carreras.materias.edit', [$carrera,$m]) }}" class="btn-ghost text-sm">Editar</a>
                                <form method="POST" action="{{ route('carreras.materias.destroy', [$carrera,$m]) }}"
                                      onsubmit="return confirm('¿Eliminar esta materia? (Baja lógica)');">
                                    @csrf @method('DELETE')
                                    <button class="btn-ghost text-sm">Eliminar</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('carreras.materias.restore', [$carrera,$m->id_materia]) }}"
                                      onsubmit="return confirm('¿Restaurar esta materia?');">
                                    @csrf
                                    <button class="btn-ghost text-sm">Restaurar</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="card p-12 text-center text-slate-400">
                    No se encontraron asignaturas
                </div>
            @endforelse
        </div>

        {{-- Paginación --}}
        <div class="card p-4">
            {{ $materias->links() }}
        </div>
    </div>
</x-app-layout>
