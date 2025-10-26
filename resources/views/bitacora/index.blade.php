<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                Bitácora del Sistema
            </h2>
            @can('ver_reportes')
            <a href="{{ route('bitacora.export', request()->query()) }}" class="btn-primary w-full sm:w-auto justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exportar CSV
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Filtros mejorados --}}
        <div class="card p-4 sm:p-6" x-data="{ showAdvanced: false }">
            <form method="GET" action="{{ route('bitacora.index') }}" class="space-y-4">
                {{-- Filtros básicos --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="q" class="block text-sm font-medium text-slate-300 mb-2">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Buscar
                        </label>
                        <input type="text" name="q" id="q" value="{{ request('q') }}" 
                               placeholder="Descripción, IP, URL..."
                               class="input">
                    </div>

                    <div>
                        <label for="modulo" class="block text-sm font-medium text-slate-300 mb-2">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            Módulo
                        </label>
                        <select name="modulo" id="modulo" class="input">
                            <option value="">Todos los módulos</option>
                            <option value="USUARIOS" {{ request('modulo') === 'USUARIOS' ? 'selected' : '' }}>Usuarios</option>
                            <option value="ROLES" {{ request('modulo') === 'ROLES' ? 'selected' : '' }}>Roles</option>
                            <option value="GESTIONES" {{ request('modulo') === 'GESTIONES' ? 'selected' : '' }}>Gestiones</option>
                            <option value="ASISTENCIAS" {{ request('modulo') === 'ASISTENCIAS' ? 'selected' : '' }}>Asistencias</option>
                            <option value="HORARIOS" {{ request('modulo') === 'HORARIOS' ? 'selected' : '' }}>Horarios</option>
                            <option value="IMPORTACION" {{ request('modulo') === 'IMPORTACION' ? 'selected' : '' }}>Importación</option>
                        </select>
                    </div>

                    <div>
                        <label for="accion" class="block text-sm font-medium text-slate-300 mb-2">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Acción
                        </label>
                        <select name="accion" id="accion" class="input">
                            <option value="">Todas las acciones</option>
                            <option value="CREAR" {{ request('accion') === 'CREAR' ? 'selected' : '' }}>Crear</option>
                            <option value="MODIFICAR" {{ request('accion') === 'MODIFICAR' ? 'selected' : '' }}>Modificar</option>
                            <option value="ELIMINAR" {{ request('accion') === 'ELIMINAR' ? 'selected' : '' }}>Eliminar</option>
                            <option value="ASIGNAR_ROLES" {{ request('accion') === 'ASIGNAR_ROLES' ? 'selected' : '' }}>Asignar Roles</option>
                            <option value="LOGIN" {{ request('accion') === 'LOGIN' ? 'selected' : '' }}>Inicio de Sesión</option>
                            <option value="LOGOUT" {{ request('accion') === 'LOGOUT' ? 'selected' : '' }}>Cierre de Sesión</option>
                        </select>
                    </div>

                    <div>
                        <label for="exitoso" class="block text-sm font-medium text-slate-300 mb-2">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Estado
                        </label>
                        <select name="exitoso" id="exitoso" class="input">
                            <option value="">Todos</option>
                            <option value="1" {{ request('exitoso') === '1' ? 'selected' : '' }}>Exitoso</option>
                            <option value="0" {{ request('exitoso') === '0' ? 'selected' : '' }}>Con Error</option>
                        </select>
                    </div>
                </div>

                {{-- Filtros avanzados (colapsables) --}}
                <div class="pt-4 border-t border-white/10">
                    <button type="button" @click="showAdvanced = !showAdvanced" class="text-sky-400 hover:text-sky-300 text-sm flex items-center gap-2">
                        <svg :class="{'rotate-90': showAdvanced}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span x-text="showAdvanced ? 'Ocultar filtros avanzados' : 'Mostrar filtros avanzados'"></span>
                    </button>

                    <div x-show="showAdvanced" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4"
                         style="display: none;">
                        <div>
                            <label for="fecha_desde" class="block text-sm font-medium text-slate-300 mb-2">Fecha Desde</label>
                            <input type="date" name="fecha_desde" id="fecha_desde" value="{{ request('fecha_desde') }}" class="input">
                        </div>

                        <div>
                            <label for="fecha_hasta" class="block text-sm font-medium text-slate-300 mb-2">Fecha Hasta</label>
                            <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ request('fecha_hasta') }}" class="input">
                        </div>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 pt-4 border-t border-white/10">
                    <button type="submit" class="btn-primary justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Aplicar Filtros
                    </button>
                    <a href="{{ route('bitacora.index') }}" class="btn-ghost justify-center">
                        Limpiar filtros
                    </a>
                </div>
            </form>
        </div>

        {{-- Lista de eventos --}}
        <div class="space-y-3">
            @forelse($bitacoras as $bitacora)
                <div class="card p-4 sm:p-6 hover:border-white/20 transition-all">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start gap-3 mb-3">
                                {{-- Icono de acción --}}
                                <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center
                                    {{ $bitacora->exitoso ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400' }}">
                                    @if($bitacora->accion === 'CREAR')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    @elseif($bitacora->accion === 'MODIFICAR')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    @elseif($bitacora->accion === 'ELIMINAR')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-slate-200 mb-1 break-words">
                                        {{ $bitacora->descripcion }}
                                    </h3>
                                    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-400">
                                        <span class="chip">{{ $bitacora->accion }}</span>
                                        @if($bitacora->modulo)
                                            <span class="chip">{{ $bitacora->modulo }}</span>
                                        @endif
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            {{ $bitacora->usuario->name ?? 'Sistema' }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $bitacora->created_at->format('d/m/Y H:i:s') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if($bitacora->ip || $bitacora->metodo)
                            <div class="flex flex-wrap gap-3 text-xs text-slate-500 pl-13">
                                @if($bitacora->ip)
                                    <span>IP: {{ $bitacora->ip }}</span>
                                @endif
                                @if($bitacora->metodo)
                                    <span>{{ $bitacora->metodo }}</span>
                                @endif
                            </div>
                            @endif
                        </div>

                        <a href="{{ route('bitacora.show', $bitacora) }}" class="btn-ghost text-sm flex-shrink-0">
                            Ver detalles
                        </a>
                    </div>
                </div>
            @empty
                <div class="card p-12 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-slate-300 mb-2">No hay eventos registrados</h3>
                    <p class="text-slate-500">Prueba ajustando los filtros de búsqueda</p>
                </div>
            @endforelse
        </div>

        {{-- Paginación --}}
        @if($bitacoras->hasPages())
            <div class="flex justify-center">
                {{ $bitacoras->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
