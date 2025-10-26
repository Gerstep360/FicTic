<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('usuarios.index') }}" class="text-slate-400 hover:text-slate-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                    Detalle del Usuario
                </h2>
            </div>
            
            @if(session('status'))
                <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-2 rounded-xl text-sm">
                    {{ session('status') }}
                </div>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Información del usuario --}}
        <div class="card p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row items-start gap-4 sm:gap-6">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center text-white text-xl sm:text-2xl font-bold shadow-lg flex-shrink-0">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>

                <div class="flex-1 min-w-0">
                    <h3 class="text-xl sm:text-2xl font-bold text-slate-200 mb-1 truncate">{{ $user->name }}</h3>
                    <p class="text-slate-400 mb-3 break-all">{{ $user->email }}</p>

                    <div class="flex flex-wrap gap-2">
                        @forelse($user->roles as $role)
                            <span class="chip">{{ $role->name }}</span>
                        @empty
                            <span class="text-slate-500 text-sm">Sin roles asignados</span>
                        @endforelse
                    </div>
                </div>

                <div class="text-left sm:text-right w-full sm:w-auto">
                    <p class="text-sm text-slate-400 mb-1">ID</p>
                    <p class="text-xl font-bold text-slate-200 font-mono">#{{ $user->id }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Roles asignados --}}
            <div class="card p-4 sm:p-6" x-data="{ editMode: false }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base sm:text-lg font-semibold text-slate-200">Roles Asignados</h3>
                    @if(auth()->user()->can('asignar_perfiles_ambitos') || auth()->user()->hasRole('Admin DTIC'))
                        <button @click="editMode = !editMode" class="text-sky-400 hover:text-sky-300 text-sm transition">
                            <span x-show="!editMode">Editar roles</span>
                            <span x-show="editMode">Ver roles</span>
                        </button>
                    @endif
                </div>

                <div x-show="!editMode">
                    @forelse($user->roles as $role)
                        <div class="mb-3 p-3 rounded-lg bg-slate-900/50 border border-white/10 transition hover:border-white/20">
                            <div class="flex items-center justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-slate-200 truncate">{{ $role->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $role->permissions->count() }} permisos</p>
                                </div>
                                <svg class="w-5 h-5 text-sky-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 text-sm">Este usuario no tiene roles asignados.</p>
                    @endforelse
                </div>

                @if(auth()->user()->can('asignar_perfiles_ambitos') || auth()->user()->hasRole('Admin DTIC'))
                <div x-show="editMode" style="display: none;">
                    <form method="POST" action="{{ route('usuarios.updateRoles', $user) }}" class="space-y-3">
                        @csrf
                        @foreach(\Spatie\Permission\Models\Role::where('guard_name', 'web')->orderBy('name')->get() as $role)
                            <label class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/5 cursor-pointer transition-colors">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}" 
                                       {{ $user->hasRole($role->name) ? 'checked' : '' }}
                                       class="checkbox">
                                <div class="flex-1 min-w-0">
                                    <span class="text-slate-200 block truncate">{{ $role->name }}</span>
                                    <span class="text-xs text-slate-400">{{ $role->permissions->count() }} permisos</span>
                                </div>
                            </label>
                        @endforeach

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 pt-3 border-t border-white/10">
                            <button type="submit" class="btn-primary flex-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Guardar cambios
                            </button>
                            <button type="button" @click="editMode = false" class="btn-ghost flex-1">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            </div>

            {{-- Permisos (a través de roles) --}}
            <div class="card p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-slate-200 mb-4">Permisos</h3>
                
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @php
                        $allPermissions = $user->roles->flatMap->permissions->unique('id')->sortBy('name');
                    @endphp

                    @forelse($allPermissions as $permission)
                        <div class="flex items-center gap-2 p-2 rounded-lg hover:bg-white/5 transition">
                            <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-slate-300 text-sm break-all">{{ $permission->name }}</span>
                        </div>
                    @empty
                        <p class="text-slate-400 text-sm">No hay permisos asignados a través de roles.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Información adicional --}}
        <div class="card p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-semibold text-slate-200 mb-4">Información Adicional</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-slate-400 mb-1">Fecha de Registro</p>
                    <p class="text-slate-200">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <div>
                    <p class="text-sm text-slate-400 mb-1">Última Actualización</p>
                    <p class="text-slate-200">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                </div>

                @if($user->email_verified_at)
                <div>
                    <p class="text-sm text-slate-400 mb-1">Email Verificado</p>
                    <p class="text-slate-200">{{ $user->email_verified_at->format('d/m/Y H:i') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
