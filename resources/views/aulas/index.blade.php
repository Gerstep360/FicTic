{{-- resources/views/aulas/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="min-w-0">
                <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                    Aulas y Capacidades
                </h2>
                <p class="text-sm text-slate-400 mt-1">
                    Catálogo de aulas disponibles para la programación académica.
                </p>
            </div>

            <div class="flex items-center gap-2">
                @canany(['crear_aulas','gestionar_aulas'])
                <a href="{{ route('aulas.create') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Aula
                </a>
                @endcanany
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Filtros --}}
        <div class="card p-4 sm:p-6">
            <form method="GET" action="{{ route('aulas.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-300 mb-1">Buscar</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="input" placeholder="Código, edificio o tipo…">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Tipo</label>
                    <select name="tipo" class="input">
                        <option value="">Todos</option>
                        @foreach($tipos as $t)
                            <option value="{{ $t }}" {{ request('tipo')===$t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Capacidad mín.</label>
                    <input type="number" name="cap_min" min="0" max="5000" value="{{ request('cap_min') }}" class="input" placeholder="Ej. 30">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Capacidad máx.</label>
                    <input type="number" name="cap_max" min="0" max="5000" value="{{ request('cap_max') }}" class="input" placeholder="Ej. 80">
                </div>

                <div class="flex flex-col justify-end">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-300 mb-2">
                        <input type="checkbox" name="with_trash" class="checkbox" value="1" {{ request()->boolean('with_trash') ? 'checked' : '' }}>
                        Ver eliminadas
                    </label>
                    <div class="flex items-center gap-2">
                        <button class="btn-primary w-full md:w-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Aplicar
                        </button>
                        <a href="{{ route('aulas.index') }}" class="btn-ghost">Limpiar</a>
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
                <h3 class="text-lg font-semibold text-slate-200">Listado de aulas</h3>
                <span class="text-sm text-slate-400">{{ $aulas->total() }} resultados</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Capacidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Edificio/Módulo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse ($aulas as $a)
                            <tr class="hover:bg-slate-800/30">
                                <td class="px-6 py-4 font-medium text-slate-200">{{ $a->codigo }}</td>
                                <td class="px-6 py-4"><span class="chip">{{ $a->tipo }}</span></td>
                                <td class="px-6 py-4">{{ $a->capacidad ?? '—' }}</td>
                                <td class="px-6 py-4">{{ $a->edificio ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    @if(method_exists($a,'trashed') && $a->trashed())
                                        <span class="chip">Eliminada</span>
                                    @else
                                        <span class="chip">Activa</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    @if(method_exists($a,'trashed') && $a->trashed())
                                        @canany(['restaurar_aulas','gestionar_aulas'])
                                        <form method="POST" action="{{ route('aulas.restore', $a->id_aula) }}" class="inline-flex"
                                              onsubmit="return confirm('¿Restaurar esta aula?');">
                                            @csrf
                                            <button type="submit" class="btn-ghost">Restaurar</button>
                                        </form>
                                        @endcanany
                                    @else
                                        @canany(['editar_aulas','gestionar_aulas'])
                                        <a href="{{ route('aulas.edit', $a) }}" class="btn-ghost">Editar</a>
                                        @endcanany
                                        @canany(['eliminar_aulas','gestionar_aulas'])
                                        <form method="POST" action="{{ route('aulas.destroy', $a) }}" class="inline-flex"
                                              onsubmit="return confirm('¿Eliminar esta aula?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-ghost">Eliminar</button>
                                        </form>
                                        @endcanany
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    No se encontraron aulas con los criterios aplicados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($aulas->hasPages())
                <div class="px-6 py-4 border-t border-white/10">
                    {{ $aulas->links() }}
                </div>
            @endif
        </div>

        {{-- Cards mobile --}}
        <div class="lg:hidden space-y-3">
            @forelse ($aulas as $a)
                <div class="card p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-lg font-semibold text-slate-200">{{ $a->codigo }}</div>
                            <div class="flex flex-wrap gap-2 mt-2">
                                <span class="chip">{{ $a->tipo }}</span>
                                <span class="chip">Cap: {{ $a->capacidad ?? '—' }}</span>
                                @if(method_exists($a,'trashed') && $a->trashed())
                                    <span class="chip">Eliminada</span>
                                @else
                                    <span class="chip">Activa</span>
                                @endif
                            </div>
                            <div class="mt-2 text-sm text-slate-400">
                                Edificio/Módulo: <span class="text-slate-200">{{ $a->edificio ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            @if(method_exists($a,'trashed') && $a->trashed())
                                @canany(['restaurar_aulas','gestionar_aulas'])
                                <form method="POST" action="{{ route('aulas.restore', $a->id_aula) }}" onsubmit="return confirm('¿Restaurar esta aula?');">
                                    @csrf
                                    <button type="submit" class="btn-ghost text-sm">Restaurar</button>
                                </form>
                                @endcanany
                            @else
                                @canany(['editar_aulas','gestionar_aulas'])
                                <a href="{{ route('aulas.edit', $a) }}" class="btn-ghost text-sm">Editar</a>
                                @endcanany
                                @canany(['eliminar_aulas','gestionar_aulas'])
                                <form method="POST" action="{{ route('aulas.destroy', $a) }}" onsubmit="return confirm('¿Eliminar esta aula?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-ghost text-sm">Eliminar</button>
                                </form>
                                @endcanany
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="card p-8 text-center text-slate-400">
                    No se encontraron aulas.
                </div>
            @endforelse

            @if($aulas->hasPages())
                <div>
                    {{ $aulas->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
