<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                {{ __('Carga Docente') }}
            </h2>
            @if(auth()->user()->can('registrar_carga_docente') || auth()->user()->hasRole('Admin DTIC'))
                <a href="{{ route('cargas-docentes.create') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Registrar Carga
                </a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Filtros --}}
        <div class="card p-4 sm:p-6">
            <form method="GET" action="{{ route('cargas-docentes.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
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
                
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Buscar docente..."
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Docente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Gestión</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Carrera</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-300 uppercase tracking-wider">Horas Contratadas</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-300 uppercase tracking-wider">Horas Asignadas</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-300 uppercase tracking-wider">Disponibles</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-300 uppercase tracking-wider">% Ocupación</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($cargas as $carga)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <div>
                                        <div class="font-medium text-slate-200">{{ $carga->docente->name }}</div>
                                        <div class="text-xs text-slate-400">{{ $carga->tipo_contrato ?? 'Sin especificar' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-300">{{ $carga->gestion->nombre }}</td>
                            <td class="px-6 py-4 text-slate-300">{{ $carga->carrera->nombre ?? 'General' }}</td>
                            <td class="px-6 py-4 text-center text-slate-300">{{ $carga->horas_contratadas }}h</td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-slate-300">{{ $carga->horas_asignadas }}h</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-medium {{ $carga->excedido ? 'text-red-400' : 'text-emerald-400' }}">
                                    {{ $carga->horas_disponibles }}h
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-16 h-2 bg-slate-700 rounded-full overflow-hidden">
                                        <div 
                                            class="h-full transition-all {{ $carga->excedido ? 'bg-red-500' : ($carga->porcentaje_ocupacion > 80 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                            style="width: {{ min(100, $carga->porcentaje_ocupacion) }}%"
                                        ></div>
                                    </div>
                                    <span class="text-sm {{ $carga->excedido ? 'text-red-400' : 'text-slate-300' }}">
                                        {{ $carga->porcentaje_ocupacion }}%
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('cargas-docentes.show', $carga) }}" class="text-blue-400 hover:text-blue-300">
                                    Ver
                                </a>
                                @if(auth()->user()->can('registrar_carga_docente') || auth()->user()->hasRole('Admin DTIC'))
                                    <a href="{{ route('cargas-docentes.edit', $carga) }}" class="ml-3 text-amber-400 hover:text-amber-300">
                                        Editar
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                No se encontraron cargas docentes
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Listado Mobile --}}
        <div class="lg:hidden space-y-4">
            @forelse($cargas as $carga)
                <div class="card p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <div>
                                <div class="font-medium text-slate-200">{{ $carga->docente->name }}</div>
                                <div class="text-xs text-slate-400">{{ $carga->gestion->nombre }}</div>
                            </div>
                        </div>
                        <span class="chip {{ $carga->excedido ? 'bg-red-500/20 text-red-400 border-red-500/30' : 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' }} text-xs">
                            {{ $carga->porcentaje_ocupacion }}%
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-3 mb-4 text-sm">
                        <div class="text-center">
                            <div class="text-slate-400 text-xs mb-1">Contratadas</div>
                            <div class="font-medium text-slate-200">{{ $carga->horas_contratadas }}h</div>
                        </div>
                        <div class="text-center">
                            <div class="text-slate-400 text-xs mb-1">Asignadas</div>
                            <div class="font-medium text-slate-200">{{ $carga->horas_asignadas }}h</div>
                        </div>
                        <div class="text-center">
                            <div class="text-slate-400 text-xs mb-1">Disponibles</div>
                            <div class="font-medium {{ $carga->excedido ? 'text-red-400' : 'text-emerald-400' }}">
                                {{ $carga->horas_disponibles }}h
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        <a href="{{ route('cargas-docentes.show', $carga) }}" class="flex-1 text-center py-2 px-4 bg-blue-500/20 text-blue-400 rounded-lg hover:bg-blue-500/30 transition-colors">
                            Ver
                        </a>
                        @if(auth()->user()->can('registrar_carga_docente') || auth()->user()->hasRole('Admin DTIC'))
                            <a href="{{ route('cargas-docentes.edit', $carga) }}" class="flex-1 text-center py-2 px-4 bg-amber-500/20 text-amber-400 rounded-lg hover:bg-amber-500/30 transition-colors">
                                Editar
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="card p-12 text-center text-slate-400">
                    No se encontraron cargas docentes
                </div>
            @endforelse
        </div>

        {{-- Paginación --}}
        <div class="card p-4">
            {{ $cargas->links() }}
        </div>
    </div>
</x-app-layout>
