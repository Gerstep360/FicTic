<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                {{ __('Facultades') }}
            </h2>
            @if(auth()->user()->can('registrar_unidades_academicas') || auth()->user()->hasRole('Admin DTIC'))
                <a href="{{ route('facultades.create') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Facultad
                </a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Buscador --}}
        <div class="card p-4 sm:p-6">
            <form method="GET" action="{{ route('facultades.index') }}" class="flex flex-col sm:flex-row gap-3">
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Buscar por nombre..."
                    class="input flex-1"
                >
                <select name="per_page" class="input w-full sm:w-32">
                    <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
                <button type="submit" class="btn-primary whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Buscar
                </button>
            </form>
        </div>

        {{-- Listado Desktop --}}
        <div class="hidden lg:block card overflow-hidden">
            <table class="w-full">
                <thead class="bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($facultades as $facultad)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span class="font-medium text-slate-200">{{ $facultad->nombre }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('facultades.show', $facultad) }}" class="text-blue-400 hover:text-blue-300">
                                    Ver
                                </a>
                                @if(auth()->user()->can('registrar_unidades_academicas') || auth()->user()->hasRole('Admin DTIC'))
                                    <a href="{{ route('facultades.edit', $facultad) }}" class="ml-3 text-amber-400 hover:text-amber-300">
                                        Editar
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-12 text-center text-slate-400">
                                No se encontraron facultades
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Listado Mobile --}}
        <div class="lg:hidden space-y-4">
            @forelse($facultades as $facultad)
                <div class="card p-4">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="font-medium text-slate-200">{{ $facultad->nombre }}</span>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('facultades.show', $facultad) }}" class="flex-1 text-center py-2 px-4 bg-blue-500/20 text-blue-400 rounded-lg hover:bg-blue-500/30 transition-colors">
                            Ver
                        </a>
                        @if(auth()->user()->can('registrar_unidades_academicas') || auth()->user()->hasRole('Admin DTIC'))
                            <a href="{{ route('facultades.edit', $facultad) }}" class="flex-1 text-center py-2 px-4 bg-amber-500/20 text-amber-400 rounded-lg hover:bg-amber-500/30 transition-colors">
                                Editar
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="card p-12 text-center text-slate-400">
                    No se encontraron facultades
                </div>
            @endforelse
        </div>

        {{-- Paginación --}}
        <div class="card p-4">
            {{ $facultades->links() }}
        </div>
    </div>
</x-app-layout>
