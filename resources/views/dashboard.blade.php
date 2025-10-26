<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        {{-- Bienvenida --}}
        <div class="card p-8">
            <div class="flex items-start gap-6">
                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-slate-200 mb-2">
                        Bienvenido, {{ Auth::user()->name }}
                    </h3>
                    <p class="text-slate-400 mb-3">
                        {{ Auth::user()->email }}
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach(Auth::user()->roles as $role)
                            <span class="chip">{{ $role->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Accesos rápidos --}}
        <div>
            <h3 class="text-lg font-semibold text-slate-200 mb-4">Accesos Rápidos</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @can('importar_usuarios')
                <a href="{{ route('usuarios.import.create') }}" class="card p-6 hover:border-sky-500/30 transition-all duration-300 group">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center group-hover:bg-sky-500/20 transition">
                            <svg class="w-6 h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-slate-200 mb-1 group-hover:text-sky-400 transition">Importar Usuarios</h4>
                            <p class="text-sm text-slate-400">Carga masiva desde CSV</p>
                        </div>
                    </div>
                </a>
                @endcan

                @can('definir_roles_perfiles')
                <a href="{{ route('roles.index') }}" class="card p-6 hover:border-emerald-500/30 transition-all duration-300 group">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center group-hover:bg-emerald-500/20 transition">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-slate-200 mb-1 group-hover:text-emerald-400 transition">Roles y Permisos</h4>
                            <p class="text-sm text-slate-400">Gestionar accesos del sistema</p>
                        </div>
                    </div>
                </a>
                @endcan

                <a href="{{ route('usuarios.index') }}" class="card p-6 hover:border-fuchsia-500/30 transition-all duration-300 group">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-fuchsia-500/10 border border-fuchsia-500/20 flex items-center justify-center group-hover:bg-fuchsia-500/20 transition">
                            <svg class="w-6 h-6 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-slate-200 mb-1 group-hover:text-fuchsia-400 transition">Usuarios</h4>
                            <p class="text-sm text-slate-400">Ver y gestionar usuarios</p>
                        </div>
                    </div>
                </a>

                @can('ver_reportes')
                <a href="{{ route('bitacora.index') }}" class="card p-6 hover:border-amber-500/30 transition-all duration-300 group">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center group-hover:bg-amber-500/20 transition">
                            <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-slate-200 mb-1 group-hover:text-amber-400 transition">Bitácora</h4>
                            <p class="text-sm text-slate-400">Auditoría del sistema</p>
                        </div>
                    </div>
                </a>
                @endcan

                <a href="{{ route('profile.edit') }}" class="card p-6 hover:border-violet-500/30 transition-all duration-300 group">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-violet-500/10 border border-violet-500/20 flex items-center justify-center group-hover:bg-violet-500/20 transition">
                            <svg class="w-6 h-6 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-slate-200 mb-1 group-hover:text-violet-400 transition">Mi Perfil</h4>
                            <p class="text-sm text-slate-400">Actualizar información</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Estadísticas --}}
        <div>
            <h3 class="text-lg font-semibold text-slate-200 mb-4">Estadísticas del Sistema</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="card p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-sky-500/10 flex items-center justify-center">
                            <svg class="w-6 h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-200">{{ \App\Models\User::count() }}</p>
                            <p class="text-sm text-slate-400">Usuarios</p>
                        </div>
                    </div>
                </div>

                <div class="card p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-200">{{ \Spatie\Permission\Models\Role::count() }}</p>
                            <p class="text-sm text-slate-400">Roles</p>
                        </div>
                    </div>
                </div>

                <div class="card p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-fuchsia-500/10 flex items-center justify-center">
                            <svg class="w-6 h-6 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-200">{{ \Spatie\Permission\Models\Permission::count() }}</p>
                            <p class="text-sm text-slate-400">Permisos</p>
                        </div>
                    </div>
                </div>

                @can('ver_reportes')
                <div class="card p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-500/10 flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-200">{{ \App\Models\Bitacora::whereDate('created_at', today())->count() }}</p>
                            <p class="text-sm text-slate-400">Eventos hoy</p>
                        </div>
                    </div>
                </div>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
