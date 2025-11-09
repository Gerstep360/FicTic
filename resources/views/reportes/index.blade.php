<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reportería y Descargas') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
                    <p class="font-bold">Éxito</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
                    <p class="font-bold">Error</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            {{-- ==================
                 REPORTES DE HORARIOS
                 ================== --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200">
                    <h3 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Reportes de Horarios
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        
                        {{-- Horario por Docente --}}
                        <div class="bg-white p-4 rounded-lg shadow border border-blue-200">
                            <h4 class="font-semibold text-blue-800 mb-3">Horario por Docente</h4>
                            <form action="{{ route('reportes.horario-docente') }}" method="GET" target="_blank">
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Docente *</label>
                                    <select name="id_docente" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="">-- Seleccione --</option>
                                        @foreach($docentes as $docente)
                                            <option value="{{ $docente->id }}">{{ $docente->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Formato *</label>
                                    <select name="formato" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="pdf">PDF</option>
                                        <option value="excel">Excel</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition text-sm">
                                    Generar Reporte
                                </button>
                            </form>
                        </div>

                        {{-- Horario por Grupo --}}
                        <div class="bg-white p-4 rounded-lg shadow border border-blue-200">
                            <h4 class="font-semibold text-blue-800 mb-3">Horario por Grupo</h4>
                            <form action="{{ route('reportes.horario-grupo') }}" method="GET" target="_blank">
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Grupo *</label>
                                    <select name="id_grupo" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="">-- Seleccione --</option>
                                        @foreach($grupos as $grupo)
                                            <option value="{{ $grupo->id_grupo }}">
                                                {{ $grupo->materia->nombre ?? 'N/A' }} - {{ $grupo->nombre_grupo }} ({{ $grupo->gestion->nombre ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Formato *</label>
                                    <select name="formato" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="pdf">PDF</option>
                                        <option value="excel">Excel</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition text-sm">
                                    Generar Reporte
                                </button>
                            </form>
                        </div>

                        {{-- Horario por Aula --}}
                        <div class="bg-white p-4 rounded-lg shadow border border-blue-200">
                            <h4 class="font-semibold text-blue-800 mb-3">Horario por Aula</h4>
                            <form action="{{ route('reportes.horario-aula') }}" method="GET" target="_blank">
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Aula *</label>
                                    <select name="id_aula" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="">-- Seleccione --</option>
                                        @foreach($aulas as $aula)
                                            <option value="{{ $aula->id_aula }}">{{ $aula->codigo }} - {{ $aula->tipo }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Formato *</label>
                                    <select name="formato" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="pdf">PDF</option>
                                        <option value="excel">Excel</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition text-sm">
                                    Generar Reporte
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ====================
                 REPORTES DE ASISTENCIA
                 ==================== --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-gradient-to-r from-green-50 to-green-100 border-b border-green-200">
                    <h3 class="text-xl font-bold text-green-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Reportes de Asistencia
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        {{-- Asistencia por Docente --}}
                        <div class="bg-white p-4 rounded-lg shadow border border-green-200">
                            <h4 class="font-semibold text-green-800 mb-3">Asistencia por Docente</h4>
                            <form action="{{ route('reportes.asistencia-docente') }}" method="GET" target="_blank">
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Docente *</label>
                                    <select name="id_docente" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                        <option value="">-- Seleccione --</option>
                                        @foreach($docentes as $docente)
                                            <option value="{{ $docente->id }}">{{ $docente->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-2 mb-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Desde *</label>
                                        <input type="date" name="fecha_inicio" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Hasta *</label>
                                        <input type="date" name="fecha_fin" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Formato *</label>
                                    <select name="formato" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                        <option value="pdf">PDF</option>
                                        <option value="excel">Excel</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded transition text-sm">
                                    Generar Reporte
                                </button>
                            </form>
                        </div>

                        {{-- Asistencia por Carrera --}}
                        <div class="bg-white p-4 rounded-lg shadow border border-green-200">
                            <h4 class="font-semibold text-green-800 mb-3">Asistencia por Carrera</h4>
                            <form action="{{ route('reportes.asistencia-carrera') }}" method="GET" target="_blank">
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Carrera *</label>
                                    <select name="id_carrera" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                        <option value="">-- Seleccione --</option>
                                        @foreach($carreras as $carrera)
                                            <option value="{{ $carrera->id_carrera }}">{{ $carrera->nombre }} ({{ $carrera->facultad->nombre ?? 'N/A' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-2 mb-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Desde *</label>
                                        <input type="date" name="fecha_inicio" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Hasta *</label>
                                        <input type="date" name="fecha_fin" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Formato *</label>
                                    <select name="formato" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                        <option value="pdf">PDF</option>
                                        <option value="excel">Excel</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded transition text-sm">
                                    Generar Reporte
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==========================
                 REPORTE DE OCUPACIÓN AULAS
                 ========================== --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-gradient-to-r from-purple-50 to-purple-100 border-b border-purple-200">
                    <h3 class="text-xl font-bold text-purple-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Reporte de Ocupación de Aulas
                    </h3>
                    
                    <div class="max-w-md">
                        <div class="bg-white p-4 rounded-lg shadow border border-purple-200">
                            <h4 class="font-semibold text-purple-800 mb-3">Ocupación de Todas las Aulas</h4>
                            <p class="text-sm text-gray-600 mb-4">Genera un reporte con el porcentaje de uso de cada aula, útil para optimizar espacios.</p>
                            <form action="{{ route('reportes.ocupacion-aulas') }}" method="GET" target="_blank">
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Formato *</label>
                                    <select name="formato" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
                                        <option value="pdf">PDF</option>
                                        <option value="excel">Excel</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded transition text-sm">
                                    Generar Reporte
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Información adicional --}}
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Nota:</strong> Los reportes se generan en base a los horarios publicados y asistencias registradas. 
                            Los archivos PDF son ideales para impresión y presentaciones formales. Los archivos Excel permiten análisis adicionales con filtros y fórmulas.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
