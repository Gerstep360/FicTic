<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalle de Suplencia') }}
            </h2>
            <a href="{{ route('suplencias.index') }}" class="btn-ghost gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-4 rounded">
                    <p class="font-bold">Éxito</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="card">
                <div class="p-6">
                    
                    {{-- Información Principal en Grid --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                        
                        {{-- Fecha y Estado --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Fecha de la Clase</label>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $suplencia->fecha_clase->format('d/m/Y') }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ $suplencia->fecha_clase->translatedFormat('l') }}
                            </p>
                            @if($suplencia->fecha_clase->isPast())
                                <span class="inline-block mt-2 px-2 py-1 text-xs bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 rounded">Pasada</span>
                            @elseif($suplencia->fecha_clase->isToday())
                                <span class="inline-block mt-2 px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded">Hoy</span>
                            @else
                                <span class="inline-block mt-2 px-2 py-1 text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded">Próxima</span>
                            @endif
                        </div>

                        {{-- Materia y Horario --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Materia</label>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $suplencia->horario->grupo->materia->nombre }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Grupo: {{ $suplencia->horario->grupo->nombre_grupo }}
                            </p>
                        </div>

                        {{-- Aula y Bloque --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Aula</label>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $suplencia->horario->aula->codigo }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ $suplencia->horario->bloque->hora_inicio }} - {{ $suplencia->horario->bloque->hora_fin }}
                                @if($suplencia->horario->bloque->etiqueta)
                                    <span class="text-xs">({{ $suplencia->horario->bloque->etiqueta }})</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700 my-6">

                    {{-- Docentes --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        
                        {{-- Docente Ausente --}}
                        <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Docente Ausente</h3>
                            </div>
                            <p class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-1">
                                {{ $suplencia->docenteAusente->name }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $suplencia->docenteAusente->email }}
                            </p>
                        </div>

                        {{-- Docente Suplente --}}
                        <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Docente Suplente</h3>
                                @if($suplencia->id_docente_externo)
                                    <span class="text-xs text-gray-500 dark:text-gray-400 italic">(externo)</span>
                                @endif
                            </div>
                            <p class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-1">
                                {{ $suplencia->nombre_suplente }}
                            </p>
                            
                            @if($suplencia->docenteExterno)
                                {{-- Información de docente externo --}}
                                @if($suplencia->docenteExterno->especialidad)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $suplencia->docenteExterno->especialidad }}
                                    </p>
                                @endif
                                <div class="mt-2 space-y-1">
                                    @if($suplencia->docenteExterno->telefono)
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            📱 {{ $suplencia->docenteExterno->telefono }}
                                        </p>
                                    @endif
                                    @if($suplencia->docenteExterno->email)
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            ✉️ {{ $suplencia->docenteExterno->email }}
                                        </p>
                                    @endif
                                </div>
                            @else
                                {{-- Información de docente interno --}}
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $suplencia->docenteSuplente->email ?? 'N/A' }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Observaciones --}}
                    @if($suplencia->observaciones)
                        <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700 mb-6">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Observaciones</h3>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $suplencia->observaciones }}</p>
                        </div>
                    @endif

                    {{-- Metadatos --}}
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Registrado el {{ $suplencia->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    {{-- Acciones --}}
                    <div class="mt-6 flex gap-3">
                        <a href="{{ route('suplencias.index') }}" class="btn-ghost gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Volver al Listado
                        </a>

                        @if(!$suplencia->fecha_clase->isPast())
                            <form action="{{ route('suplencias.destroy', $suplencia) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta suplencia?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Eliminar Suplencia
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
