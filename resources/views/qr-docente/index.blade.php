<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-200 leading-tight">
            CU-19. Gestión de QR Docentes
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-green-900/50 border border-green-700 text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-red-900/50 border border-red-700 text-red-200">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Información -->
            <div class="bg-gradient-to-r from-purple-900/50 to-indigo-900/50 border border-purple-700 rounded-lg p-6 mb-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <svg class="w-12 h-12 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-purple-200 mb-2">
                            Sistema de Códigos QR para Asistencia
                        </h3>
                        <p class="text-purple-300/90 text-sm mb-3">
                            Genera códigos QR únicos por docente y gestión para el registro de asistencia. 
                            Cada código está cifrado y puede ser descargado en PDF o PNG para imprimir o guardar en el dispositivo del docente.
                        </p>
                        <ul class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-purple-300/80">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Generación individual o masiva
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Tokens cifrados únicos
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Exportación PDF/PNG
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Control de activación/desactivación
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Acciones rápidas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <button onclick="openModal('generarMasivoModal')"
                        class="px-6 py-4 bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-700 hover:to-purple-600 text-white rounded-lg transition flex items-center justify-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Generación Masiva
                </button>
                <a href="{{ route('qr-docente.estadisticas') }}"
                   class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white rounded-lg transition flex items-center justify-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Ver Estadísticas
                </a>
            </div>

            <!-- Filtros -->
            <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Gestión</label>
                        <select name="id_gestion" 
                                onchange="this.form.submit()"
                                class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                            <option value="">Todas las gestiones</option>
                            @foreach($gestiones as $gest)
                                <option value="{{ $gest->id_gestion }}" {{ request('id_gestion') == $gest->id_gestion ? 'selected' : '' }}>
                                    {{ $gest->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Docente</label>
                        <select name="id_docente" 
                                onchange="this.form.submit()"
                                class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                            <option value="">Todos los docentes</option>
                            @foreach($docentes as $doc)
                                <option value="{{ $doc->id }}" {{ request('id_docente') == $doc->id ? 'selected' : '' }}>
                                    {{ $doc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Estado</label>
                        <select name="activo" 
                                onchange="this.form.submit()"
                                class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                            <option value="">Todos</option>
                            <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
                            <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Buscar</label>
                        <input type="text" 
                               name="buscar" 
                               value="{{ request('buscar') }}"
                               placeholder="Nombre o email..."
                               class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                    </div>
                </form>
            </div>

            <!-- Tabla de QR -->
            <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-900/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-slate-300 font-semibold">Docente</th>
                                <th class="px-4 py-3 text-left text-slate-300 font-semibold">Gestión</th>
                                <th class="px-4 py-3 text-center text-slate-300 font-semibold">Estado</th>
                                <th class="px-4 py-3 text-center text-slate-300 font-semibold">Veces Usado</th>
                                <th class="px-4 py-3 text-center text-slate-300 font-semibold">Último Uso</th>
                                <th class="px-4 py-3 text-center text-slate-300 font-semibold">Generado</th>
                                <th class="px-4 py-3 text-center text-slate-300 font-semibold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tokens as $token)
                                <tr class="border-b border-slate-700 hover:bg-slate-750">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-slate-200">{{ $token->docente->name }}</div>
                                        <div class="text-xs text-slate-400">{{ $token->docente->email }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-300">
                                        {{ $token->gestion->nombre }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($token->activo)
                                            <span class="px-2 py-1 text-xs rounded bg-green-900/50 border border-green-700 text-green-300">
                                                ✓ Activo
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded bg-slate-700 text-slate-400">
                                                Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center text-slate-300">
                                        <span class="px-2 py-1 rounded bg-blue-900/30 text-blue-300 font-mono text-xs">
                                            {{ $token->veces_usado }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center text-slate-400 text-xs">
                                        {{ $token->ultimo_uso?->format('d/m/Y H:i') ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-slate-400 text-xs">
                                        {{ $token->fecha_generacion->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('qr-docente.ver', $token->id_qr_token) }}"
                                               class="px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded text-xs transition"
                                               title="Ver QR">
                                                Ver QR
                                            </a>
                                            @if($token->activo)
                                                <form action="{{ route('qr-docente.desactivar', $token->id_qr_token) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            onclick="return confirm('¿Desactivar este QR?')"
                                                            class="px-3 py-1 bg-orange-600 hover:bg-orange-700 text-white rounded text-xs transition"
                                                            title="Desactivar">
                                                        Desactivar
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('qr-docente.activar', $token->id_qr_token) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            onclick="return confirm('¿Reactivar este QR?')"
                                                            class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs transition"
                                                            title="Activar">
                                                        Activar
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('qr-docente.regenerar', $token->id_qr_token) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        onclick="return confirm('¿Regenerar código? El anterior dejará de funcionar.')"
                                                        class="px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white rounded text-xs transition"
                                                        title="Regenerar token">
                                                    Regenerar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center">
                                        <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>
                                        <p class="text-slate-400 text-lg">No hay códigos QR generados</p>
                                        <p class="text-slate-500 text-sm mt-2">Use la generación masiva para crear QR para todos los docentes</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $tokens->links() }}
            </div>

        </div>
    </div>

    <!-- Modal Generación Masiva -->
    <div id="generarMasivoModal" class="fixed inset-0 bg-black/50 z-50 items-center justify-center p-4" style="display: none;">
        <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 max-w-md w-full">
            <div class="px-6 py-4 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-700">
                <h3 class="text-lg font-semibold text-slate-200">Generación Masiva de QR</h3>
            </div>
            
            <form action="{{ route('qr-docente.generar-masivo') }}" method="POST" class="p-6">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Gestión <span class="text-red-400">*</span>
                    </label>
                    <select name="id_gestion" required class="w-full rounded-lg bg-slate-700 border-slate-600 text-slate-200">
                        <option value="">Seleccione una gestión</option>
                        @foreach($gestiones as $gest)
                            <option value="{{ $gest->id_gestion }}">{{ $gest->nombre }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-400 mt-1">
                        Se generarán códigos QR para todos los docentes activos en esta gestión
                    </p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-700">
                    <button type="button" 
                            onclick="closeModal('generarMasivoModal')"
                            class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg transition">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-700 hover:to-purple-600 text-white rounded-lg transition">
                        Generar Códigos QR
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal('generarMasivoModal');
            }
        });
    </script>
</x-app-layout>
