<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                {{ __('Gestión de Usuarios') }}
            </h2>
            @can('importar_usuarios')
            <a href="{{ route('usuarios.import.create') }}" class="btn-primary w-full sm:w-auto justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Importar Usuarios
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Estadísticas --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <div class="card p-4 sm:p-6">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-sky-500/10 flex items-center justify-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl sm:text-2xl font-bold text-slate-200">{{ \App\Models\User::count() }}</p>
                        <p class="text-xs sm:text-sm text-slate-400">Total de usuarios</p>
                    </div>
                </div>
            </div>

            <div class="card p-4 sm:p-6">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl sm:text-2xl font-bold text-slate-200">{{ \App\Models\User::has('roles')->count() }}</p>
                        <p class="text-xs sm:text-sm text-slate-400">Con roles asignados</p>
                    </div>
                </div>
            </div>

            <div class="card p-4 sm:p-6 sm:col-span-2 lg:col-span-1">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-fuchsia-500/10 flex items-center justify-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl sm:text-2xl font-bold text-slate-200">{{ \App\Models\User::where('created_at', '>=', now()->subDays(7))->count() }}</p>
                        <p class="text-xs sm:text-sm text-slate-400">Nuevos (7 días)</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="card p-4 sm:p-6">
            <form method="GET" action="{{ route('usuarios.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label for="q" class="block text-sm font-medium text-slate-300 mb-2">Buscar</label>
                        <input type="text" name="q" id="q" value="{{ request('q') }}" 
                               placeholder="Nombre o correo..."
                               class="input">
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-slate-300 mb-2">Rol</label>
                        <select name="role" id="role" class="input">
                            <option value="">Todos los roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 pt-4 border-t border-white/10">
                    <button type="submit" class="btn-primary justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Buscar
                    </button>
                    <a href="{{ route('usuarios.index') }}" class="btn-ghost justify-center">
                        Limpiar filtros
                    </a>
                </div>
            </form>
        </div>

        {{-- Lista de usuarios --}}
        <div class="card overflow-hidden">
            <!-- Desktop table view -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-900/50 border-b border-white/10">
                        <tr>
                            <th class="px-4 py-3 text-left text-slate-300 font-semibold">ID</th>
                            <th class="px-4 py-3 text-left text-slate-300 font-semibold">Nombre</th>
                            <th class="px-4 py-3 text-left text-slate-300 font-semibold">Correo</th>
                            <th class="px-4 py-3 text-left text-slate-300 font-semibold">Roles</th>
                            <th class="px-4 py-3 text-left text-slate-300 font-semibold">Fecha Registro</th>
                            <th class="px-4 py-3 text-right text-slate-300 font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($users as $user)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-4 py-3 text-slate-400 font-mono">
                                    #{{ $user->id }}
                                </td>
                                <td class="px-4 py-3 text-slate-200 font-medium">
                                    {{ $user->name }}
                                </td>
                                <td class="px-4 py-3 text-slate-400">
                                    {{ $user->email }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($user->roles as $role)
                                            <span class="chip text-xs">{{ $role->name }}</span>
                                        @empty
                                            <span class="text-slate-500 text-xs">Sin roles</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-400 text-xs">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('usuarios.show', $user) }}" 
                                       class="text-sky-400 hover:text-sky-300 text-sm">
                                        Ver detalles →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                    No se encontraron usuarios.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile card view -->
            <div class="lg:hidden divide-y divide-white/5">
                @forelse($users as $user)
                    <div class="p-4 hover:bg-white/5 transition-colors">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-slate-200 truncate">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-400 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-slate-500 font-mono flex-shrink-0">#{{ $user->id }}</span>
                        </div>

                        <div class="flex flex-wrap gap-2 mb-3">
                            @forelse($user->roles as $role)
                                <span class="chip text-xs">{{ $role->name }}</span>
                            @empty
                                <span class="text-slate-500 text-xs">Sin roles</span>
                            @endforelse
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400">{{ $user->created_at->format('d/m/Y') }}</span>
                            <a href="{{ route('usuarios.show', $user) }}" 
                               class="text-sky-400 hover:text-sky-300 font-medium">
                                Ver detalles →
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400">
                        No se encontraron usuarios.
                    </div>
                @endforelse
            </div>

            {{-- Paginación --}}
            @if($users->hasPages())
                <div class="px-4 py-3 border-t border-white/10">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
