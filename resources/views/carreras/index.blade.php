<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                {{ __('Carreras') }}
            </h2>
            @if(auth()->user()->can('registrar_unidades_academicas') || auth()->user()->hasRole('Admin DTIC'))
                <a href="{{ route('carreras.create') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Carrera
                </a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Filtros --}}
        <div class="card p-4 sm:p-6">
            <form method="GET" action="{{ route('carreras.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <select name="id_facultad" class="input">
                    <option value="">Todas las facultades</option>
                    @foreach($facultades as $fac)
                        <option value="{{ $fac->id_facultad }}" {{ request('id_facultad') == $fac->id_facultad ? 'selected' : '' }}>
                            {{ $fac->nombre }}
                        </option>
                    @endforeach
                </select>
                
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Buscar por nombre..."
                    class="input"
                >
                
                <select name="per_page" class="input">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Facultad</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($carreras as $carrera)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    <span class="font-medium text-slate-200">{{ $carrera->nombre }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-300">
                                {{ $carrera->facultad->nombre ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('carreras.show', $carrera) }}" class="text-blue-400 hover:text-blue-300">
                                    Ver
                                </a>
                                @if(auth()->user()->can('registrar_unidades_academicas') || auth()->user()->hasRole('Admin DTIC'))
                                    <a href="{{ route('carreras.edit', $carrera) }}" class="ml-3 text-amber-400 hover:text-amber-300">
                                        Editar
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-slate-400">
                                No se encontraron carreras
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Listado Mobile --}}
        <div class="lg:hidden space-y-4">
            @forelse($carreras as $carrera)
                <div class="card p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span class="font-medium text-slate-200">{{ $carrera->nombre }}</span>
                    </div>
                    <p class="text-sm text-slate-400 mb-4">{{ $carrera->facultad->nombre ?? '-' }}</p>
                    <div class="flex gap-2">
                        <a href="{{ route('carreras.show', $carrera) }}" class="flex-1 text-center py-2 px-4 bg-blue-500/20 text-blue-400 rounded-lg hover:bg-blue-500/30 transition-colors">
                            Ver
                        </a>
                        @if(auth()->user()->can('registrar_unidades_academicas') || auth()->user()->hasRole('Admin DTIC'))
                            <a href="{{ route('carreras.edit', $carrera) }}" class="flex-1 text-center py-2 px-4 bg-amber-500/20 text-amber-400 rounded-lg hover:bg-amber-500/30 transition-colors">
                                Editar
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="card p-12 text-center text-slate-400">
                    No se encontraron carreras
                </div>
            @endforelse
        </div>

        {{-- Paginación --}}
        <div class="card p-4">
            {{ $carreras->links() }}
        </div>
    </div>
</x-app-layout>
