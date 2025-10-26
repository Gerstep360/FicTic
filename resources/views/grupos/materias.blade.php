{{-- resources/views/grupos/materias.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="min-w-0">
                <h2 class="font-semibold text-xl text-slate-200 leading-tight">Materias de la carrera</h2>
                <p class="text-sm text-slate-400 mt-1">
                    Carrera: <span class="font-medium text-slate-200">{{ $carrera->nombre }}</span>
                </p>
            </div>
            <span class="chip">Gestión #{{ $gestionId }}</span>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Filtros --}}
        <div class="card p-4 sm:p-6">
            <form method="GET" action="{{ route('grupos.materias', $carrera) }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-300 mb-1">Buscar</label>
                    <input class="input" type="text" name="q" value="{{ request('q') }}" placeholder="Código o nombre de materia...">
                </div>
                <div class="flex items-end gap-2">
                    <button class="btn-primary w-full md:w-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Aplicar
                    </button>
                    <a href="{{ route('grupos.materias', $carrera) }}" class="btn-ghost">Limpiar</a>
                </div>
            </form>
        </div>

        {{-- Grid de materias --}}
        <div class="card p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-200">Selecciona una materia</h3>
                <span class="text-sm text-slate-400">{{ $materias->total() }} resultados</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-4">
                @forelse($materias as $m)
                    <div class="rounded-2xl border border-white/10 bg-slate-900/60 p-4 hover:border-sky-500/30 transition">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm text-slate-400">{{ $m->codigo }}</div>
                                <div class="font-semibold text-slate-200 truncate" title="{{ $m->nombre }}">{{ $m->nombre }}</div>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <span class="chip">Créditos: {{ $m->creditos }}</span>
                                    <span class="chip">Grupos: {{ $m->grupos_en_gestion ?? 0 }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center gap-2">
                            <a href="{{ route('carreras.materias.grupos.index', [$carrera, $m, 'gestion' => $gestionId]) }}"
                               class="btn-ghost w-full justify-center">
                                Ver grupos
                            </a>

                            @canany(['crear_grupos','gestionar_grupos'])
                                <a href="{{ route('carreras.materias.grupos.create', [$carrera, $m]) }}"
                                   class="btn-primary w-full justify-center">
                                    Nuevo grupo
                                </a>
                            @endcanany
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-8 text-center text-slate-400">
                        No se encontraron materias.
                    </div>
                @endforelse
            </div>

            @if($materias->hasPages())
                <div class="mt-4">
                    {{ $materias->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
