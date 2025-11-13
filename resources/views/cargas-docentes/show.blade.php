<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalle de Carga Docente') }}
            </h2>
            <div class="flex gap-2">
                @can('registrar_carga_docente')
                    <a href="{{ route('cargas-docentes.edit', $cargaDocente) }}" class="btn-primary gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar
                    </a>
                @endcan
                <a href="{{ route('cargas-docentes.index') }}" class="btn-ghost gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('status'))
                <div class="mb-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-4 rounded" role="alert">
                    <p class="font-bold">Éxito</p>
                    <p>{{ session('status') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Información Principal --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Datos del Docente --}}
                    <div class="card">
                        <div class="p-6 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border-b border-blue-200 dark:border-blue-700">
                            <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-300 mb-4 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Información del Docente
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-start">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Nombre:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100 text-right">{{ $cargaDocente->docente->name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Email:</span>
                                    <span class="text-sm text-gray-900 dark:text-gray-100 text-right">{{ $cargaDocente->docente->email ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Datos de la Carga --}}
                    <div class="card">
                        <div class="p-6 bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 border-b border-purple-200 dark:border-purple-700">
                            <h3 class="text-lg font-semibold text-purple-900 dark:text-purple-300 mb-4 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Detalles de la Carga Docente
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-start">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Gestión:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100 text-right">{{ $cargaDocente->gestion->nombre ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Carrera:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100 text-right">{{ $cargaDocente->carrera->nombre ?? 'Sin carrera específica' }}</span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Tipo de Contrato:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100 text-right">{{ $cargaDocente->tipo_contrato ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Categoría:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100 text-right">{{ $cargaDocente->categoria ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Observaciones --}}
                    @if($cargaDocente->observaciones)
                        <div class="card">
                            <div class="p-6 bg-gradient-to-r from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 border-b border-yellow-200 dark:border-yellow-700">
                                <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-300 mb-4 flex items-center">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                    </svg>
                                    Observaciones
                                </h3>
                                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $cargaDocente->observaciones }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Restricciones de Horario --}}
                    @if($cargaDocente->restricciones_horario)
                        <div class="card">
                            <div class="p-6 bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border-b border-red-200 dark:border-red-700">
                                <h3 class="text-lg font-semibold text-red-900 dark:text-red-300 mb-4 flex items-center">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Restricciones de Horario
                                </h3>
                                <pre class="text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 p-3 rounded">{{ json_encode(json_decode($cargaDocente->restricciones_horario), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Panel Lateral --}}
                <div class="space-y-6">
                    
                    {{-- Estadísticas de Horas --}}
                    <div class="card">
                        <div class="p-6 bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border-b border-green-200 dark:border-green-700">
                            <h3 class="text-lg font-semibold text-green-900 dark:text-green-300 mb-4 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Carga Horaria
                            </h3>
                            
                            <div class="space-y-4">
                                {{-- Horas Contratadas --}}
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Horas Contratadas</span>
                                        <span class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $cargaDocente->horas_contratadas }}</span>
                                    </div>
                                </div>

                                {{-- Horas Asignadas --}}
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Horas Asignadas</span>
                                        <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $cargaDocente->horas_asignadas }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        @php
                                            $porcentaje = $cargaDocente->horas_contratadas > 0 
                                                ? ($cargaDocente->horas_asignadas / $cargaDocente->horas_contratadas) * 100 
                                                : 0;
                                            $porcentaje = min($porcentaje, 100);
                                        @endphp
                                        <div class="bg-blue-600 dark:bg-blue-400 h-2 rounded-full transition-all duration-300" style="width: {{ $porcentaje }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">{{ number_format($porcentaje, 1) }}% utilizado</span>
                                </div>

                                {{-- Horas Disponibles --}}
                                <div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Horas Disponibles</span>
                                        <span class="text-2xl font-bold {{ $cargaDocente->horas_contratadas - $cargaDocente->horas_asignadas > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $cargaDocente->horas_contratadas - $cargaDocente->horas_asignadas }}
                                        </span>
                                    </div>
                                </div>

                                @if($cargaDocente->horas_asignadas > $cargaDocente->horas_contratadas)
                                    <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-3 rounded">
                                        <p class="text-xs text-red-700 dark:text-red-300">
                                            <strong>⚠️ Sobrecarga:</strong> El docente tiene {{ $cargaDocente->horas_asignadas - $cargaDocente->horas_contratadas }} horas excedentes.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="card">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Acciones</h3>
                            <div class="space-y-2">
                                @can('registrar_carga_docente')
                                    <a href="{{ route('cargas-docentes.edit', $cargaDocente) }}" class="btn-primary w-full justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Editar Carga
                                    </a>
                                    
                                    @if($cargaDocente->horas_asignadas == 0)
                                        <form action="{{ route('cargas-docentes.destroy', $cargaDocente) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta carga docente?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Eliminar Carga
                                            </button>
                                        </form>
                                    @else
                                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 p-3 rounded">
                                            <p class="text-xs text-yellow-700 dark:text-yellow-300">
                                                <strong>ℹ️ Nota:</strong> No se puede eliminar una carga con horas asignadas.
                                            </p>
                                        </div>
                                    @endif
                                @endcan
                            </div>
                        </div>
                    </div>

                    {{-- Metadatos --}}
                    <div class="card">
                        <div class="p-6 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">Información del Sistema</h3>
                            <div class="space-y-2 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">ID:</span>
                                    <span class="text-gray-900 dark:text-gray-100 font-mono">{{ $cargaDocente->id_carga }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Creado:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $cargaDocente->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Actualizado:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $cargaDocente->updated_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
