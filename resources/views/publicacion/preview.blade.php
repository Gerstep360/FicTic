<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('publicacion.index') }}" 
                   class="p-2 hover:bg-slate-700 rounded-lg transition">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                        Vista Previa - {{ $gestion->nombre }}
                    </h2>
                    <p class="text-sm text-slate-400 mt-1">
                        {{ $gestion->fecha_inicio->format('d/m/Y') }} - {{ $gestion->fecha_fin->format('d/m/Y') }}
                    </p>
                </div>
            </div>
            
            @if($gestion->puede_publicar)
                <button onclick="openPublicarModal()" 
                        class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white rounded-lg transition flex items-center gap-2 font-bold shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Publicar Ahora
                </button>
            @else
                <span class="px-4 py-2 bg-yellow-900/50 border border-yellow-700 text-yellow-300 rounded-lg text-sm">
                    ⚠️ Pendiente de aprobaciones
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Estadísticas Generales -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-gradient-to-br from-blue-900/40 to-blue-800/30 border border-blue-700/50 rounded-lg p-4">
                    <div class="text-3xl font-bold text-blue-300 mb-1">{{ $stats['total_horarios'] }}</div>
                    <div class="text-sm text-slate-400">Horarios Totales</div>
                </div>
                <div class="bg-gradient-to-br from-purple-900/40 to-purple-800/30 border border-purple-700/50 rounded-lg p-4">
                    <div class="text-3xl font-bold text-purple-300 mb-1">{{ $stats['total_docentes'] }}</div>
                    <div class="text-sm text-slate-400">Docentes</div>
                </div>
                <div class="bg-gradient-to-br from-green-900/40 to-green-800/30 border border-green-700/50 rounded-lg p-4">
                    <div class="text-3xl font-bold text-green-300 mb-1">{{ $stats['total_materias'] }}</div>
                    <div class="text-sm text-slate-400">Materias</div>
                </div>
                <div class="bg-gradient-to-br from-orange-900/40 to-orange-800/30 border border-orange-700/50 rounded-lg p-4">
                    <div class="text-3xl font-bold text-orange-300 mb-1">{{ $stats['total_grupos'] }}</div>
                    <div class="text-sm text-slate-400">Grupos</div>
                </div>
                <div class="bg-gradient-to-br from-pink-900/40 to-pink-800/30 border border-pink-700/50 rounded-lg p-4">
                    <div class="text-3xl font-bold text-pink-300 mb-1">{{ $stats['total_aulas'] }}</div>
                    <div class="text-sm text-slate-400">Aulas</div>
                </div>
            </div>

            <!-- Tabs de Navegación -->
            <div class="bg-slate-800 rounded-lg border border-slate-700 overflow-hidden">
                <div class="flex border-b border-slate-700">
                    <button onclick="showTab('horarios')" id="tab-horarios" 
                            class="tab-button active px-6 py-4 font-medium transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Horarios Completos
                    </button>
                    <button onclick="showTab('docentes')" id="tab-docentes" 
                            class="tab-button px-6 py-4 font-medium transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Docentes ({{ $stats['total_docentes'] }})
                    </button>
                    <button onclick="showTab('aulas')" id="tab-aulas" 
                            class="tab-button px-6 py-4 font-medium transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Aulas ({{ $stats['total_aulas'] }})
                    </button>
                    <button onclick="showTab('aprobaciones')" id="tab-aprobaciones" 
                            class="tab-button px-6 py-4 font-medium transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Aprobaciones
                    </button>
                </div>

                <!-- Tab Content: Horarios -->
                <div id="content-horarios" class="tab-content p-6">
                    @foreach($horariosPorCarrera as $carrera => $horariosCarrera)
                        <div class="mb-8 last:mb-0">
                            <div class="bg-gradient-to-r from-blue-900/30 to-blue-800/20 border-l-4 border-blue-500 p-4 mb-4 rounded-r-lg">
                                <h3 class="text-lg font-bold text-blue-300">{{ $carrera }}</h3>
                                <p class="text-sm text-slate-400">{{ $horariosCarrera->count() }} horarios asignados</p>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-slate-900/50 border-b border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-slate-300 font-semibold">Día</th>
                                            <th class="px-4 py-3 text-left text-slate-300 font-semibold">Bloque</th>
                                            <th class="px-4 py-3 text-left text-slate-300 font-semibold">Materia</th>
                                            <th class="px-4 py-3 text-left text-slate-300 font-semibold">Grupo</th>
                                            <th class="px-4 py-3 text-left text-slate-300 font-semibold">Docente</th>
                                            <th class="px-4 py-3 text-left text-slate-300 font-semibold">Aula</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-700">
                                        @foreach($horariosCarrera as $horario)
                                            <tr class="hover:bg-slate-700/30 transition">
                                                <td class="px-4 py-3 text-slate-300">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium
                                                        @if($horario->dia_semana === 'Lunes') bg-blue-900/50 text-blue-300
                                                        @elseif($horario->dia_semana === 'Martes') bg-green-900/50 text-green-300
                                                        @elseif($horario->dia_semana === 'Miércoles') bg-yellow-900/50 text-yellow-300
                                                        @elseif($horario->dia_semana === 'Jueves') bg-purple-900/50 text-purple-300
                                                        @elseif($horario->dia_semana === 'Viernes') bg-pink-900/50 text-pink-300
                                                        @else bg-slate-700 text-slate-300
                                                        @endif">
                                                        {{ $horario->dia_semana }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-slate-300 font-mono">
                                                    {{ $horario->bloque->etiqueta }}
                                                    <span class="text-xs text-slate-500 block">
                                                        {{ $horario->bloque->hora_inicio }} - {{ $horario->bloque->hora_fin }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="text-slate-200 font-medium">{{ $horario->grupo->materia->nombre }}</div>
                                                    <div class="text-xs text-slate-500">{{ $horario->grupo->materia->codigo }}</div>
                                                </td>
                                                <td class="px-4 py-3 text-slate-300">{{ $horario->grupo->nombre }}</td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-8 h-8 rounded-full bg-blue-900/50 border border-blue-700 flex items-center justify-center text-xs font-bold text-blue-300">
                                                            {{ substr($horario->docente->name, 0, 2) }}
                                                        </div>
                                                        <span class="text-slate-300">{{ $horario->docente->name }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="text-slate-200 font-medium">{{ $horario->aula->codigo }}</div>
                                                    @if($horario->aula->edificio)
                                                        <div class="text-xs text-slate-500">{{ $horario->aula->edificio }}</div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Tab Content: Docentes -->
                <div id="content-docentes" class="tab-content hidden p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($docentes as $docenteData)
                            <div class="bg-slate-900/50 border border-slate-700 rounded-lg p-4 hover:border-blue-600 transition">
                                <div class="flex items-start gap-3">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center text-lg font-bold text-white">
                                        {{ substr($docenteData['docente']->name, 0, 2) }}
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-slate-200 mb-1">{{ $docenteData['docente']->name }}</h4>
                                        <p class="text-sm text-slate-400 mb-2">{{ $docenteData['docente']->email }}</p>
                                        <div class="flex items-center gap-4 text-xs">
                                            <span class="px-2 py-1 bg-blue-900/50 border border-blue-700 text-blue-300 rounded">
                                                📚 {{ $docenteData['materias']->count() }} materias
                                            </span>
                                            <span class="px-2 py-1 bg-green-900/50 border border-green-700 text-green-300 rounded">
                                                ⏱️ {{ $docenteData['total_horas'] }} horas/semana
                                            </span>
                                        </div>
                                        <details class="mt-3">
                                            <summary class="text-xs text-blue-400 cursor-pointer hover:text-blue-300">
                                                Ver materias asignadas
                                            </summary>
                                            <ul class="mt-2 space-y-1 pl-4">
                                                @foreach($docenteData['materias'] as $materia)
                                                    <li class="text-sm text-slate-400">• {{ $materia }}</li>
                                                @endforeach
                                            </ul>
                                        </details>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Tab Content: Aulas -->
                <div id="content-aulas" class="tab-content hidden p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($aulas as $aulaData)
                            <div class="bg-slate-900/50 border border-slate-700 rounded-lg p-4 hover:border-purple-600 transition">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 rounded bg-gradient-to-br from-purple-600 to-purple-800 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-slate-200">{{ $aulaData['aula']->codigo }}</h4>
                                        @if($aulaData['aula']->edificio)
                                            <p class="text-xs text-slate-400">{{ $aulaData['aula']->edificio }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-slate-400">Ocupación:</span>
                                    <span class="font-bold text-purple-300">{{ $aulaData['ocupacion'] }} bloques</span>
                                </div>
                                <div class="flex items-center justify-between text-sm mt-1">
                                    <span class="text-slate-400">Grupos:</span>
                                    <span class="font-bold text-blue-300">{{ $aulaData['grupos'] }}</span>
                                </div>
                                @if($aulaData['aula']->capacidad)
                                    <div class="flex items-center justify-between text-sm mt-1">
                                        <span class="text-slate-400">Capacidad:</span>
                                        <span class="font-bold text-green-300">{{ $aulaData['aula']->capacidad }} personas</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Tab Content: Aprobaciones -->
                <div id="content-aprobaciones" class="tab-content hidden p-6">
                    @forelse($gestion->aprobaciones as $aprobacion)
                        <div class="bg-slate-900/50 border border-slate-700 rounded-lg p-6 mb-4 last:mb-0">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h4 class="text-lg font-semibold text-slate-200 mb-1">
                                        {{ $aprobacion->carrera?->nombre_carrera ?? '🏛️ Toda la Facultad' }}
                                    </h4>
                                    @if($aprobacion->carrera)
                                        <p class="text-sm text-slate-400">{{ $aprobacion->carrera->facultad->nombre_facultad }}</p>
                                    @endif
                                </div>
                                <span class="px-3 py-1.5 rounded-lg text-sm font-semibold {{ $aprobacion->color_estado }} border">
                                    {{ $aprobacion->icono_estado }} {{ $aprobacion->estado_texto }}
                                </span>
                            </div>

                            <div class="grid grid-cols-3 gap-4 mb-4">
                                <div class="bg-slate-800 rounded p-3 text-center">
                                    <div class="text-2xl font-bold text-blue-300">{{ $aprobacion->total_horarios }}</div>
                                    <div class="text-xs text-slate-400">Horarios</div>
                                </div>
                                <div class="bg-slate-800 rounded p-3 text-center">
                                    <div class="text-2xl font-bold text-green-300">{{ $aprobacion->horarios_validados }}</div>
                                    <div class="text-xs text-slate-400">Validados</div>
                                </div>
                                <div class="bg-slate-800 rounded p-3 text-center">
                                    <div class="text-2xl font-bold {{ $aprobacion->conflictos_pendientes > 0 ? 'text-orange-300' : 'text-green-300' }}">
                                        {{ $aprobacion->conflictos_pendientes }}
                                    </div>
                                    <div class="text-xs text-slate-400">Conflictos</div>
                                </div>
                            </div>

                            <div class="border-t border-slate-700 pt-4">
                                <p class="text-xs font-semibold text-slate-400 mb-3">FLUJO DE APROBACIÓN</p>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-green-900/50 border border-green-700 flex items-center justify-center">
                                            <svg class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm text-slate-300">
                                            Coordinador: <strong>{{ $aprobacion->coordinador?->name ?? 'N/A' }}</strong>
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full {{ $aprobacion->id_director ? 'bg-green-900/50 border-green-700' : 'bg-slate-700 border-slate-600' }} border flex items-center justify-center">
                                            <svg class="w-3 h-3 {{ $aprobacion->id_director ? 'text-green-400' : 'text-slate-500' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm {{ $aprobacion->id_director ? 'text-slate-300' : 'text-slate-500' }}">
                                            Director: <strong>{{ $aprobacion->director?->name ?? 'Pendiente' }}</strong>
                                        </span>
                                    </div>
                                    @if($aprobacion->id_decano || $aprobacion->fecha_envio_decano)
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full {{ $aprobacion->id_decano ? 'bg-green-900/50 border-green-700' : 'bg-slate-700 border-slate-600' }} border flex items-center justify-center">
                                                <svg class="w-3 h-3 {{ $aprobacion->id_decano ? 'text-green-400' : 'text-slate-500' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <span class="text-sm {{ $aprobacion->id_decano ? 'text-slate-300' : 'text-slate-500' }}">
                                                Decano: <strong>{{ $aprobacion->decano?->name ?? 'Pendiente' }}</strong>
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-slate-400">
                            No hay aprobaciones registradas
                        </div>
                    @endforelse
                </div>
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
            
            <form action="{{ route('publicacion.publicar', $gestion) }}" method="POST" class="p-6">
                @csrf
                
                <div class="mb-6">
                    <div class="bg-green-900/20 border border-green-700/50 rounded-lg p-4">
                        <p class="text-green-300 font-semibold mb-2">
                            ¿Publicar {{ $stats['total_horarios'] }} horarios de {{ $gestion->nombre }}?
                        </p>
                        <p class="text-sm text-green-200/80">
                            Los horarios serán visibles públicamente para toda la comunidad universitaria.
                        </p>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Nota de publicación (opcional)
                    </label>
                    <textarea name="nota" 
                              rows="3"
                              placeholder="Ej: Horarios oficiales del semestre {{ $gestion->nombre }}"
                              class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200 placeholder-slate-500"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-700">
                    <button type="button" 
                            onclick="closeModal()"
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

    <style>
        .tab-button {
            @apply text-slate-400 border-b-2 border-transparent;
        }
        .tab-button.active {
            @apply text-blue-400 border-blue-500 bg-slate-900/30;
        }
        .tab-button:hover:not(.active) {
            @apply text-slate-300 bg-slate-700/30;
        }
    </style>

    <script>
        function showTab(tabName) {
            // Hide all content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });
            
            // Show selected content and activate button
            document.getElementById(`content-${tabName}`).classList.remove('hidden');
            document.getElementById(`tab-${tabName}`).classList.add('active');
        }

        function openPublicarModal() {
            document.getElementById('publicarModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('publicarModal').style.display = 'none';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        document.getElementById('publicarModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</x-app-layout>
