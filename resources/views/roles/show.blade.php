<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('roles.index') }}" class="text-slate-400 hover:text-slate-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                    Detalle del Rol
                </h2>
            </div>
            
            @if(session('status'))
                <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-2 rounded-xl text-sm">
                    {{ session('status') }}
                </div>
            @endif
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Información del rol --}}
        <div class="card p-4 sm:p-6">
            <div class="flex items-start justify-between gap-4 mb-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-slate-200 mb-1">{{ $role->name }}</h3>
                        <p class="text-slate-400 text-sm">{{ $role->permissions->count() }} permisos asignados</p>
                    </div>
                </div>

                <a href="{{ route('roles.edit', $role) }}" class="btn-ghost flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-6 border-t border-white/10">
                <div>
                    <p class="text-sm text-slate-400 mb-1">Guard</p>
                    <p class="text-slate-200 font-mono">{{ $role->guard_name }}</p>
                </div>

                <div>
                    <p class="text-sm text-slate-400 mb-1">ID</p>
                    <p class="text-slate-200 font-mono">#{{ $role->id }}</p>
                </div>

                <div>
                    <p class="text-sm text-slate-400 mb-1">Fecha de Creación</p>
                    <p class="text-slate-200">{{ $role->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <div>
                    <p class="text-sm text-slate-400 mb-1">Última Actualización</p>
                    <p class="text-slate-200">{{ $role->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        {{-- Permisos asignados --}}
        <div class="card p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-slate-200 mb-4">Permisos Asignados</h3>

            @if($role->permissions->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($role->permissions->sortBy('name') as $permission)
                        <div class="flex items-center gap-2 p-3 rounded-lg bg-slate-900/50 border border-white/10">
                            <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-slate-300 text-sm break-words">{{ $permission->name }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto mb-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-slate-300 mb-2">Sin permisos asignados</h3>
                    <p class="text-slate-500 mb-4">Este rol no tiene ningún permiso configurado.</p>
                    <a href="{{ route('roles.edit', $role) }}" class="btn-primary inline-flex">
                        Asignar permisos
                    </a>
                </div>
            @endif
        </div>

        {{-- Usuarios con este rol --}}
        <div class="card p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-slate-200 mb-4">Usuarios con este rol</h3>

            @php
                $users = \App\Models\User::role($role->name)->take(10)->get();
            @endphp

            @if($users->count() > 0)
                <div class="space-y-2">
                    @foreach($users as $user)
                        <a href="{{ route('usuarios.show', $user) }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/5 transition-colors">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-slate-200 truncate">{{ $user->name }}</p>
                                <p class="text-xs text-slate-400 truncate">{{ $user->email }}</p>
                            </div>
                            <svg class="w-4 h-4 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endforeach
                </div>

                @php
                    $totalUsers = \App\Models\User::role($role->name)->count();
                @endphp
                
                @if($totalUsers > 10)
                    <p class="text-sm text-slate-400 mt-4 text-center">
                        Mostrando 10 de {{ $totalUsers }} usuarios con este rol
                    </p>
                @endif
            @else
                <div class="text-center py-8">
                    <p class="text-slate-400">No hay usuarios con este rol asignado.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
