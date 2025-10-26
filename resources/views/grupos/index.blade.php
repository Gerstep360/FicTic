{{-- resources/views/grupos/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="min-w-0">
                <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                    Grupos / Paralelos
                </h2>
                <p class="text-sm text-slate-400 mt-1">
                    Carrera: <span class="font-medium text-slate-200">{{ $carrera->nombre }}</span>
                    <span class="mx-2">•</span>
                    Materia: <span class="font-medium text-slate-200">{{ $materia->codigo }} — {{ $materia->nombre }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span class="chip">Gestión #{{ $gestionId }}</span>
                @canany(['crear_grupos','gestionar_grupos'])
                <a href="{{ route('carreras.materias.grupos.create', [$carrera, $materia]) }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nuevo Grupo
                </a>
                @endcanany
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Filtros --}}
        <div class="card p-4 sm:p-6">
            <form method="GET" action="{{ route('carreras.materias.grupos.index', [$carrera, $materia]) }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-300 mb-1">Buscar</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Nombre de grupo (p.ej. GR1, A, B)..."
                           class="input" />
                    <input type="hidden" name="gestion" value="{{ $gestionId }}">
                </div>
                <div class="flex items-end gap-2">
                    <button class="btn-primary w-full md:w-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Aplicar
                    </button>
                    <a href="{{ route('carreras.materias.grupos.index', [$carrera, $materia]) }}" class="btn-ghost">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        {{-- Flash --}}
        @if (session('status'))
            <div class="card p-4">
                <span class="chip">{{ session('status') }}</span>
            </div>
        @endif

        {{-- Tabla desktop --}}
        <div class="card overflow-hidden hidden lg:block">
            <div class="px-6 py-4 bg-slate-800/40 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-200">Listado de grupos</h3>
                <span class="text-sm text-slate-400">{{ $grupos->total() }} resultados</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Grupo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Turno</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Modalidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Cupo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Docente</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse ($grupos as $g)
                            <tr class="hover:bg-slate-800/30">
                                <td class="px-6 py-4 font-medium text-slate-200">{{ $g->nombre_grupo }}</td>
                                <td class="px-6 py-4">
                                    <span class="chip">{{ $g->turno }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="chip">{{ $g->modalidad }}</span>
                                </td>
                                <td class="px-6 py-4">{{ $g->cupo ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    @if($g->docente)
                                        <span class="text-slate-200">{{ $g->docente->name }}</span>
                                        <span class="text-slate-500 text-xs block">{{ $g->docente->email }}</span>
                                    @else
                                        <span class="text-slate-400">No asignado</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    @canany(['editar_grupos','gestionar_grupos'])
                                    <a href="{{ route('carreras.materias.grupos.edit', [$carrera, $materia, $g]) }}" class="btn-ghost">Editar</a>
                                    @endcanany
                                    @canany(['eliminar_grupos','gestionar_grupos'])
                                    <form method="POST" action="{{ route('carreras.materias.grupos.destroy', [$carrera, $materia, $g]) }}"
                                          class="inline-flex"
                                          onsubmit="return confirm('¿Eliminar este grupo?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-ghost">Eliminar</button>
                                    </form>
                                    @endcanany
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    No hay grupos registrados para esta materia (gestión #{{ $gestionId }}).
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($grupos->hasPages())
                <div class="px-6 py-4 border-t border-white/10">
                    {{ $grupos->links() }}
                </div>
            @endif
        </div>

        {{-- Cards mobile --}}
        <div class="lg:hidden space-y-3">
            @forelse ($grupos as $g)
                <div class="card p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-lg font-semibold text-slate-200">{{ $g->nombre_grupo }}</div>
                            <div class="flex flex-wrap gap-2 mt-2">
                                <span class="chip">{{ $g->turno }}</span>
                                <span class="chip">{{ $g->modalidad }}</span>
                                <span class="chip">Cupo: {{ $g->cupo ?? '—' }}</span>
                            </div>
                            <div class="mt-2 text-sm text-slate-400">
                                @if($g->docente)
                                    Docente: <span class="text-slate-200">{{ $g->docente->name }}</span>
                                @else
                                    Docente: <span class="text-slate-500">No asignado</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            @canany(['editar_grupos','gestionar_grupos'])
                            <a href="{{ route('carreras.materias.grupos.edit', [$carrera, $materia, $g]) }}" class="btn-ghost text-sm">Editar</a>
                            @endcanany
                            @canany(['eliminar_grupos','gestionar_grupos'])
                            <form method="POST" action="{{ route('carreras.materias.grupos.destroy', [$carrera, $materia, $g]) }}" onsubmit="return confirm('¿Eliminar este grupo?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-ghost text-sm">Eliminar</button>
                            </form>
                            @endcanany
                        </div>
                    </div>
                </div>
            @empty
                <div class="card p-8 text-center text-slate-400">
                    No hay grupos registrados (gestión #{{ $gestionId }}).
                </div>
            @endforelse

            @if($grupos->hasPages())
                <div>
                    {{ $grupos->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
