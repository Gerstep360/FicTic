{{-- resources/views/facultades/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                    {{ $facultad->nombre }}
                </h2>
                <p class="text-sm text-slate-400 mt-1">
                    Unidad Académica
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('facultades.index') }}" class="btn-ghost">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Volver
                </a>

                @if(auth()->user()->can('registrar_unidades_academicas') || auth()->user()->hasRole('Admin DTIC'))
                    <a href="{{ route('facultades.edit', $facultad) }}" class="btn-ghost">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    @php
        // Toma carreras precargadas (si el controlador hizo load) o consulta aquí.
        $carreras = $facultad->relationLoaded('carreras')
            ? $facultad->carreras
            : \App\Models\Carrera::where('id_facultad', $facultad->id_facultad)->orderBy('nombre')->get(['id_carrera','nombre','id_facultad']);
    @endphp

    <div class="space-y-6">

        {{-- Flash --}}
        @if (session('status'))
            <div class="card p-4">
                <span class="chip">{{ session('status') }}</span>
            </div>
        @endif

        {{-- Resumen --}}
        <div class="grid gap-4 md:grid-cols-3">
            <div class="card p-5">
                <div class="text-sm text-slate-400">Facultad</div>
                <div class="mt-1 text-lg font-semibold text-slate-200">{{ $facultad->nombre }}</div>
            </div>
            <div class="card p-5">
                <div class="text-sm text-slate-400">Total de carreras</div>
                <div class="mt-1 text-lg font-semibold text-slate-200">{{ $carreras->count() }}</div>
            </div>
            <div class="card p-5 flex items-center justify-between gap-3">
                <div>
                    <div class="text-sm text-slate-400">Acceso rápido</div>
                    <div class="mt-1 text-slate-300 text-sm">Ir al catálogo de carreras</div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('carreras.index', ['id_facultad' => $facultad->id_facultad]) }}" class="btn-ghost">Ver carreras</a>
                    @if(auth()->user()->can('registrar_unidades_academicas') || auth()->user()->hasRole('Admin DTIC'))
                        <a href="{{ route('carreras.create', ['id_facultad' => $facultad->id_facultad]) }}" class="btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Nueva Carrera
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Carreras: tabla desktop --}}
        <div class="hidden lg:block card overflow-hidden">
            <div class="px-6 py-4 bg-slate-800/40 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-200">Carreras</h3>
                <span class="text-sm text-slate-400">{{ $carreras->count() }} resultado(s)</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($carreras as $i => $c)
                            <tr class="hover:bg-slate-800/30">
                                <td class="px-6 py-4 text-slate-400">{{ $i+1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-200">{{ $c->nombre }}</div>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('carreras.show', $c->id_carrera) }}" class="btn-ghost">Ver</a>
                                    @can('gestionar_asignaturas')
                                        <a href="{{ route('carreras.index', ['id_facultad' => $facultad->id_facultad, 'q' => $c->nombre]) }}" class="btn-ghost">Asignaturas</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-slate-400">No hay carreras registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Carreras: cards mobile --}}
        <div class="lg:hidden space-y-3">
            @forelse($carreras as $i => $c)
                <div class="card p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-xs text-slate-500 mb-1">#{{ $i+1 }}</div>
                            <div class="text-lg font-semibold text-slate-200">{{ $c->nombre }}</div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('carreras.show', $c->id_carrera) }}" class="btn-ghost text-sm">Ver</a>
                            @can('gestionar_asignaturas')
                                <a href="{{ route('carreras.index', ['id_facultad' => $facultad->id_facultad, 'q' => $c->nombre]) }}" class="btn-ghost text-sm">Asignaturas</a>
                            @endcan
                        </div>
                    </div>
                </div>
            @empty
                <div class="card p-8 text-center text-slate-400">
                    No hay carreras registradas.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
