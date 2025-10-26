<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                {{ __('Roles y Permisos') }}
            </h2>
            <a href="{{ route('roles.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Crear Rol
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Buscador --}}
        <div class="card p-6">
            <form method="GET" action="{{ route('roles.index') }}" class="flex items-center gap-3">
                <div class="flex-1">
                    <input type="text" name="q" id="q" value="{{ request('q') }}" 
                           placeholder="Buscar rol por nombre..."
                           class="input">
                </div>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Buscar
                </button>
                @if(request('q'))
                    <a href="{{ route('roles.index') }}" class="btn-ghost">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        {{-- Lista de roles --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($roles as $role)
                <div class="card p-6 hover:border-sky-500/30 transition-all duration-300">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-200 mb-1">{{ $role->name }}</h3>
                            <p class="text-sm text-slate-400">{{ $role->permissions_count }} permisos</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-sky-500/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('roles.show', $role) }}" 
                           class="flex-1 text-center px-3 py-2 rounded-lg border border-white/10 hover:bg-white/5 text-slate-200 text-sm transition-all">
                            Ver detalles
                        </a>
                        <a href="{{ route('roles.edit', $role) }}" 
                           class="flex-1 text-center px-3 py-2 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-sm transition-all">
                            Editar
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-slate-400">No se encontraron roles.</p>
                </div>
            @endforelse
        </div>

        {{-- Paginación --}}
        @if($roles->hasPages())
            <div class="card p-4">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
