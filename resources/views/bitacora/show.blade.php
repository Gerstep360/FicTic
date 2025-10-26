<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('bitacora.index') }}" class="text-slate-400 hover:text-slate-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                    Detalle del Evento #{{ $bitacora->id_bitacora }}
                </h2>
            </div>
            
            @if($bitacora->exitoso)
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Exitoso
                </span>
            @else
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Fallido
                </span>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Información principal --}}
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-slate-200 mb-4">Información del Evento</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Fecha y Hora</label>
                    <p class="text-slate-200">{{ $bitacora->created_at->format('d/m/Y H:i:s') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Usuario</label>
                    <p class="text-slate-200">{{ $bitacora->usuario->name ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Acción</label>
                    <p><span class="chip">{{ $bitacora->accion }}</span></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Módulo</label>
                    <p class="text-slate-200">{{ $bitacora->modulo ?? '—' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Tabla Afectada</label>
                    <p class="text-slate-200">{{ $bitacora->tabla_afectada }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Registro ID</label>
                    <p class="text-slate-200">{{ $bitacora->registro_id ?? '—' }}</p>
                </div>

                @if($bitacora->id_gestion)
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Gestión</label>
                    <p class="text-slate-200">{{ $bitacora->gestion->nombre ?? $bitacora->id_gestion }}</p>
                </div>
                @endif
            </div>

            @if($bitacora->descripcion)
            <div class="mt-4 pt-4 border-t border-white/10">
                <label class="block text-sm font-medium text-slate-400 mb-1">Descripción</label>
                <p class="text-slate-200">{{ $bitacora->descripcion }}</p>
            </div>
            @endif
        </div>

        {{-- Información técnica --}}
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-slate-200 mb-4">Información Técnica</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">IP del Cliente</label>
                    <p class="text-slate-200 font-mono text-sm">{{ $bitacora->ip ?? '—' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Método HTTP</label>
                    <p class="text-slate-200">{{ $bitacora->metodo ?? '—' }}</p>
                </div>

                @if($bitacora->url)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-400 mb-1">URL</label>
                    <p class="text-slate-200 font-mono text-sm break-all">{{ $bitacora->url }}</p>
                </div>
                @endif

                @if($bitacora->user_agent)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-400 mb-1">User Agent</label>
                    <p class="text-slate-200 text-sm break-all">{{ $bitacora->user_agent }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Metadata --}}
        @if($bitacora->metadata)
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-slate-200 mb-4">Metadatos</h3>
            <pre class="bg-slate-950 border border-white/10 rounded-lg p-4 text-xs text-slate-300 overflow-x-auto">{{ json_encode($bitacora->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
        @endif

        {{-- Cambios (antes/después) --}}
        @if($bitacora->cambios_antes || $bitacora->cambios_despues)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($bitacora->cambios_antes)
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-slate-200 mb-4">Estado Anterior</h3>
                <pre class="bg-slate-950 border border-white/10 rounded-lg p-4 text-xs text-slate-300 overflow-x-auto">{{ json_encode($bitacora->cambios_antes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif

            @if($bitacora->cambios_despues)
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-slate-200 mb-4">Estado Posterior</h3>
                <pre class="bg-slate-950 border border-white/10 rounded-lg p-4 text-xs text-slate-300 overflow-x-auto">{{ json_encode($bitacora->cambios_despues, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif
        </div>
        @endif
    </div>
</x-app-layout>
