<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Registrar Nueva Suplencia') }}
            </h2>
            <a href="{{ route('suplencias.index') }}" class="btn-ghost gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Cancelar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('error'))
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 rounded">
                    <p class="font-bold">Error</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 rounded">
                    <p class="font-bold">Por favor corrige los siguientes errores:</p>
                    <ul class="list-disc list-inside mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="p-6">
                    <form method="POST" action="{{ route('suplencias.store') }}" class="space-y-6">
                        @csrf

                        {{-- Docente Ausente --}}
                        <div>
                            <label for="id_docente_ausente" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Docente Ausente <span class="text-red-500">*</span>
                            </label>
                            <select name="id_docente_ausente" id="id_docente_ausente" required 
                                    class="input @error('id_docente_ausente') border-red-500 @enderror"
                                    onchange="cargarHorarios()">
                                <option value="">-- Seleccione un docente --</option>
                                @if($justificacion)
                                    <option value="{{ $justificacion->id_docente }}" selected>
                                        {{ $justificacion->docente->name }}
                                    </option>
                                @else
                                    @foreach($todosDocentes as $docente)
                                        <option value="{{ $docente->id }}" {{ old('id_docente_ausente') == $docente->id ? 'selected' : '' }}>
                                            {{ $docente->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('id_docente_ausente')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Fecha de la Clase --}}
                        <div>
                            <label for="fecha_clase" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Fecha de la Clase <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="fecha_clase" id="fecha_clase" 
                                   value="{{ old('fecha_clase', $justificacion ? \Carbon\Carbon::parse($justificacion->fecha_clase)->format('Y-m-d') : '') }}" 
                                   required
                                   min="{{ date('Y-m-d') }}"
                                   class="input @error('fecha_clase') border-red-500 @enderror"
                                   onchange="buscarDocentesDisponibles()">
                            @error('fecha_clase')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Horario (se llena después de seleccionar docente) --}}
                        <div id="horario-container">
                            <label for="id_horario" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Horario de la Clase <span class="text-red-500">*</span>
                            </label>
                            <select name="id_horario" id="id_horario" required
                                    class="input @error('id_horario') border-red-500 @enderror"
                                    onchange="buscarDocentesDisponibles()">
                                <option value="">-- Seleccione un horario --</option>
                                @if($justificacion && $horarios->count() > 0)
                                    @foreach($horarios as $horario)
                                        <option value="{{ $horario->id_horario }}" {{ old('id_horario') == $horario->id_horario ? 'selected' : '' }}>
                                            {{ $horario->grupo->materia->nombre_materia ?? 'N/A' }} - 
                                            {{ $horario->grupo->nombre_grupo ?? 'N/A' }} - 
                                            {{ $horario->bloque->nombre_bloque ?? 'N/A' }} 
                                            ({{ ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'][$horario->dia_semana] ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('id_horario')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Seleccione el horario de la clase que necesita suplencia</p>
                        </div>

                        {{-- Docente Suplente --}}
                        <div id="suplente-container">
                            <div class="flex items-center justify-between mb-2">
                                <label for="id_docente_suplente" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Docente Suplente <span class="text-red-500">*</span>
                                </label>
                                <button type="button" onclick="abrirModalDocenteExterno()" 
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 flex items-center gap-1 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Agregar Docente Externo
                                </button>
                            </div>
                            <select name="id_docente_suplente" id="id_docente_suplente" required
                                    class="input @error('id_docente_suplente') border-red-500 @enderror">
                                <option value="">-- Seleccione un docente suplente --</option>
                                @if(old('id_docente_suplente'))
                                    @foreach($docentes as $docente)
                                        <option value="{{ $docente->id }}" {{ old('id_docente_suplente') == $docente->id ? 'selected' : '' }}>
                                            {{ $docente->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('id_docente_suplente')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="disponibles-info">
                                @if(!old('id_docente_suplente'))
                                    Complete el horario y fecha para ver docentes disponibles
                                @endif
                            </p>
                        </div>

                        {{-- Campos ocultos para docente externo --}}
                        <input type="hidden" name="es_docente_externo" id="es_docente_externo" value="0">
                        <input type="hidden" name="nombre_completo_externo" id="nombre_completo_externo_hidden">
                        <input type="hidden" name="especialidad_externo" id="especialidad_externo_hidden">
                        <input type="hidden" name="telefono_externo" id="telefono_externo_hidden">
                        <input type="hidden" name="email_externo" id="email_externo_hidden">
                        <input type="hidden" name="observaciones_externo" id="observaciones_externo_hidden">

                        {{-- Observaciones --}}
                        <div>
                            <label for="observaciones" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Observaciones
                            </label>
                            <textarea name="observaciones" id="observaciones" rows="3" 
                                      class="input @error('observaciones') border-red-500 @enderror"
                                      placeholder="Información adicional sobre la suplencia...">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Botones --}}
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('suplencias.index') }}" class="btn-ghost">
                                Cancelar
                            </a>
                            <button type="submit" class="btn-primary">
                                Registrar Suplencia
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para Agregar Docente Externo - Estilo Dark Minimalista --}}
    <div id="modalDocenteExterno" 
         class="fixed inset-0 z-50 items-center justify-center p-4 transition-all duration-300" 
         style="display: none; opacity: 0;">
        {{-- Overlay con blur --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-md"></div>
        
        {{-- Contenedor del Modal con animación --}}
        <div id="modalContent" 
             class="relative bg-white dark:bg-gray-800 rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden transform transition-all duration-300 scale-95 opacity-0 border border-gray-200 dark:border-gray-700">
            
            {{-- Header minimalista --}}
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg transition-all duration-300 hover:scale-110">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                Agregar Docente Externo
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Complete los datos del docente</p>
                        </div>
                    </div>
                    <button type="button" 
                            onclick="cerrarModalDocenteExterno()" 
                            class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all duration-200 transform hover:rotate-90">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Body con scroll suave --}}
            <div class="p-6 space-y-4 overflow-y-auto max-h-[calc(90vh-200px)] custom-scrollbar">
                {{-- Nombre Completo --}}
                <div class="group">
                    <label for="modal_nombre_completo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nombre Completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="modal_nombre_completo" 
                           class="input focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 transition-all duration-200"
                           placeholder="Ej: Dr. Juan Pérez García"
                           required>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Ingrese el nombre completo del docente</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Especialidad --}}
                    <div class="group">
                        <label for="modal_especialidad" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Especialidad
                        </label>
                        <input type="text" 
                               id="modal_especialidad" 
                               class="input focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 transition-all duration-200"
                               placeholder="Ej: Ingeniería de Software">
                    </div>

                    {{-- Teléfono --}}
                    <div class="group">
                        <label for="modal_telefono" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Teléfono
                        </label>
                        <input type="text" 
                               id="modal_telefono" 
                               class="input focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 transition-all duration-200"
                               placeholder="Ej: +591 70123456">
                    </div>
                </div>

                {{-- Email --}}
                <div class="group">
                    <label for="modal_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email
                    </label>
                    <input type="email" 
                           id="modal_email" 
                           class="input focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 transition-all duration-200"
                           placeholder="Ej: juan.perez@email.com">
                </div>

                {{-- Observaciones --}}
                <div class="group">
                    <label for="modal_observaciones" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Observaciones
                    </label>
                    <textarea id="modal_observaciones" 
                              rows="3"
                              class="input resize-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 transition-all duration-200"
                              placeholder="Información adicional sobre el docente..."></textarea>
                </div>

                {{-- Mensaje de éxito con animación --}}
                <div id="mensaje-exito-modal" class="hidden animate-fade-in">
                    <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded-r-lg p-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-green-800 dark:text-green-200">¡Docente agregado exitosamente!</p>
                                <p class="text-xs text-green-700 dark:text-green-300 mt-0.5">El docente ya está disponible en el listado</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer con botones --}}
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center justify-end gap-3">
                    <button type="button" 
                            onclick="cerrarModalDocenteExterno()" 
                            class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-all duration-200 font-medium">
                        Cancelar
                    </button>
                    <button type="button" 
                            onclick="guardarDocenteExterno()" 
                            id="btnGuardarDocente"
                            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white rounded-lg font-semibold flex items-center gap-2 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Agregar Docente</span>
                        
                        {{-- Loading spinner (oculto por defecto) --}}
                        <svg class="hidden w-5 h-5 animate-spin" id="btnGuardarLoading" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Estilos CSS minimalistas con animaciones suaves --}}
    <style>
        @keyframes fade-in {
            from { 
                opacity: 0; 
                transform: translateY(-10px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-8px); }
            20%, 40%, 60%, 80% { transform: translateX(8px); }
        }
        
        .animate-fade-in {
            animation: fade-in 0.4s ease-out;
        }
        
        .animate-shake {
            animation: shake 0.5s ease-in-out;
        }
        
        /* Scrollbar minimalista */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(107, 114, 128, 0.3);
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(107, 114, 128, 0.5);
        }
        
        /* Transiciones suaves globales para el modal */
        #modalDocenteExterno * {
            transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
            transition-duration: 200ms;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Efecto de focus suave */
        .input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
    </style>

    <script>
        // Al cargar la página, verificar si hay valores old() o de justificación para autocompletar
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== INICIANDO FORMULARIO DE SUPLENCIAS ===');
            
            const docenteAusenteSelect = document.getElementById('id_docente_ausente');
            const horarioSelect = document.getElementById('id_horario');
            const fechaClaseInput = document.getElementById('fecha_clase');
            
            const docenteAusente = docenteAusenteSelect ? docenteAusenteSelect.value : null;
            const idHorario = horarioSelect ? horarioSelect.value : null;
            const fechaClase = fechaClaseInput ? fechaClaseInput.value : null;
            
            console.log('Valores iniciales:', {
                docenteAusente,
                idHorario,
                fechaClase
            });
            
            // Si ya hay un docente ausente seleccionado, cargar sus horarios
            if (docenteAusente && docenteAusente !== '') {
                console.log('✓ Docente ausente detectado, cargando horarios automáticamente...');
                setTimeout(() => {
                    cargarHorarios();
                }, 500);
            } else {
                console.log('⚠ No hay docente ausente seleccionado');
            }
            
            // Si ya hay un horario seleccionado (errores de validación o justificación), buscar docentes
            if (docenteAusente && idHorario && fechaClase) {
                console.log('✓ Todos los datos presentes, buscando docentes disponibles...');
                setTimeout(() => {
                    buscarDocentesDisponibles();
                }, 1000);
            }
        });

        // Abrir modal con animaciones suaves
        function abrirModalDocenteExterno() {
            const modal = document.getElementById('modalDocenteExterno');
            const modalContent = document.getElementById('modalContent');
            
            // Limpiar campos
            document.getElementById('modal_nombre_completo').value = '';
            document.getElementById('modal_especialidad').value = '';
            document.getElementById('modal_telefono').value = '';
            document.getElementById('modal_email').value = '';
            document.getElementById('modal_observaciones').value = '';
            document.getElementById('mensaje-exito-modal').classList.add('hidden');
            
            // Mostrar modal y animar
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Prevenir scroll del body
            
            // Forzar reflow para que la animación funcione
            modal.offsetHeight;
            
            // Animar entrada
            requestAnimationFrame(() => {
                modal.style.opacity = '1';
                modalContent.style.transform = 'scale(1)';
                modalContent.style.opacity = '1';
            });
            
            // Focus automático en el primer campo después de la animación
            setTimeout(() => {
                document.getElementById('modal_nombre_completo').focus();
            }, 300);
            
            console.log('✨ Modal de docente externo abierto con animación');
        }

        // Cerrar modal con animación suave
        function cerrarModalDocenteExterno() {
            const modal = document.getElementById('modalDocenteExterno');
            const modalContent = document.getElementById('modalContent');
            
            // Animar salida
            modal.style.opacity = '0';
            modalContent.style.transform = 'scale(0.95)';
            modalContent.style.opacity = '0';
            
            // Ocultar después de la animación
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = ''; // Restaurar scroll del body
                console.log('✨ Modal cerrado con animación');
            }, 300);
        }

        // Cerrar modal al hacer clic en el overlay
        document.getElementById('modalDocenteExterno')?.addEventListener('click', function(e) {
            // Solo cerrar si se hace clic directamente en el overlay, no en el contenido
            if (e.target === this) {
                cerrarModalDocenteExterno();
            }
        });
        
        // Cerrar modal con tecla ESC
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('modalDocenteExterno');
            if (e.key === 'Escape' && modal.style.display === 'flex') {
                cerrarModalDocenteExterno();
            }
        });

        // Guardar docente externo con feedback visual mejorado
        async function guardarDocenteExterno() {
            const nombreCompleto = document.getElementById('modal_nombre_completo').value.trim();
            const especialidad = document.getElementById('modal_especialidad').value.trim();
            const telefono = document.getElementById('modal_telefono').value.trim();
            const email = document.getElementById('modal_email').value.trim();
            const observaciones = document.getElementById('modal_observaciones').value.trim();
            const btnGuardar = document.getElementById('btnGuardarDocente');
            const btnLoading = document.getElementById('btnGuardarLoading');

            // Validar nombre completo (obligatorio)
            if (!nombreCompleto) {
                // Animación de shake en el campo
                const input = document.getElementById('modal_nombre_completo');
                input.classList.add('animate-shake', 'border-red-500');
                input.focus();
                
                // Remover animación después de 500ms
                setTimeout(() => {
                    input.classList.remove('animate-shake');
                }, 500);
                
                // Mostrar alerta con estilo
                mostrarAlerta('❌ El nombre completo es obligatorio', 'error');
                return;
            }

            console.log('💾 Guardando docente externo:', { nombreCompleto, especialidad, telefono, email });

            // Deshabilitar botón y mostrar loading
            btnGuardar.disabled = true;
            btnGuardar.style.opacity = '0.7';
            btnGuardar.querySelector('span').textContent = 'Guardando...';
            btnGuardar.querySelector('svg:not(#btnGuardarLoading)').classList.add('hidden');
            btnLoading.classList.remove('hidden');

            try {
                // Guardar en backend
                const response = await fetch('{{ route("suplencias.store-docente-externo") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        nombre_completo: nombreCompleto,
                        especialidad: especialidad,
                        telefono: telefono,
                        email: email,
                        observaciones: observaciones
                    })
                });

                const data = await response.json();

                if (data.success) {
                    console.log('✅ Docente externo guardado:', data);

                    // Agregar al select con animación
                    const supleSelect = document.getElementById('id_docente_suplente');
                    const option = document.createElement('option');
                    option.value = 'ext_' + data.docente.id_docente_externo;
                    option.textContent = `${data.docente.nombre_completo}${data.docente.especialidad ? ' - ' + data.docente.especialidad : ''} ✨`;
                    option.selected = true;
                    option.className = 'animate-fade-in';
                    
                    // Buscar o crear optgroup de externos
                    let groupExternos = supleSelect.querySelector('optgroup[label*="Externos"]');
                    if (!groupExternos) {
                        groupExternos = document.createElement('optgroup');
                        groupExternos.label = '📋 Docentes Externos Registrados';
                        if (supleSelect.children.length > 1) {
                            supleSelect.insertBefore(groupExternos, supleSelect.children[1]);
                        } else {
                            supleSelect.appendChild(groupExternos);
                        }
                    }
                    groupExternos.appendChild(option);

                    // Mostrar mensaje de éxito con confetti
                    const mensajeExito = document.getElementById('mensaje-exito-modal');
                    mensajeExito.classList.remove('hidden');
                    
                    // Restaurar botón con efecto de éxito
                    btnGuardar.querySelector('span').textContent = '¡Guardado! ✓';
                    btnGuardar.classList.add('bg-green-600');
                    btnLoading.classList.add('hidden');
                    btnGuardar.querySelector('svg:not(#btnGuardarLoading)').classList.remove('hidden');
                    
                    // Cerrar modal después de 1.8 segundos con animación
                    setTimeout(() => {
                        cerrarModalDocenteExterno();
                        
                        // Restaurar botón al estado original después de cerrar
                        setTimeout(() => {
                            btnGuardar.disabled = false;
                            btnGuardar.style.opacity = '1';
                            btnGuardar.querySelector('span').textContent = 'Agregar Docente';
                            btnGuardar.classList.remove('bg-green-600');
                        }, 300);
                    }, 1800);

                } else {
                    mostrarAlerta('❌ Error al guardar: ' + (data.message || 'Error desconocido'), 'error');
                    restaurarBoton();
                }
            } catch (error) {
                console.error('❌ Error:', error);
                mostrarAlerta('❌ Error al guardar el docente externo.\n\n' + error.message, 'error');
                restaurarBoton();
            }

            function restaurarBoton() {
                btnGuardar.disabled = false;
                btnGuardar.style.opacity = '1';
                btnGuardar.querySelector('span').textContent = 'Agregar Docente';
                btnGuardar.querySelector('svg:not(#btnGuardarLoading)').classList.remove('hidden');
                btnLoading.classList.add('hidden');
            }
        }

        // Función para mostrar alertas con estilo
        function mostrarAlerta(mensaje, tipo = 'info') {
            const alertas = {
                'error': { bg: 'bg-red-50 dark:bg-red-900/20', border: 'border-red-500', text: 'text-red-700 dark:text-red-300' },
                'success': { bg: 'bg-green-50 dark:bg-green-900/20', border: 'border-green-500', text: 'text-green-700 dark:text-green-300' },
                'info': { bg: 'bg-blue-50 dark:bg-blue-900/20', border: 'border-blue-500', text: 'text-blue-700 dark:text-blue-300' }
            };
            
            const estilo = alertas[tipo] || alertas['info'];
            
            // Crear elemento de alerta
            const alerta = document.createElement('div');
            alerta.className = `fixed top-4 right-4 ${estilo.bg} ${estilo.text} px-6 py-4 rounded-lg shadow-2xl border-l-4 ${estilo.border} z-[60] animate-slide-in-right max-w-md`;
            alerta.innerHTML = `
                <div class="flex items-start gap-3">
                    <div class="flex-1">
                        <p class="font-semibold">${mensaje}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-current hover:opacity-70">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(alerta);
            
            // Auto-remover después de 4 segundos
            setTimeout(() => {
                alerta.style.opacity = '0';
                alerta.style.transform = 'translateX(100%)';
                setTimeout(() => alerta.remove(), 300);
            }, 4000);
        }

        // Cargar horarios del docente ausente
        async function cargarHorarios() {
            const docenteId = document.getElementById('id_docente_ausente').value;
            const horarioSelect = document.getElementById('id_horario');
            const fechaClase = document.getElementById('fecha_clase').value;
            
            console.log('📋 cargarHorarios() - Docente ID:', docenteId);
            
            if (!docenteId || docenteId === '') {
                console.log('⚠ No hay docente seleccionado');
                horarioSelect.innerHTML = '<option value="">-- Seleccione un horario --</option>';
                horarioSelect.disabled = true;
                return;
            }

            // Mostrar loading
            horarioSelect.disabled = false;
            horarioSelect.innerHTML = '<option value="">⏳ Cargando horarios...</option>';
            console.log('⏳ Solicitando horarios del docente...');

            try {
                const url = `{{ url('api/horarios/docente') }}/${docenteId}`;
                console.log('🌐 URL:', url);
                
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                console.log('📥 Response status:', response.status);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('❌ Error response:', errorText);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log('✓ Horarios recibidos:', data);

                horarioSelect.innerHTML = '<option value="">-- Seleccione un horario --</option>';
                
                if (data.success && data.horarios && data.horarios.length > 0) {
                    const dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                    
                    data.horarios.forEach(horario => {
                        const option = document.createElement('option');
                        option.value = horario.id_horario;
                        option.textContent = `${horario.materia || 'N/A'} - ${horario.grupo || 'N/A'} - ${horario.bloque || 'N/A'} (${dias[horario.dia_semana] || 'N/A'})`;
                        horarioSelect.appendChild(option);
                    });
                    
                    console.log(`✅ ${data.horarios.length} horarios cargados exitosamente`);
                } else {
                    horarioSelect.innerHTML = '<option value="">⚠️ Este docente no tiene horarios asignados</option>';
                    horarioSelect.disabled = true;
                    console.warn('⚠ No hay horarios disponibles para este docente');
                }

                // Si hay fecha seleccionada y un horario, buscar docentes disponibles
                if (fechaClase && horarioSelect.value && horarioSelect.value !== '') {
                    console.log('🔍 Buscando docentes disponibles automáticamente...');
                    buscarDocentesDisponibles();
                }
            } catch (error) {
                console.error('❌ Error al cargar horarios:', error);
                horarioSelect.innerHTML = '<option value="">❌ Error al cargar horarios</option>';
                horarioSelect.disabled = true;
                
                // Mostrar alert al usuario
                alert('❌ Error al cargar los horarios del docente.\n\nDetalles técnicos: ' + error.message + '\n\nPor favor, verifica:\n1. Tu conexión a internet\n2. Que el docente tenga horarios asignados\n3. Recarga la página e intenta nuevamente');
            }
        }

        // Buscar docentes disponibles
        async function buscarDocentesDisponibles() {
            const fechaClase = document.getElementById('fecha_clase').value;
            const idHorario = document.getElementById('id_horario').value;
            const idDocenteAusente = document.getElementById('id_docente_ausente').value;
            
            console.log('👥 buscarDocentesDisponibles()', {
                fechaClase,
                idHorario,
                idDocenteAusente
            });
            
            const supleSelect = document.getElementById('id_docente_suplente');
            const infoDiv = document.getElementById('disponibles-info');
            const docentesExternosGroup = document.getElementById('docentes-externos-group');
            const docentesInternosGroup = document.getElementById('docentes-internos-group');
            
            // Validar campos requeridos
            if (!fechaClase || !idHorario || !idDocenteAusente) {
                console.log('⚠ Faltan datos para buscar docentes disponibles');
                
                // Mostrar mensaje específico de qué falta
                let faltantes = [];
                if (!idDocenteAusente) faltantes.push('Docente Ausente');
                if (!fechaClase) faltantes.push('Fecha de la Clase');
                if (!idHorario) faltantes.push('Horario');
                
                supleSelect.innerHTML = '<option value="">-- Seleccione un docente suplente --</option>';
                supleSelect.disabled = true;
                infoDiv.textContent = `⚠️ Primero complete: ${faltantes.join(', ')}`;
                infoDiv.className = 'mt-1 text-xs text-yellow-600 dark:text-yellow-400';
                return;
            }

            const oldValue = supleSelect.value; // Preservar selección anterior

            // Mostrar loading
            supleSelect.disabled = false;
            supleSelect.innerHTML = '<option value="">⏳ Buscando docentes...</option>';
            infoDiv.textContent = 'Buscando docentes disponibles...';
            infoDiv.className = 'mt-1 text-xs text-blue-600 dark:text-blue-400';
            console.log('⏳ Buscando docentes internos y externos...');

            try {
                // Buscar docentes disponibles del sistema y externos en paralelo
                const [responseInternos, responseExternos] = await Promise.all([
                    fetch('{{ route("suplencias.docentes-disponibles") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            fecha_clase: fechaClase,
                            id_horario: idHorario,
                            id_docente_ausente: idDocenteAusente
                        })
                    }),
                    fetch('{{ route("suplencias.docentes-externos") }}', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    })
                ]);

                const dataInternos = await responseInternos.json();
                const dataExternos = await responseExternos.json();
                
                console.log('✓ Docentes internos:', dataInternos);
                console.log('✓ Docentes externos:', dataExternos);

                // Reconstruir el select
                supleSelect.innerHTML = '<option value="">-- Seleccione un docente suplente --</option>';
                
                // Agregar optgroup de externos si hay
                if (dataExternos.success && dataExternos.externos.length > 0) {
                    const groupExternos = document.createElement('optgroup');
                    groupExternos.label = `📋 Docentes Externos Registrados (${dataExternos.externos.length})`;
                    
                    dataExternos.externos.forEach(externo => {
                        const option = document.createElement('option');
                        option.value = 'ext_' + externo.id_docente_externo;
                        option.textContent = externo.nombre_completo + (externo.especialidad ? ` - ${externo.especialidad}` : '');
                        if (oldValue === option.value) option.selected = true;
                        groupExternos.appendChild(option);
                    });
                    
                    supleSelect.appendChild(groupExternos);
                }
                
                // Agregar optgroup de internos si hay
                if (dataInternos.success && dataInternos.docentes.length > 0) {
                    const groupInternos = document.createElement('optgroup');
                    groupInternos.label = `👥 Docentes del Sistema Disponibles (${dataInternos.docentes.length})`;
                    
                    dataInternos.docentes.forEach(docente => {
                        const option = document.createElement('option');
                        option.value = docente.id;
                        option.textContent = docente.name;
                        if (oldValue == docente.id) option.selected = true;
                        groupInternos.appendChild(option);
                    });
                    
                    supleSelect.appendChild(groupInternos);
                }
                
                // Actualizar mensaje informativo
                const totalExternos = dataExternos.externos?.length || 0;
                const totalInternos = dataInternos.docentes?.length || 0;
                const total = totalExternos + totalInternos;
                
                if (total > 0) {
                    infoDiv.innerHTML = `✓ ${totalInternos} del sistema + ${totalExternos} externos disponibles. <button type="button" onclick="abrirModalDocenteExterno()" class="text-blue-600 hover:text-blue-800 underline">¿Agregar nuevo?</button>`;
                    infoDiv.className = 'mt-1 text-xs text-green-600 dark:text-green-400';
                } else {
                    infoDiv.innerHTML = `⚠️ No hay docentes disponibles. <button type="button" onclick="abrirModalDocenteExterno()" class="text-blue-600 hover:text-blue-800 underline font-semibold">Agregar docente externo</button>`;
                    infoDiv.className = 'mt-1 text-xs text-yellow-600 dark:text-yellow-400';
                }
                
            } catch (error) {
                console.error('❌ Error al buscar docentes:', error);
                supleSelect.innerHTML = '<option value="">-- Seleccione un docente suplente --</option>';
                infoDiv.innerHTML = `Error al buscar docentes. <button type="button" onclick="abrirModalDocenteExterno()" class="text-blue-600 hover:text-blue-800 underline">Agregar docente externo</button>`;
                infoDiv.className = 'mt-1 text-xs text-red-600 dark:text-red-400';
            }
        }
    </script>
</x-app-layout>
