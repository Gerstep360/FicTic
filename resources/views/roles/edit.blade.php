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
                    Editar Rol: {{ $role->name }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        @if($errors->any())
            <div class="card p-4 bg-red-500/10 border-red-500/20">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="font-semibold text-red-400 mb-1">Error al actualizar el rol</h3>
                        <ul class="list-disc list-inside text-sm text-red-300 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('roles.update', $role) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Información básica --}}
            <div class="card p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-slate-200 mb-4">Información del Rol</h3>

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-300 mb-2">
                        Nombre del Rol <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" 
                           placeholder="Ej: Coordinador de Carrera"
                           required
                           class="input @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Permisos --}}
            <div class="card p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-slate-200">Permisos</h3>
                    <div class="flex items-center gap-2 text-sm">
                        <button type="button" onclick="selectAll()" class="text-sky-400 hover:text-sky-300">Seleccionar todos</button>
                        <span class="text-slate-500">|</span>
                        <button type="button" onclick="deselectAll()" class="text-slate-400 hover:text-slate-200">Deseleccionar todos</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($permissions as $permission)
                        <label class="flex items-start gap-3 p-3 rounded-lg hover:bg-white/5 cursor-pointer transition-colors border border-white/5 hover:border-white/10">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                   {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}
                                   class="checkbox mt-0.5">
                            <div class="flex-1 min-w-0">
                                <span class="text-slate-200 text-sm block break-words">{{ $permission->name }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>

                @if($permissions->isEmpty())
                    <p class="text-slate-400 text-center py-8">No hay permisos disponibles.</p>
                @endif
            </div>

            {{-- Botones --}}
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center gap-3">
                <a href="{{ route('roles.show', $role) }}" class="btn-ghost justify-center">
                    Cancelar
                </a>
                <button type="submit" class="btn-primary flex-1 justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Actualizar Rol
                </button>
            </div>
        </form>
    </div>

    <script>
        function selectAll() {
            document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = true);
        }
        function deselectAll() {
            document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = false);
        }
    </script>
</x-app-layout>
