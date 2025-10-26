<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                {{ __('Gestiones Académicas') }}
            </h2>
            @if(auth()->user()->can('abrir_gestion') || auth()->user()->hasRole('Admin DTIC'))
                <a href="{{ route('gestiones.create') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Abrir Gestión
                </a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Buscador --}}
        <div class="card p-4 sm:p-6">
            <form method="GET" action="{{ route('gestiones.index') }}" class="flex flex-col sm:flex-row gap-3">
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Buscar por nombre..."
                    class="input flex-1"
                >
                <select name="per_page" class="input w-full sm:w-32">
                    <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                    <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Fecha Inicio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Fecha Fin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($gestiones as $gestion)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="font-medium text-slate-200">{{ $gestion->nombre }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-300">
                                {{ \Carbon\Carbon::parse($gestion->fecha_inicio)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-300">
                                {{ \Carbon\Carbon::parse($gestion->fecha_fin)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($gestion->publicada)
                                    <span class="chip bg-emerald-500/20 text-emerald-400 border-emerald-500/30">
                                        Publicada
                                    </span>
                                @else
                                    <span class="chip bg-slate-500/20 text-slate-400 border-slate-500/30">
                                        No Publicada
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('gestiones.show', $gestion) }}" class="text-blue-400 hover:text-blue-300">
                                    Ver
                                </a>
                                @if(auth()->user()->can('abrir_gestion') || auth()->user()->hasRole('Admin DTIC'))
                                    <a href="{{ route('gestiones.edit', $gestion) }}" class="ml-3 text-amber-400 hover:text-amber-300">
                                        Editar
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                No se encontraron gestiones
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Listado Mobile --}}
        <div class="lg:hidden space-y-4">
            @forelse($gestiones as $gestion)
                <div class="card p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="font-medium text-slate-200">{{ $gestion->nombre }}</span>
                        </div>
                        @if($gestion->publicada)
                            <span class="chip bg-emerald-500/20 text-emerald-400 border-emerald-500/30 text-xs">
                                Publicada
                            </span>
                        @else
                            <span class="chip bg-slate-500/20 text-slate-400 border-slate-500/30 text-xs">
                                No Publicada
                            </span>
                        @endif
                    </div>
                    <div class="space-y-2 text-sm text-slate-300 mb-4">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Inicio:</span>
                            <span>{{ \Carbon\Carbon::parse($gestion->fecha_inicio)->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Fin:</span>
                            <span>{{ \Carbon\Carbon::parse($gestion->fecha_fin)->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('gestiones.show', $gestion) }}" class="flex-1 text-center py-2 px-4 bg-blue-500/20 text-blue-400 rounded-lg hover:bg-blue-500/30 transition-colors">
                            Ver
                        </a>
                        @if(auth()->user()->can('abrir_gestion') || auth()->user()->hasRole('Admin DTIC'))
                            <a href="{{ route('gestiones.edit', $gestion) }}" class="flex-1 text-center py-2 px-4 bg-amber-500/20 text-amber-400 rounded-lg hover:bg-amber-500/30 transition-colors">
                                Editar
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="card p-12 text-center text-slate-400">
                    No se encontraron gestiones
                </div>
            @endforelse
        </div>

        {{-- Paginación --}}
        <div class="card p-4">
            {{ $gestiones->links() }}
        </div>
    </div>
</x-app-layout>
