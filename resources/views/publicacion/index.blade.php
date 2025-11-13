<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-200 leading-tight">
            Publicación de Horarios
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-green-900/50 border border-green-700 text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-yellow-900/50 border border-yellow-700 text-yellow-200">
                    {{ session('warning') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-red-900/50 border border-red-700 text-red-200">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Información -->
            <div class="bg-gradient-to-r from-blue-900/50 to-blue-800/50 border border-blue-700 rounded-lg p-6 mb-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <svg class="w-12 h-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-blue-200 mb-2">
                            Sistema de Publicación Oficial
                        </h3>
                        <p class="text-blue-300/90 text-sm mb-3">
                            Administre la publicación de horarios aprobados. Una vez publicados, los horarios serán visibles 
                            públicamente para estudiantes, docentes y personal administrativo.
                        </p>
                        <ul class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-blue-300/80">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Vistas por docente, grupo y aula
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Exportación a PDF
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Maestro de oferta académica
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Acceso público sin login
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Lista de gestiones -->
            <div class="space-y-6">
                @forelse($gestiones as $gestion)
                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 overflow-hidden">
                        <!-- Header de la Gestión -->
                        <div class="bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-700 p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="bg-blue-900/30 p-3 rounded-lg">
                                        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-slate-200">
                                            {{ $gestion->nombre }}
                                        </h3>
                                        <p class="text-slate-400 text-sm mt-1">
                                            {{ $gestion->fecha_inicio->format('d/m/Y') }} - {{ $gestion->fecha_fin->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Badge de Estado -->
                                @if($gestion->publicada)
                                    <span class="px-4 py-2 text-sm font-semibold rounded-lg bg-green-900/50 border-2 border-green-600 text-green-300 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Publicada
                                    </span>
                                @elseif($gestion->puede_publicar)
                                    <span class="px-4 py-2 text-sm font-semibold rounded-lg bg-blue-900/50 border-2 border-blue-600 text-blue-300 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Lista para Publicar
                                    </span>
                                @else
                                    <span class="px-4 py-2 text-sm font-semibold rounded-lg bg-yellow-900/50 border-2 border-yellow-600 text-yellow-300 flex items-center gap-2">
                                        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        En Proceso de Aprobación
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="p-6">
                            <!-- Grid de Estadísticas Clave -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                                <!-- Total de Horarios -->
                                <div class="bg-gradient-to-br from-blue-900/30 to-blue-800/20 border border-blue-700/50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-3xl font-bold text-blue-300">
                                            {{ $gestion->aprobaciones->sum('total_horarios') }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-400">Horarios Totales</p>
                                </div>

                                <!-- Aprobaciones -->
                                <div class="bg-gradient-to-br from-green-900/30 to-green-800/20 border border-green-700/50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-3xl font-bold text-green-300">
                                            {{ $gestion->aprobaciones()->where('estado', 'aprobado_final')->count() }} / {{ $gestion->aprobaciones()->count() }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-400">Aprobaciones Finales</p>
                                </div>

                                <!-- Conflictos -->
                                <div class="bg-gradient-to-br from-orange-900/30 to-orange-800/20 border border-orange-700/50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <span class="text-3xl font-bold text-orange-300">
                                            {{ $gestion->aprobaciones->sum('conflictos_pendientes') }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-400">Conflictos Detectados</p>
                                </div>

                                <!-- Estado Global -->
                                <div class="bg-gradient-to-br from-purple-900/30 to-purple-800/20 border border-purple-700/50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <span class="text-2xl font-bold {{ $gestion->puede_publicar ? 'text-green-300' : 'text-yellow-300' }}">
                                            {{ $gestion->puede_publicar ? '✓' : '...' }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-400">Estado de Publicación</p>
                                </div>
                            </div>

                            @if($gestion->publicada && $gestion->nota_publicacion)
                                <!-- Nota de Publicación -->
                                <div class="bg-blue-900/20 border border-blue-700/50 rounded-lg p-4 mb-6">
                                    <div class="flex gap-3">
                                        <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-blue-300 mb-1">Nota de Publicación:</p>
                                            <p class="text-sm text-blue-200/80">{{ $gestion->nota_publicacion }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($gestion->publicada)
                                <!-- Información de Publicación -->
                                <div class="bg-green-900/20 border border-green-700/50 rounded-lg p-4 mb-6">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs text-slate-400 mb-1">Publicado por</p>
                                            <p class="text-sm font-semibold text-green-300">
                                                {{ $gestion->usuarioPublicador->name ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-400 mb-1">Fecha de publicación</p>
                                            <p class="text-sm font-semibold text-green-300">
                                                {{ $gestion->fecha_publicacion?->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- SECCIÓN EXPANDIDA: Detalles de Aprobaciones -->
                            @if($gestion->aprobaciones->isNotEmpty())
                                <div class="border border-slate-700 rounded-lg overflow-hidden mb-6">
                                    <button onclick="toggleDetails({{ $gestion->id_gestion }})" 
                                            class="w-full bg-slate-900/50 hover:bg-slate-900/70 transition p-4 flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <div class="text-left">
                                                <p class="text-sm font-semibold text-slate-200">
                                                    Detalles de Aprobaciones ({{ $gestion->aprobaciones->count() }})
                                                </p>
                                                <p class="text-xs text-slate-400">Click para expandir y ver información completa</p>
                                            </div>
                                        </div>
                                        <svg id="arrow-{{ $gestion->id_gestion }}" class="w-5 h-5 text-slate-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                    
                                    <div id="details-{{ $gestion->id_gestion }}" class="hidden bg-slate-900/30 p-6 space-y-4">
                                        @foreach($gestion->aprobaciones as $aprobacion)
                                            <div class="bg-slate-800 border border-slate-700 rounded-lg p-5">
                                                <!-- Header de Aprobación -->
                                                <div class="flex items-start justify-between mb-4">
                                                    <div class="flex-1">
                                                        <h4 class="text-lg font-semibold text-slate-200 mb-1">
                                                            {{ $aprobacion->carrera?->nombre_carrera ?? '🏛️ Toda la Facultad' }}
                                                        </h4>
                                                        @if($aprobacion->carrera)
                                                            <p class="text-sm text-slate-400">
                                                                {{ $aprobacion->carrera->facultad->nombre_facultad }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <span class="px-3 py-1.5 rounded-lg text-sm font-semibold {{ $aprobacion->color_estado }} border">
                                                        {{ $aprobacion->icono_estado }} {{ $aprobacion->estado_texto }}
                                                    </span>
                                                </div>

                                                <!-- Grid de Detalles -->
                                                <div class="grid grid-cols-3 gap-4 mb-4">
                                                    <div class="bg-slate-900/50 rounded p-3">
                                                        <p class="text-xs text-slate-400 mb-1">Total Horarios</p>
                                                        <p class="text-xl font-bold text-blue-300">{{ $aprobacion->total_horarios }}</p>
                                                    </div>
                                                    <div class="bg-slate-900/50 rounded p-3">
                                                        <p class="text-xs text-slate-400 mb-1">Validados</p>
                                                        <p class="text-xl font-bold text-green-300">{{ $aprobacion->horarios_validados }}</p>
                                                    </div>
                                                    <div class="bg-slate-900/50 rounded p-3">
                                                        <p class="text-xs text-slate-400 mb-1">Conflictos</p>
                                                        <p class="text-xl font-bold {{ $aprobacion->conflictos_pendientes > 0 ? 'text-orange-300' : 'text-green-300' }}">
                                                            {{ $aprobacion->conflictos_pendientes }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Flujo de Aprobación -->
                                                <div class="border-t border-slate-700 pt-4">
                                                    <p class="text-xs font-semibold text-slate-400 mb-3">FLUJO DE APROBACIÓN</p>
                                                    <div class="space-y-3">
                                                        <!-- Coordinador -->
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-8 h-8 rounded-full bg-blue-900/50 border border-blue-700 flex items-center justify-center">
                                                                <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                                                </svg>
                                                            </div>
                                                            <div class="flex-1">
                                                                <p class="text-sm font-medium text-slate-200">
                                                                    Coordinador: {{ $aprobacion->coordinador?->name ?? 'Sin asignar' }}
                                                                </p>
                                                                <p class="text-xs text-slate-400">Creado el {{ $aprobacion->created_at->format('d/m/Y H:i') }}</p>
                                                            </div>
                                                            <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                            </svg>
                                                        </div>

                                                        <!-- Director -->
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-8 h-8 rounded-full {{ $aprobacion->id_director ? 'bg-green-900/50 border-green-700' : 'bg-slate-700 border-slate-600' }} border flex items-center justify-center">
                                                                <svg class="w-4 h-4 {{ $aprobacion->id_director ? 'text-green-400' : 'text-slate-500' }}" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                                                </svg>
                                                            </div>
                                                            <div class="flex-1">
                                                                <p class="text-sm font-medium {{ $aprobacion->id_director ? 'text-slate-200' : 'text-slate-400' }}">
                                                                    Director: {{ $aprobacion->director?->name ?? 'Pendiente' }}
                                                                </p>
                                                                @if($aprobacion->fecha_respuesta_director)
                                                                    <p class="text-xs text-slate-400">Aprobado el {{ $aprobacion->fecha_respuesta_director->format('d/m/Y H:i') }}</p>
                                                                @elseif($aprobacion->fecha_envio_director)
                                                                    <p class="text-xs text-yellow-400">Enviado el {{ $aprobacion->fecha_envio_director->format('d/m/Y H:i') }}</p>
                                                                @else
                                                                    <p class="text-xs text-slate-500">No enviado</p>
                                                                @endif
                                                            </div>
                                                            @if($aprobacion->id_director)
                                                                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                                </svg>
                                                            @else
                                                                <svg class="w-5 h-5 text-slate-600" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd"/>
                                                                </svg>
                                                            @endif
                                                        </div>

                                                        <!-- Decano (Opcional) -->
                                                        @if($aprobacion->id_decano || $aprobacion->fecha_envio_decano)
                                                            <div class="flex items-center gap-3">
                                                                <div class="w-8 h-8 rounded-full {{ $aprobacion->id_decano ? 'bg-green-900/50 border-green-700' : 'bg-slate-700 border-slate-600' }} border flex items-center justify-center">
                                                                    <svg class="w-4 h-4 {{ $aprobacion->id_decano ? 'text-green-400' : 'text-slate-500' }}" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                                                    </svg>
                                                                </div>
                                                                <div class="flex-1">
                                                                    <p class="text-sm font-medium {{ $aprobacion->id_decano ? 'text-slate-200' : 'text-slate-400' }}">
                                                                        Decano: {{ $aprobacion->decano?->name ?? 'Pendiente' }}
                                                                    </p>
                                                                    @if($aprobacion->fecha_respuesta_decano)
                                                                        <p class="text-xs text-slate-400">Aprobado el {{ $aprobacion->fecha_respuesta_decano->format('d/m/Y H:i') }}</p>
                                                                    @elseif($aprobacion->fecha_envio_decano)
                                                                        <p class="text-xs text-yellow-400">Enviado el {{ $aprobacion->fecha_envio_decano->format('d/m/Y H:i') }}</p>
                                                                    @endif
                                                                </div>
                                                                @if($aprobacion->id_decano)
                                                                    <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                @else
                                                                    <svg class="w-5 h-5 text-slate-600" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Observaciones -->
                                                @if($aprobacion->observaciones_coordinador || $aprobacion->observaciones_director || $aprobacion->observaciones_decano)
                                                    <div class="border-t border-slate-700 mt-4 pt-4">
                                                        <p class="text-xs font-semibold text-slate-400 mb-3">OBSERVACIONES</p>
                                                        <div class="space-y-2">
                                                            @if($aprobacion->observaciones_coordinador)
                                                                <div class="bg-blue-900/20 border border-blue-700/50 rounded p-3">
                                                                    <p class="text-xs text-blue-400 font-medium mb-1">Coordinador:</p>
                                                                    <p class="text-sm text-blue-200/80">{{ $aprobacion->observaciones_coordinador }}</p>
                                                                </div>
                                                            @endif
                                                            @if($aprobacion->observaciones_director)
                                                                <div class="bg-purple-900/20 border border-purple-700/50 rounded p-3">
                                                                    <p class="text-xs text-purple-400 font-medium mb-1">Director:</p>
                                                                    <p class="text-sm text-purple-200/80">{{ $aprobacion->observaciones_director }}</p>
                                                                </div>
                                                            @endif
                                                            @if($aprobacion->observaciones_decano)
                                                                <div class="bg-green-900/20 border border-green-700/50 rounded p-3">
                                                                    <p class="text-xs text-green-400 font-medium mb-1">Decano:</p>
                                                                    <p class="text-sm text-green-200/80">{{ $aprobacion->observaciones_decano }}</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Botones de Acción -->
                            <div class="flex flex-wrap items-center justify-end gap-3 pt-4 border-t border-slate-700">
                                @if($gestion->publicada)
                                    <a href="{{ route('publicacion.preview', $gestion->id_gestion) }}"
                                       class="px-6 py-3 bg-slate-600 hover:bg-slate-700 text-white rounded-lg transition flex items-center gap-2 font-medium">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Vista Detallada
                                    </a>
                                    <a href="{{ route('publicacion.maestro', $gestion->id_gestion) }}"
                                       target="_blank"
                                       class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center gap-2 font-medium">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Ver Publicación
                                    </a>
                                    <a href="{{ route('publicacion.pdf-maestro', $gestion->id_gestion) }}"
                                       class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition flex items-center gap-2 font-medium">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Descargar PDF
                                    </a>
                                    <form action="{{ route('publicacion.despublicar', $gestion->id_gestion) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('⚠️ ¿Despublicar esta gestión?\n\nLos horarios dejarán de ser visibles públicamente.')"
                                                class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition flex items-center gap-2 font-medium">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                            </svg>
                                            Despublicar
                                        </button>
                                    </form>
                                @elseif($gestion->puede_publicar)
                                    <a href="{{ route('publicacion.preview', $gestion->id_gestion) }}"
                                       class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center gap-2 font-medium">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Vista Previa
                                    </a>
                                    <button onclick="openPreviewModal({{ $gestion->id_gestion }}, '{{ $gestion->nombre }}')"
                                            class="px-8 py-3 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white rounded-lg transition flex items-center gap-2 font-bold text-lg shadow-lg">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        Publicar Horarios
                                    </button>
                                @else
                                    <a href="{{ route('publicacion.preview', $gestion->id_gestion) }}"
                                       class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center gap-2 font-medium">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Ver Horarios
                                    </a>
                                    <button disabled
                                            class="px-8 py-3 bg-slate-700 text-slate-500 rounded-lg cursor-not-allowed flex items-center gap-2 font-medium">
                                        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        Publicar (Pendiente)
                                    </button>
                                    
                                    {{-- Botón especial para Admin DTIC para forzar publicación --}}
                                    @role('Admin DTIC')
                                        <button onclick="openForcePublishModal({{ $gestion->id_gestion }}, '{{ $gestion->nombre }}')"
                                                class="px-6 py-3 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white rounded-lg transition flex items-center gap-2 font-bold border-2 border-orange-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            Forzar Publicación
                                        </button>
                                    @endrole
                                    
                                    <p class="text-sm text-slate-400">
                                        Se requiere que todas las aprobaciones estén en estado "Aprobado Final"
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-12 text-center">
                        <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-slate-400 text-lg">No hay gestiones registradas</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $gestiones->links() }}
            </div>

        </div>
    </div>

    <!-- Modal Publicar -->
    <div id="publicarModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 items-center justify-center p-4" style="display: none;">
        <div class="bg-slate-800 rounded-lg shadow-2xl border border-slate-700 max-w-md w-full">
            <div class="px-6 py-4 bg-gradient-to-r from-green-900 to-green-800 border-b border-green-700">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Confirmar Publicación
                </h3>
            </div>
            
            <form id="publicarForm" method="POST" class="p-6">
                @csrf
                
                <div class="mb-6">
                    <div class="bg-green-900/20 border border-green-700/50 rounded-lg p-4 mb-4">
                        <div class="flex gap-3">
                            <svg class="w-6 h-6 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-green-300 font-semibold mb-2">
                                    ¿Publicar la gestión <span id="gestionNombre" class="font-bold"></span>?
                                </p>
                                <p class="text-sm text-green-200/80">
                                    Los horarios aprobados serán visibles públicamente para toda la comunidad universitaria.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-900/20 border border-blue-700/50 rounded-lg p-4">
                        <p class="text-sm text-blue-300 mb-2">
                            <strong>Acceso público incluye:</strong>
                        </p>
                        <ul class="text-sm text-blue-200/80 space-y-1">
                            <li>✓ Vistas por docente, grupo y aula</li>
                            <li>✓ Exportación a PDF</li>
                            <li>✓ Maestro de oferta académica</li>
                            <li>✓ Sin necesidad de login</li>
                        </ul>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Nota de publicación (opcional)
                    </label>
                    <textarea name="nota" 
                              rows="3"
                              placeholder="Ej: Horarios oficiales del semestre II-2025"
                              class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200 placeholder-slate-500"></textarea>
                    <p class="text-xs text-slate-400 mt-1">Esta nota será visible junto a los horarios publicados</p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-700">
                    <button type="button" 
                            onclick="closeModal('publicarModal')"
                            class="px-6 py-2.5 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg transition font-medium">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white rounded-lg transition font-bold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Confirmar y Publicar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Forzar Publicación (Solo Admin DTIC) -->
    <div id="forcePublishModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 items-center justify-center p-4" style="display: none;">
        <div class="bg-slate-800 rounded-lg shadow-2xl border border-orange-700 max-w-md w-full">
            <div class="px-6 py-4 bg-gradient-to-r from-orange-900 to-red-900 border-b border-orange-700">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    ⚠️ Forzar Publicación
                </h3>
            </div>
            
            <form id="forcePublishForm" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="forzar" value="1">
                
                <div class="mb-6">
                    <div class="bg-red-900/20 border border-red-700/50 rounded-lg p-4 mb-4">
                        <div class="flex gap-3">
                            <svg class="w-6 h-6 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-red-300 font-bold mb-2">
                                    ADVERTENCIA: Publicación Forzada
                                </p>
                                <p class="text-sm text-red-200/80 mb-2">
                                    Estás a punto de publicar la gestión <span id="forceGestionNombre" class="font-bold"></span> 
                                    <strong>SIN TODAS LAS APROBACIONES COMPLETAS</strong>.
                                </p>
                                <p class="text-xs text-red-200/70">
                                    Esta acción solo debe usarse en casos excepcionales y quedará registrada en la bitácora.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-orange-900/20 border border-orange-700/50 rounded-lg p-4">
                        <p class="text-sm text-orange-300 mb-2">
                            <strong>Motivos válidos para forzar:</strong>
                        </p>
                        <ul class="text-xs text-orange-200/80 space-y-1">
                            <li>• Urgencia académica por inicio de clases</li>
                            <li>• Aprobación verbal de autoridades</li>
                            <li>• Situaciones de contingencia</li>
                        </ul>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        <span class="text-red-400">*</span> Justificación requerida
                    </label>
                    <textarea name="nota" 
                              rows="3"
                              required
                              placeholder="Explique el motivo de forzar la publicación sin aprobaciones completas..."
                              class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200 placeholder-slate-500"></textarea>
                    <p class="text-xs text-slate-400 mt-1">Esta justificación será registrada permanentemente</p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-700">
                    <button type="button" 
                            onclick="closeModal('forcePublishModal')"
                            class="px-6 py-2.5 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg transition font-medium">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white rounded-lg transition font-bold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Forzar Publicación
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleDetails(gestionId) {
            const details = document.getElementById(`details-${gestionId}`);
            const arrow = document.getElementById(`arrow-${gestionId}`);
            
            if (details.classList.contains('hidden')) {
                details.classList.remove('hidden');
                arrow.classList.add('rotate-180');
            } else {
                details.classList.add('hidden');
                arrow.classList.remove('rotate-180');
            }
        }

        function openPreviewModal(idGestion, nombre) {
            document.getElementById('publicarForm').action = `/admin/publicacion/${idGestion}/publicar`;
            document.getElementById('gestionNombre').textContent = nombre;
            document.getElementById('publicarModal').style.display = 'flex';
        }

        function openForcePublishModal(idGestion, nombre) {
            document.getElementById('forcePublishForm').action = `/admin/publicacion/${idGestion}/publicar`;
            document.getElementById('forceGestionNombre').textContent = nombre;
            document.getElementById('forcePublishModal').style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal('publicarModal');
                closeModal('forcePublishModal');
            }
        });

        // Click fuera del modal para cerrar
        document.getElementById('publicarModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal('publicarModal');
            }
        });
    </script>
</x-app-layout>
