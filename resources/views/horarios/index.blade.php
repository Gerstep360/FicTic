<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                {{ __('Asignación de Horarios') }}
            </h2>
            @if(auth()->user()->can('asignar_horarios') || auth()->user()->hasRole('Admin DTIC'))
                <a href="{{ route('horarios.create') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Asignar Horario
                </a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Filtros --}}
        <div class="card p-4 sm:p-6">
            <form method="GET" action="{{ route('horarios.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <select name="id_gestion" class="input">
                    <option value="">Todas las gestiones</option>
                    @foreach($gestiones as $g)
                        <option value="{{ $g->id_gestion }}" {{ request('id_gestion') == $g->id_gestion ? 'selected' : '' }}>
                            {{ $g->nombre }}
                        </option>
                    @endforeach
                </select>
                
                <select name="id_carrera" class="input">
                    <option value="">Todas las carreras</option>
                    @foreach($carreras as $c)
                        <option value="{{ $c->id_carrera }}" {{ request('id_carrera') == $c->id_carrera ? 'selected' : '' }}>
                            {{ $c->nombre }}
                        </option>
                    @endforeach
                </select>
                
                <select name="id_aula" class="input">
                    <option value="">Todas las aulas</option>
                    @foreach($aulas as $a)
                        <option value="{{ $a->id_aula }}" {{ request('id_aula') == $a->id_aula ? 'selected' : '' }}>
                            {{ $a->codigo }} ({{ $a->tipo }})
                        </option>
                    @endforeach
                </select>
                
                <select name="id_docente" class="input">
                    <option value="">Todos los docentes</option>
                    @foreach($docentes as $d)
                        <option value="{{ $d->id }}" {{ request('id_docente') == $d->id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
                
                <button type="submit" class="btn-primary whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filtrar
                </button>
            </form>
        </div>

        {{-- Cuadrícula de horarios --}}
        <div class="card overflow-x-auto">
            @if(empty($matriz))
                <div class="p-12 text-center text-slate-400">
                    <svg class="w-16 h-16 mx-auto mb-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-lg">No hay horarios asignados con los filtros actuales</p>
                    <p class="text-sm mt-2">Selecciona una gestión y carrera para comenzar</p>
                </div>
            @else
                <table class="w-full">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-300 uppercase">Día/Bloque</th>
                            @foreach($bloques as $bloque)
                                <th class="px-4 py-3 text-center text-xs font-medium text-slate-300 uppercase min-w-[200px]">
                                    <div>{{ $bloque->etiqueta ?? "Bloque {$bloque->id_bloque}" }}</div>
                                    <div class="text-xs text-slate-400 font-normal">
                                        {{ \Carbon\Carbon::parse($bloque->hora_inicio)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($bloque->hora_fin)->format('H:i') }}
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @foreach([1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado'] as $dia => $nombreDia)
                            <tr class="hover:bg-slate-800/30">
                                <td class="px-4 py-3 font-medium text-slate-200 whitespace-nowrap">
                                    {{ $nombreDia }}
                                </td>
                                @foreach($bloques as $bloque)
                                    <td class="px-2 py-2 text-center border-l border-slate-700/50">
                                        @php
                                            $horario = $matriz[$dia]['bloques'][$bloque->id_bloque] ?? null;
                                        @endphp
                                        
                                        @if($horario)
                                            <a href="{{ route('horarios.show', $horario) }}" 
                                               class="block p-3 rounded-lg bg-gradient-to-br from-blue-500/20 to-indigo-500/20 border border-blue-500/30 hover:border-blue-400/50 transition-all group">
                                                <div class="text-sm font-medium text-slate-200 mb-1 group-hover:text-blue-300">
                                                    {{ $horario->grupo->materia->nombre }}
                                                </div>
                                                <div class="text-xs text-slate-400 space-y-1">
                                                    <div class="flex items-center justify-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                        </svg>
                                                        {{ $horario->grupo->nombre_grupo }}
                                                    </div>
                                                    <div class="flex items-center justify-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                        </svg>
                                                        {{ $horario->docente->name }}
                                                    </div>
                                                    <div class="flex items-center justify-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                        </svg>
                                                        {{ $horario->aula->codigo }}
                                                    </div>
                                                </div>
                                            </a>
                                        @else
                                            <div class="p-3 rounded-lg bg-slate-800/30 border border-dashed border-slate-700/50 text-slate-600 text-xs">
                                                Libre
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Leyenda --}}
        <div class="card p-4">
            <h3 class="text-sm font-medium text-slate-300 mb-3">Leyenda:</h3>
            <div class="flex flex-wrap gap-4 text-sm text-slate-400">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded bg-gradient-to-br from-blue-500/20 to-indigo-500/20 border border-blue-500/30"></div>
                    <span>Horario asignado</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded bg-slate-800/30 border border-dashed border-slate-700/50"></div>
                    <span>Espacio libre</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
