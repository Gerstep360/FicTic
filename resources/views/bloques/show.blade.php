{{-- resources/views/bloques/show.blade.php --}}
@php
    $formatTime = static function (string $time): string {
        return \Illuminate\Support\Carbon::createFromFormat('H:i:s', $time)->format('H:i');
    };

    $minutes = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $bloque->hora_inicio)
        ->diffInMinutes(\Illuminate\Support\Carbon::createFromFormat('H:i:s', $bloque->hora_fin));

    $durationText = $minutes >= 60
        ? sprintf('%02d h %02d min', intdiv($minutes, 60), $minutes % 60)
        : sprintf('%02d min', $minutes);
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('bloques.index') }}" class="text-slate-400 hover:text-slate-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                        Bloque {{ $bloque->etiqueta ?? '#'.$bloque->id_bloque }}
                    </h2>
                    <p class="text-sm text-slate-400 mt-1">
                        {{ $formatTime($bloque->hora_inicio) }} &rarr; {{ $formatTime($bloque->hora_fin) }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @can('configurar_catalogos')
                    <a href="{{ route('bloques.edit', $bloque) }}" class="btn-ghost">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar
                    </a>
                    <form method="POST" action="{{ route('bloques.destroy', $bloque) }}" onsubmit="return confirm('Eliminar este bloque?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-ghost text-red-300 hover:text-red-200">
                            Eliminar
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

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
                <div class="text-sm text-slate-400">Etiqueta</div>
                <div class="mt-1 text-lg font-semibold text-slate-200">
                    {{ $bloque->etiqueta ?? 'Sin etiqueta' }}
                </div>
            </div>
            <div class="card p-5">
                <div class="text-sm text-slate-400">Duracion</div>
                <div class="mt-1 text-lg font-semibold text-slate-200">{{ $durationText }}</div>
            </div>
            <div class="card p-5">
                <div class="text-sm text-slate-400">Ultima actualizacion</div>
                <div class="mt-1 text-lg font-semibold text-slate-200">
                    {{ optional($bloque->updated_at)->diffForHumans() ?? 'Sin registro' }}
                </div>
            </div>
        </div>

        {{-- Detalle --}}
        <div class="card p-6 space-y-4">
            <h3 class="text-lg font-semibold text-slate-200">Detalle del bloque</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl bg-slate-800/40 border border-white/10 p-4">
                    <div class="text-sm text-slate-400">Hora de inicio</div>
                    <div class="mt-1 text-xl font-semibold text-slate-100">
                        {{ $formatTime($bloque->hora_inicio) }}
                    </div>
                </div>
                <div class="rounded-xl bg-slate-800/40 border border-white/10 p-4">
                    <div class="text-sm text-slate-400">Hora de fin</div>
                    <div class="mt-1 text-xl font-semibold text-slate-100">
                        {{ $formatTime($bloque->hora_fin) }}
                    </div>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3 text-sm text-slate-300">
                <div>
                    <div class="text-slate-400 uppercase tracking-wide text-xs">ID</div>
                    <div class="mt-1 text-slate-100 font-medium">#{{ $bloque->id_bloque }}</div>
                </div>
                <div>
                    <div class="text-slate-400 uppercase tracking-wide text-xs">Creado</div>
                    <div class="mt-1 text-slate-100 font-medium">
                        {{ optional($bloque->created_at)->format('d/m/Y H:i') ?? 'Sin registro' }}
                    </div>
                </div>
                <div>
                    <div class="text-slate-400 uppercase tracking-wide text-xs">Actualizado</div>
                    <div class="mt-1 text-slate-100 font-medium">
                        {{ optional($bloque->updated_at)->format('d/m/Y H:i') ?? 'Sin registro' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Nota --}}
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-slate-200 mb-3">Recomendaciones</h3>
            <ul class="text-sm text-slate-400 space-y-2">
                <li>Verifica los horarios de clases que dependan de este bloque antes de realizar cambios mayores.</li>
                <li>Los bloques se ordenan por la hora de inicio dentro de los listados y reportes.</li>
                <li>Usa etiquetas consistentes para facilitar la comunicacion con docentes y estudiantes.</li>
            </ul>
        </div>
    </div>
</x-app-layout>
