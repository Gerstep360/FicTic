<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                    Resultado de Validación
                </h2>
                <p class="text-sm text-slate-400 mt-1">
                    {{ $gestion->nombre }} 
                    @if($carrera) 
                        - {{ $carrera->nombre_carrera }}
                    @else
                        - Todas las carreras
                    @endif
                </p>
            </div>
            <a href="{{ route('validacion-horarios.index') }}"
               class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition duration-150">
                Nueva Validación
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Resumen general -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <!-- Estado general -->
                <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        @if($resultado['success'])
                            <div class="w-12 h-12 bg-green-900/50 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        @else
                            <div class="w-12 h-12 bg-red-900/50 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <p class="text-slate-400 text-sm">Estado</p>
                            <p class="text-lg font-bold {{ $resultado['success'] ? 'text-green-400' : 'text-red-400' }}">
                                {{ $resultado['success'] ? 'Válido' : 'Con Conflictos' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Total conflictos -->
                <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-red-900/50 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm">Conflictos</p>
                            <p class="text-2xl font-bold text-slate-200">
                                {{ $resultado['resumen']['total_conflictos'] }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Total advertencias -->
                <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-yellow-900/50 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm">Advertencias</p>
                            <p class="text-2xl font-bold text-slate-200">
                                {{ $resultado['resumen']['total_advertencias'] }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Bloqueantes -->
                <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-purple-900/50 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm">Bloqueantes</p>
                            <p class="text-2xl font-bold text-slate-200">
                                {{ $resultado['resumen']['bloqueantes'] }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @if($resultado['resumen']['bloqueantes'] > 0)
                <div class="mb-6 bg-red-900/30 border border-red-700/50 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <p class="text-sm text-red-300 font-medium">Conflictos bloqueantes detectados</p>
                            <p class="text-sm text-red-200/80 mt-1">
                                Existen {{ $resultado['resumen']['bloqueantes'] }} conflicto(s) crítico(s) que deben resolverse antes de aplicar este horario.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Conflictos críticos -->
            @if($resultado['resumen']['criticos'] > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-red-400 mb-3 flex items-center gap-2">
                        <span class="w-8 h-8 bg-red-900/50 rounded-full flex items-center justify-center text-sm">
                            {{ $resultado['resumen']['criticos'] }}
                        </span>
                        Conflictos Críticos
                    </h3>
                    <div class="space-y-3">
                        @foreach($resultado['conflictos'] as $conflicto)
                            @if($conflicto['severidad'] === 'critica')
                                <div class="bg-slate-800 border-l-4 border-red-500 rounded-lg overflow-hidden">
                                    <div class="p-4">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <span class="text-red-400 font-semibold">
                                                        {{ $conflicto['mensaje'] }}
                                                    </span>
                                                    @if($conflicto['bloqueante'])
                                                        <span class="px-2 py-0.5 bg-red-900/50 border border-red-700 text-red-300 text-xs rounded">
                                                            BLOQUEANTE
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-slate-300 text-sm mb-2 whitespace-pre-line">{{ $conflicto['detalles'] }}</p>
                                                @if(!empty($conflicto['sugerencia']))
                                                    <div class="mt-3 flex items-start gap-2 bg-blue-900/20 border border-blue-700/30 rounded px-3 py-2">
                                                        <svg class="w-4 h-4 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <p class="text-blue-300 text-sm">{{ $conflicto['sugerencia'] }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                            <span class="text-xs text-slate-500 whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($conflicto['timestamp'])->format('H:i:s') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Conflictos altos -->
            @if($resultado['resumen']['altos'] > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-orange-400 mb-3 flex items-center gap-2">
                        <span class="w-8 h-8 bg-orange-900/50 rounded-full flex items-center justify-center text-sm">
                            {{ $resultado['resumen']['altos'] }}
                        </span>
                        Conflictos Altos
                    </h3>
                    <div class="space-y-3">
                        @foreach($resultado['conflictos'] as $conflicto)
                            @if($conflicto['severidad'] === 'alta')
                                <div class="bg-slate-800 border-l-4 border-orange-500 rounded-lg overflow-hidden">
                                    <div class="p-4">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <span class="text-orange-400 font-semibold">
                                                        {{ $conflicto['mensaje'] }}
                                                    </span>
                                                    @if($conflicto['bloqueante'])
                                                        <span class="px-2 py-0.5 bg-orange-900/50 border border-orange-700 text-orange-300 text-xs rounded">
                                                            BLOQUEANTE
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-slate-300 text-sm mb-2 whitespace-pre-line">{{ $conflicto['detalles'] }}</p>
                                                @if(!empty($conflicto['sugerencia']))
                                                    <div class="mt-3 flex items-start gap-2 bg-blue-900/20 border border-blue-700/30 rounded px-3 py-2">
                                                        <svg class="w-4 h-4 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <p class="text-blue-300 text-sm">{{ $conflicto['sugerencia'] }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                            <span class="text-xs text-slate-500 whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($conflicto['timestamp'])->format('H:i:s') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Advertencias (media y baja) -->
            @if($resultado['resumen']['total_advertencias'] > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-yellow-400 mb-3 flex items-center gap-2">
                        <span class="w-8 h-8 bg-yellow-900/50 rounded-full flex items-center justify-center text-sm">
                            {{ $resultado['resumen']['total_advertencias'] }}
                        </span>
                        Advertencias
                    </h3>
                    <div class="space-y-3">
                        @foreach($resultado['advertencias'] as $advertencia)
                            @php
                                $borderColor = match($advertencia['severidad']) {
                                    'media' => 'border-yellow-500',
                                    'baja' => 'border-blue-500',
                                    default => 'border-slate-500'
                                };
                                $textColor = match($advertencia['severidad']) {
                                    'media' => 'text-yellow-400',
                                    'baja' => 'text-blue-400',
                                    default => 'text-slate-400'
                                };
                            @endphp
                            <div class="bg-slate-800 border-l-4 {{ $borderColor }} rounded-lg overflow-hidden">
                                <div class="p-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1">
                                            <span class="{{ $textColor }} font-semibold">
                                                {{ $advertencia['mensaje'] }}
                                            </span>
                                            <p class="text-slate-300 text-sm mt-2 whitespace-pre-line">{{ $advertencia['detalles'] }}</p>
                                            @if(!empty($advertencia['sugerencia']))
                                                <div class="mt-3 flex items-start gap-2 bg-slate-900/50 border border-slate-700 rounded px-3 py-2">
                                                    <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <p class="text-slate-300 text-sm">{{ $advertencia['sugerencia'] }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        <span class="text-xs text-slate-500 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($advertencia['timestamp'])->format('H:i:s') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Sin problemas -->
            @if($resultado['success'] && $resultado['resumen']['total_conflictos'] === 0 && $resultado['resumen']['total_advertencias'] === 0)
                <div class="bg-green-900/30 border border-green-700/50 rounded-lg p-8 text-center">
                    <svg class="w-16 h-16 text-green-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="text-2xl font-bold text-green-400 mb-2">
                        ¡Horario Válido!
                    </h3>
                    <p class="text-green-300/80">
                        No se detectaron conflictos ni advertencias. El horario cumple con todas las reglas de validación.
                    </p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
