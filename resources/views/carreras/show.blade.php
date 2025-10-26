{{-- resources/views/carreras/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                    {{ $carrera->nombre }}
                </h2>
                <p class="text-sm text-slate-400 mt-1">
                    Facultad:
                    <span class="chip">{{ $carrera->facultad->nombre ?? '—' }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('carreras.index', ['id_facultad' => $carrera->id_facultad]) }}" class="btn-ghost">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Volver
                </a>

                @if(auth()->user()->can('registrar_unidades_academicas') || auth()->user()->hasRole('Admin DTIC'))
                    <a href="{{ route('carreras.edit', $carrera) }}" class="btn-ghost">
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
        // Cargamos materias de la carrera (si no viene precargado)
        $materias = \App\Models\Materia::where('id_carrera', $carrera->id_carrera)
            ->orderBy('nivel')->orderBy('codigo')->get(['id_materia','codigo','nombre','nivel','creditos','id_carrera']);
    @endphp

    <div class="space-y-6">

        {{-- Flash --}}
        @if (session('status'))
            <div class="card p-4">
                <span class="chip">{{ session('status') }}</span>
            </div>
        @endif

        {{-- Resumen / Accesos rápidos --}}
        <div class="grid gap-4 md:grid-cols-3">
            <div class="card p-5">
                <div class="text-sm text-slate-400">Carrera</div>
                <div class="mt-1 text-lg font-semibold text-slate-200">{{ $carrera->nombre }}</div>
                <div class="mt-2 text-xs text-slate-500">ID #{{ $carrera->id_carrera }}</div>
            </div>
            <div class="card p-5">
                <div class="text-sm text-slate-400">Facultad</div>
                <div class="mt-1 text-lg font-semibold text-slate-200">{{ $carrera->facultad->nombre ?? '—' }}</div>
            </div>
            <div class="card p-5 flex items-center justify-between gap-3">
                <div>
                    <div class="text-sm text-slate-400">Materias en la carrera</div>
                    <div class="mt-1 text-lg font-semibold text-slate-200">{{ $materias->count() }}</div>
                </div>
                <div class="flex items-center gap-2">
                    @can('gestionar_grupos')
                        <a href="{{ route('grupos.materias', $carrera) }}" class="btn-primary">
                            Gestionar Grupos
                        </a>
                    @endcan
                    @can('gestionar_asignaturas')
                        <a href="{{ route('carreras.index', ['id_facultad' => $carrera->id_facultad, 'q' => $carrera->nombre]) }}" class="btn-ghost">
                            Gestionar Asignaturas
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Materias: tabla desktop --}}
        <div class="hidden lg:block card overflow-hidden">
            <div class="px-6 py-4 bg-slate-800/40 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-200">Materias de la carrera</h3>
                <span class="text-sm text-slate-400">{{ $materias->count() }} resultado(s)</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Nivel</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Créditos</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($materias as $m)
                            <tr class="hover:bg-slate-800/30">
                                <td class="px-6 py-3 font-medium text-slate-200">{{ $m->codigo }}</td>
                                <td class="px-6 py-3 text-slate-200">{{ $m->nombre }}</td>
                                <td class="px-6 py-3">
                                    <span class="chip">{{ $m->nivel }}</span>
                                </td>
                                <td class="px-6 py-3">{{ $m->creditos }}</td>
                                <td class="px-6 py-3 text-right whitespace-nowrap">
                                    @can('gestionar_grupos')
                                        <a href="{{ route('carreras.materias.grupos.index', [$carrera, $m]) }}" class="btn-ghost">
                                            Ver/Asignar Grupos
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    No hay materias registradas para esta carrera.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Materias: cards mobile --}}
        <div class="lg:hidden space-y-3">
            @forelse($materias as $m)
                <div class="card p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-xs text-slate-500 mb-1">{{ $m->codigo }}</div>
                            <div class="text-lg font-semibold text-slate-200">{{ $m->nombre }}</div>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="chip">{{ $m->nivel }}</span>
                                <span class="chip">Créditos: {{ $m->creditos }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            @can('gestionar_grupos')
                                <a href="{{ route('carreras.materias.grupos.index', [$carrera, $m]) }}" class="btn-ghost text-sm">
                                    Grupos
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            @empty
                <div class="card p-8 text-center text-slate-400">
                    No hay materias registradas para esta carrera.
                </div>
            @endforelse
        </div>

    </div>
</x-app-layout>
