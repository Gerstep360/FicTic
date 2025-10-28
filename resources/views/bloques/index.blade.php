{{-- resources/views/bloques/index.blade.php --}}
@php
    $formatTime = static function (string $time): string {
        return \Illuminate\Support\Carbon::createFromFormat('H:i:s', $time)->format('H:i');
    };

    $formatDuration = static function (string $start, string $end): string {
        $minutes = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $start)->diffInMinutes(
            \Illuminate\Support\Carbon::createFromFormat('H:i:s', $end)
        );

        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        if ($hours === 0) {
            return sprintf('%02d min', $mins);
        }

        if ($mins === 0) {
            return sprintf('%02d h', $hours);
        }

        return sprintf('%02d h %02d min', $hours, $mins);
    };
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="min-w-0">
                <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                    Bloques horarios
                </h2>
                <p class="text-sm text-slate-400 mt-1">
                    Catalogo de bloques para organizar horarios academicos.
                </p>
            </div>

            @can('configurar_catalogos')
                <a href="{{ route('bloques.create') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nuevo bloque
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Filtros --}}
        <div class="card p-4 sm:p-6">
            <form method="GET" action="{{ route('bloques.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <div class="md:col-span-3 lg:col-span-4">
                    <label class="block text-sm font-medium text-slate-300 mb-1">Buscar</label>
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        class="input"
                        placeholder="Etiqueta o alias"
                        maxlength="20"
                    >
                </div>

                <div class="md:col-span-2 lg:col-span-1">
                    <label class="block text-sm font-medium text-slate-300 mb-1">Por pagina</label>
                    <select name="per_page" class="input">
                        @foreach([10, 20, 30, 50, 100] as $size)
                            <option value="{{ $size }}" {{ request('per_page', 20) == $size ? 'selected' : '' }}>
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-1 flex items-end">
                    <div class="flex w-full gap-2">
                        <button type="submit" class="btn-primary w-full sm:w-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Aplicar
                        </button>
                        <a href="{{ route('bloques.index') }}" class="btn-ghost w-full sm:w-auto">Limpiar</a>
                    </div>
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
                <h3 class="text-lg font-semibold text-slate-200">Listado de bloques</h3>
                <span class="text-sm text-slate-400">{{ $bloques->total() }} resultados</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Etiqueta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Inicio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Fin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Duracion</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Actualizado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse ($bloques as $bloque)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-slate-200 font-medium">
                                    {{ $bloque->etiqueta ?? 'Sin etiqueta' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-300">
                                    {{ $formatTime($bloque->hora_inicio) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-300">
                                    {{ $formatTime($bloque->hora_fin) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-300">
                                    {{ $formatDuration($bloque->hora_inicio, $bloque->hora_fin) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-400">
                                    {{ optional($bloque->updated_at)->diffForHumans() ?? 'N/D' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('bloques.show', $bloque) }}" class="btn-ghost">
                                            Ver
                                        </a>
                                        @can('configurar_catalogos')
                                            <a href="{{ route('bloques.edit', $bloque) }}" class="btn-ghost">
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
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    No se encontraron bloques con los criterios dados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($bloques->hasPages())
                <div class="px-6 py-4 border-t border-white/10">
                    {{ $bloques->links() }}
                </div>
            @endif
        </div>

        {{-- Cards mobile --}}
        <div class="lg:hidden space-y-3">
            @forelse ($bloques as $bloque)
                <div class="card p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-lg font-semibold text-slate-200">
                                {{ $bloque->etiqueta ?? 'Bloque #'.$bloque->id_bloque }}
                            </div>
                            <div class="mt-2 text-sm text-slate-300 space-y-1">
                                <div>
                                    <span class="text-slate-400">Inicio:</span>
                                    <span class="ml-1 text-slate-200">{{ $formatTime($bloque->hora_inicio) }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400">Fin:</span>
                                    <span class="ml-1 text-slate-200">{{ $formatTime($bloque->hora_fin) }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400">Duracion:</span>
                                    <span class="ml-1 text-slate-200">{{ $formatDuration($bloque->hora_inicio, $bloque->hora_fin) }}</span>
                                </div>
                            </div>
                            <div class="mt-3 text-xs text-slate-500">
                                Actualizado {{ optional($bloque->updated_at)->diffForHumans() ?? 'no disponible' }}
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('bloques.show', $bloque) }}" class="btn-ghost text-sm">
                                Ver
                            </a>
                            @can('configurar_catalogos')
                                <a href="{{ route('bloques.edit', $bloque) }}" class="btn-ghost text-sm">
                                    Editar
                                </a>
                                <form method="POST" action="{{ route('bloques.destroy', $bloque) }}" onsubmit="return confirm('Eliminar este bloque?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-ghost text-sm text-red-300 hover:text-red-200">
                                        Eliminar
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            @empty
                <div class="card p-8 text-center text-slate-400">
                    No se encontraron bloques.
                </div>
            @endforelse

            @if ($bloques->hasPages())
                <div>
                    {{ $bloques->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
