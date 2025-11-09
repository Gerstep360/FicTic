<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                Estadísticas de QR Docentes
            </h2>
            <a href="{{ route('qr-docente.index') }}" 
               class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg transition text-sm">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Filtro de Gestión -->
            <div class="mb-6 bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-4">
                <form method="GET" class="flex items-center gap-4">
                    <label class="text-sm font-medium text-slate-300">Filtrar por Gestión:</label>
                    <select name="id_gestion" 
                            onchange="this.form.submit()"
                            class="rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                        <option value="">Todas las gestiones</option>
                        @foreach($gestiones as $gest)
                            <option value="{{ $gest->id_gestion }}" {{ $gestionActual && $gestionActual->id_gestion == $gest->id_gestion ? 'selected' : '' }}>
                                {{ $gest->nombre }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- Cards de Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                
                <!-- Total QR -->
                <div class="bg-gradient-to-br from-purple-900/50 to-purple-800/50 border border-purple-700 rounded-lg shadow-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-300/80 text-sm font-medium">Total de QR</p>
                            <p class="text-4xl font-bold text-white mt-2">{{ $stats['total'] }}</p>
                        </div>
                        <div class="bg-purple-500/20 p-4 rounded-full">
                            <svg class="w-10 h-10 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- QR Activos -->
                <div class="bg-gradient-to-br from-green-900/50 to-green-800/50 border border-green-700 rounded-lg shadow-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-300/80 text-sm font-medium">Códigos Activos</p>
                            <p class="text-4xl font-bold text-white mt-2">{{ $stats['activos'] }}</p>
                            <p class="text-xs text-green-300/70 mt-1">
                                {{ $stats['total'] > 0 ? round(($stats['activos'] / $stats['total']) * 100, 1) : 0 }}% del total
                            </p>
                        </div>
                        <div class="bg-green-500/20 p-4 rounded-full">
                            <svg class="w-10 h-10 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- QR Inactivos -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-700 border border-slate-600 rounded-lg shadow-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-300 text-sm font-medium">Códigos Inactivos</p>
                            <p class="text-4xl font-bold text-white mt-2">{{ $stats['inactivos'] }}</p>
                            <p class="text-xs text-slate-400 mt-1">
                                {{ $stats['total'] > 0 ? round(($stats['inactivos'] / $stats['total']) * 100, 1) : 0 }}% del total
                            </p>
                        </div>
                        <div class="bg-slate-600/30 p-4 rounded-full">
                            <svg class="w-10 h-10 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- QR Usados -->
                <div class="bg-gradient-to-br from-blue-900/50 to-blue-800/50 border border-blue-700 rounded-lg shadow-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-300/80 text-sm font-medium">QR Usados</p>
                            <p class="text-4xl font-bold text-white mt-2">{{ $stats['usados'] }}</p>
                            <p class="text-xs text-blue-300/70 mt-1">
                                Al menos un escaneo
                            </p>
                        </div>
                        <div class="bg-blue-500/20 p-4 rounded-full">
                            <svg class="w-10 h-10 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- QR Nunca Usados -->
                <div class="bg-gradient-to-br from-orange-900/50 to-orange-800/50 border border-orange-700 rounded-lg shadow-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-300/80 text-sm font-medium">Nunca Usados</p>
                            <p class="text-4xl font-bold text-white mt-2">{{ $stats['nunca_usados'] }}</p>
                            <p class="text-xs text-orange-300/70 mt-1">
                                {{ $stats['total'] > 0 ? round(($stats['nunca_usados'] / $stats['total']) * 100, 1) : 0 }}% del total
                            </p>
                        </div>
                        <div class="bg-orange-500/20 p-4 rounded-full">
                            <svg class="w-10 h-10 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Escaneos -->
                <div class="bg-gradient-to-br from-indigo-900/50 to-indigo-800/50 border border-indigo-700 rounded-lg shadow-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-indigo-300/80 text-sm font-medium">Total Escaneos</p>
                            <p class="text-4xl font-bold text-white mt-2">{{ $stats['total_escaneos'] }}</p>
                            <p class="text-xs text-indigo-300/70 mt-1">
                                Promedio: {{ $stats['usados'] > 0 ? round($stats['total_escaneos'] / $stats['usados'], 1) : 0 }} por QR
                            </p>
                        </div>
                        <div class="bg-indigo-500/20 p-4 rounded-full">
                            <svg class="w-10 h-10 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Información visual adicional -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Gráfico de Estado -->
                <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6">
                    <h3 class="text-lg font-semibold text-slate-200 mb-4">Estado de Códigos QR</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between text-sm mb-2">
                                <span class="text-slate-300">Activos</span>
                                <span class="text-green-400 font-semibold">{{ $stats['activos'] }} ({{ $stats['total'] > 0 ? round(($stats['activos'] / $stats['total']) * 100) : 0 }}%)</span>
                            </div>
                            <div class="w-full bg-slate-700 rounded-full h-3">
                                <div class="bg-gradient-to-r from-green-500 to-green-400 h-3 rounded-full transition-all duration-500" 
                                     style="width: {{ $stats['total'] > 0 ? ($stats['activos'] / $stats['total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between text-sm mb-2">
                                <span class="text-slate-300">Inactivos</span>
                                <span class="text-slate-400 font-semibold">{{ $stats['inactivos'] }} ({{ $stats['total'] > 0 ? round(($stats['inactivos'] / $stats['total']) * 100) : 0 }}%)</span>
                            </div>
                            <div class="w-full bg-slate-700 rounded-full h-3">
                                <div class="bg-gradient-to-r from-slate-500 to-slate-400 h-3 rounded-full transition-all duration-500" 
                                     style="width: {{ $stats['total'] > 0 ? ($stats['inactivos'] / $stats['total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Uso -->
                <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6">
                    <h3 class="text-lg font-semibold text-slate-200 mb-4">Uso de Códigos QR</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between text-sm mb-2">
                                <span class="text-slate-300">Con uso registrado</span>
                                <span class="text-blue-400 font-semibold">{{ $stats['usados'] }} ({{ $stats['total'] > 0 ? round(($stats['usados'] / $stats['total']) * 100) : 0 }}%)</span>
                            </div>
                            <div class="w-full bg-slate-700 rounded-full h-3">
                                <div class="bg-gradient-to-r from-blue-500 to-blue-400 h-3 rounded-full transition-all duration-500" 
                                     style="width: {{ $stats['total'] > 0 ? ($stats['usados'] / $stats['total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between text-sm mb-2">
                                <span class="text-slate-300">Sin uso</span>
                                <span class="text-orange-400 font-semibold">{{ $stats['nunca_usados'] }} ({{ $stats['total'] > 0 ? round(($stats['nunca_usados'] / $stats['total']) * 100) : 0 }}%)</span>
                            </div>
                            <div class="w-full bg-slate-700 rounded-full h-3">
                                <div class="bg-gradient-to-r from-orange-500 to-orange-400 h-3 rounded-full transition-all duration-500" 
                                     style="width: {{ $stats['total'] > 0 ? ($stats['nunca_usados'] / $stats['total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Resumen -->
            <div class="mt-6 bg-gradient-to-r from-slate-800 to-slate-700 border border-slate-600 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-slate-200 mb-3">Resumen</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="bg-slate-900/50 rounded-lg p-4">
                        <p class="text-slate-400 mb-1">Tasa de Activación</p>
                        <p class="text-2xl font-bold text-green-400">
                            {{ $stats['total'] > 0 ? round(($stats['activos'] / $stats['total']) * 100) : 0 }}%
                        </p>
                    </div>
                    <div class="bg-slate-900/50 rounded-lg p-4">
                        <p class="text-slate-400 mb-1">Tasa de Uso</p>
                        <p class="text-2xl font-bold text-blue-400">
                            {{ $stats['total'] > 0 ? round(($stats['usados'] / $stats['total']) * 100) : 0 }}%
                        </p>
                    </div>
                    <div class="bg-slate-900/50 rounded-lg p-4">
                        <p class="text-slate-400 mb-1">Promedio de Escaneos</p>
                        <p class="text-2xl font-bold text-indigo-400">
                            {{ $stats['usados'] > 0 ? round($stats['total_escaneos'] / $stats['usados'], 1) : 0 }}
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
