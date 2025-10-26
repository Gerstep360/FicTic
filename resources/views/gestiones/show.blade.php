{{-- resources/views/gestiones/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('gestiones.index') }}" class="text-slate-400 hover:text-slate-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                        Gestión: {{ $gestion->nombre }}
                    </h2>
                    @php
                        $fi   = \Carbon\Carbon::parse($gestion->fecha_inicio);
                        $ff   = \Carbon\Carbon::parse($gestion->fecha_fin);
                        $hoy  = \Carbon\Carbon::today();
                        $activa = $hoy->greaterThanOrEqualTo($fi) && $hoy->lessThanOrEqualTo($ff);
                    @endphp
                    <p class="text-sm text-slate-400 mt-1">
                        {{ $fi->format('d/m/Y') }} &rarr; {{ $ff->format('d/m/Y') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if($gestion->publicada)
                    <span class="chip bg-emerald-500/20 text-emerald-400 border-emerald-500/30">Publicada</span>
                @else
                    <span class="chip bg-slate-500/20 text-slate-400 border-slate-500/30">No Publicada</span>
                @endif
                @if($activa)
                    <span class="chip bg-sky-500/20 text-sky-400 border-sky-500/30">Activa hoy</span>
                @endif

                @if(auth()->user()->can('abrir_gestion') || auth()->user()->hasRole('Admin DTIC'))
                    <a href="{{ route('gestiones.edit', $gestion) }}" class="btn-ghost">
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
        $feriados = \App\Models\Feriado::where('id_gestion', $gestion->id_gestion)
            ->orderBy('fecha')
            ->get(['fecha','descripcion']);
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
                <div class="text-sm text-slate-400">Nombre</div>
                <div class="mt-1 text-lg font-semibold text-slate-200">{{ $gestion->nombre }}</div>
            </div>
            <div class="card p-5">
                <div class="text-sm text-slate-400">Rango</div>
                <div class="mt-1 text-lg font-semibold text-slate-200">
                    {{ $fi->format('d/m/Y') }} — {{ $ff->format('d/m/Y') }}
                </div>
            </div>
            <div class="card p-5">
                <div class="text-sm text-slate-400">Feriados</div>
                <div class="mt-1 text-lg font-semibold text-slate-200">{{ $feriados->count() }}</div>
            </div>
        </div>

        {{-- Feriados --}}
        <div class="card overflow-hidden">
            <div class="px-6 py-4 bg-slate-800/40 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-200">Feriados de la gestión</h3>
                <span class="text-sm text-slate-400">{{ $feriados->count() }} registro(s)</span>
            </div>

            @if($feriados->isEmpty())
                <div class="p-8 text-center text-slate-400">
                    No hay feriados registrados.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-800/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Descripción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            @foreach($feriados as $f)
                                <tr class="hover:bg-slate-800/30">
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <span class="chip">{{ \Carbon\Carbon::parse($f->fecha)->format('d/m/Y') }}</span>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="text-slate-200">{{ $f->descripcion ?: '—' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if(auth()->user()->can('abrir_gestion') || auth()->user()->hasRole('Admin DTIC'))
                <div class="px-6 py-4 border-t border-white/10 flex items-center justify-end">
                    <a href="{{ route('gestiones.edit', $gestion) }}" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar gestión
                    </a>
                </div>
            @endif
        </div>

        {{-- Metadatos --}}
        <div class="card p-5">
            <h4 class="text-slate-300 font-semibold mb-3">Metadatos</h4>
            <div class="grid gap-3 sm:grid-cols-3 text-sm">
                <div>
                    <div class="text-slate-400">ID</div>
                    <div class="text-slate-200 mt-0.5">#{{ $gestion->id_gestion }}</div>
                </div>
                <div>
                    <div class="text-slate-400">Creado</div>
                    <div class="text-slate-200 mt-0.5">{{ optional($gestion->created_at)->format('d/m/Y H:i') }}</div>
                </div>
                <div>
                    <div class="text-slate-400">Actualizado</div>
                    <div class="text-slate-200 mt-0.5">{{ optional($gestion->updated_at)->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
