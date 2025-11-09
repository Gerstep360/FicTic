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
            <div class="space-y-4">
                @forelse($gestiones as $gestion)
                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-3">
                                        <h3 class="text-lg font-semibold text-slate-200">
                                            {{ $gestion->nombre }}
                                        </h3>
                                        @if($gestion->publicada)
                                            <span class="px-3 py-1 text-sm font-medium rounded bg-green-900/50 border border-green-700 text-green-300">
                                                ✅ Publicada
                                            </span>
                                        @elseif($gestion->puede_publicar)
                                            <span class="px-3 py-1 text-sm font-medium rounded bg-blue-900/50 border border-blue-700 text-blue-300">
                                                📋 Lista para Publicar
                                            </span>
                                        @else
                                            <span class="px-3 py-1 text-sm font-medium rounded bg-slate-700 text-slate-300">
                                                ⏳ En Proceso
                                            </span>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                        <div>
                                            <p class="text-sm text-slate-400">Periodo</p>
                                            <p class="text-slate-200 font-medium">
                                                {{ $gestion->fecha_inicio->format('d/m/Y') }} - {{ $gestion->fecha_fin->format('d/m/Y') }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-slate-400">Aprobaciones</p>
                                            <p class="text-slate-200 font-medium">
                                                {{ $gestion->aprobaciones()->where('estado', 'aprobado_final')->count() }} / 
                                                {{ $gestion->aprobaciones()->count() }}
                                            </p>
                                        </div>
                                        @if($gestion->publicada)
                                            <div>
                                                <p class="text-sm text-slate-400">Publicado por</p>
                                                <p class="text-slate-200 font-medium">
                                                    {{ $gestion->usuarioPublicador->name ?? 'N/A' }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-slate-400">Fecha publicación</p>
                                                <p class="text-slate-200 font-medium">
                                                    {{ $gestion->fecha_publicacion?->format('d/m/Y H:i') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    @if($gestion->nota_publicacion)
                                        <div class="bg-blue-900/20 border border-blue-700/50 rounded-lg p-3 mb-3">
                                            <p class="text-sm text-blue-300 font-medium mb-1">Nota de publicación:</p>
                                            <p class="text-sm text-blue-200/80">{{ $gestion->nota_publicacion }}</p>
                                        </div>
                                    @endif

                                    <!-- Listado de aprobaciones -->
                                    @if($gestion->aprobaciones->isNotEmpty())
                                        <details class="group">
                                            <summary class="cursor-pointer text-sm text-blue-400 hover:text-blue-300 mb-2">
                                                Ver aprobaciones ({{ $gestion->aprobaciones->count() }})
                                            </summary>
                                            <div class="pl-4 space-y-1">
                                                @foreach($gestion->aprobaciones as $aprobacion)
                                                    <div class="flex items-center justify-between text-sm">
                                                        <span class="text-slate-300">
                                                            {{ $aprobacion->carrera?->nombre_carrera ?? 'Toda la facultad' }}
                                                        </span>
                                                        <span class="px-2 py-0.5 rounded text-xs {{ $aprobacion->color_estado }}">
                                                            {{ $aprobacion->estado_texto }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </details>
                                    @endif
                                </div>

                                <!-- Acciones -->
                                <div class="flex flex-col gap-2 min-w-[150px]">
                                    @if($gestion->publicada)
                                        <a href="{{ route('publicacion.maestro', $gestion->id_gestion) }}"
                                           target="_blank"
                                           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-center text-sm">
                                            Ver Publicación
                                        </a>
                                        <a href="{{ route('publicacion.pdf-maestro', $gestion->id_gestion) }}"
                                           class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition text-center text-sm">
                                            Descargar PDF
                                        </a>
                                        <form action="{{ route('publicacion.despublicar', $gestion->id_gestion) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('¿Despublicar esta gestión?')"
                                                    class="w-full px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition text-sm">
                                                Despublicar
                                            </button>
                                        </form>
                                    @elseif($gestion->puede_publicar)
                                        <button onclick="openPublicarModal({{ $gestion->id_gestion }}, '{{ $gestion->nombre }}')"
                                                class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white rounded-lg transition text-sm">
                                            Publicar
                                        </button>
                                    @else
                                        <button disabled
                                                class="px-4 py-2 bg-slate-700 text-slate-500 rounded-lg cursor-not-allowed text-sm">
                                            No disponible
                                        </button>
                                    @endif
                                </div>
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
    <div id="publicarModal" class="fixed inset-0 bg-black/50 z-50 items-center justify-center p-4" style="display: none;">
        <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 max-w-md w-full">
            <div class="px-6 py-4 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-700">
                <h3 class="text-lg font-semibold text-slate-200">Publicar Horarios</h3>
            </div>
            
            <form id="publicarForm" method="POST" class="p-6">
                @csrf
                
                <div class="mb-4">
                    <p class="text-slate-300 mb-4">
                        ¿Está seguro de publicar la gestión <span id="gestionNombre" class="font-semibold text-blue-400"></span>?
                    </p>
                    <p class="text-sm text-slate-400 mb-4">
                        Los horarios serán visibles públicamente para toda la comunidad universitaria.
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Nota de publicación (opcional)
                    </label>
                    <textarea name="nota" 
                              rows="3"
                              placeholder="Ej: Horarios oficiales del semestre 2/2025"
                              class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-700">
                    <button type="button" 
                            onclick="closeModal('publicarModal')"
                            class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg transition">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white rounded-lg transition">
                        Confirmar Publicación
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPublicarModal(idGestion, nombre) {
            document.getElementById('publicarForm').action = `/admin/publicacion/${idGestion}/publicar`;
            document.getElementById('gestionNombre').textContent = nombre;
            document.getElementById('publicarModal').style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal('publicarModal');
            }
        });
    </script>
</x-app-layout>
