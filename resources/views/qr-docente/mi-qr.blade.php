<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-200 leading-tight">
            Mi Código QR de Asistencia
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
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

            <!-- Bienvenida -->
            <div class="mb-6 bg-gradient-to-r from-purple-900/50 to-indigo-900/50 border border-purple-700 rounded-lg p-6">
                <h3 class="text-xl font-bold text-purple-200 mb-2">
                    👋 Bienvenido, {{ auth()->user()->name }}
                </h3>
                <p class="text-purple-300/90">
                    Este es tu código QR personal para el registro de asistencia. 
                    Puedes descargarlo en PDF para imprimir o en PNG para guardarlo en tu dispositivo móvil.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Panel principal: QR -->
                <div class="lg:col-span-2">
                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 overflow-hidden">
                        
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-700 px-6 py-4">
                            <h3 class="text-lg font-semibold text-slate-200">Tu Código QR Personal</h3>
                            <p class="text-sm text-slate-400 mt-1">Gestión: {{ $token->gestion->nombre }}</p>
                        </div>

                        <!-- QR con overlay institucional -->
                        <div class="p-8 bg-gradient-to-br from-slate-900 to-slate-800">
                            <div class="relative bg-white rounded-xl p-8 mx-auto max-w-md">
                                
                                <!-- Logo watermark -->
                                <div class="absolute top-2 right-2 opacity-20">
                                    <img src="{{ asset('brand/logo.svg') }}" 
                                         alt="Logo FicTic" 
                                         class="w-12 h-12"
                                         onerror="this.style.display='none'">
                                </div>

                                <!-- QR Code -->
                                <div class="flex items-center justify-center">
                                    {!! $qrCode !!}
                                </div>

                                <!-- Info institucional -->
                                <div class="mt-4 pt-3 border-t border-slate-200">
                                    <p class="text-xs text-center text-slate-600 font-semibold">
                                        FACULTAD DE CIENCIAS Y TECNOLOGÍA
                                    </p>
                                    <p class="text-xs text-center text-slate-500 mt-1">
                                        Sistema de Control de Asistencia
                                    </p>
                                </div>
                            </div>

                            <!-- Estado -->
                            <div class="mt-4 text-center">
                                @if($token->activo && $token->esta_vigente)
                                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-900/50 border border-green-700 text-green-300">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Tu código está activo y listo para usar
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-orange-900/50 border border-orange-700 text-orange-300">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Tu código requiere atención (contacta al coordinador)
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Botones de descarga -->
                        <div class="px-6 py-4 bg-slate-900/50 border-t border-slate-700">
                            <div class="grid grid-cols-2 gap-3">
                                <a href="{{ route('qr-docente.descargar-mi-qr', ['formato' => 'pdf']) }}"
                                   class="flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    Descargar PDF
                                </a>
                                <a href="{{ route('qr-docente.descargar-mi-qr', ['formato' => 'png']) }}"
                                   class="flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Descargar PNG
                                </a>
                            </div>
                            <p class="text-xs text-slate-400 text-center mt-3">
                                💡 <strong>Tip:</strong> Descarga en PDF para imprimir o en PNG para usar en tu dispositivo
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Panel lateral: Info y ayuda -->
                <div class="space-y-6">
                    
                    <!-- Información -->
                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6">
                        <h4 class="text-sm font-semibold text-slate-300 uppercase tracking-wider mb-4">
                            Información
                        </h4>
                        <div class="space-y-3 text-sm">
                            <div>
                                <label class="text-xs text-slate-400">Gestión Actual</label>
                                <p class="text-slate-200 font-medium">{{ $gestionActual->nombre }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-slate-400">Periodo</label>
                                <p class="text-slate-200">
                                    {{ $gestionActual->fecha_inicio->format('d/m/Y') }} - 
                                    {{ $gestionActual->fecha_fin->format('d/m/Y') }}
                                </p>
                            </div>
                            <div>
                                <label class="text-xs text-slate-400">Generado el</label>
                                <p class="text-slate-200">{{ $token->fecha_generacion->format('d/m/Y H:i') }}</p>
                            </div>
                            @if($token->veces_usado > 0)
                                <div>
                                    <label class="text-xs text-slate-400">Veces escaneado</label>
                                    <p class="text-slate-200 font-mono">{{ $token->veces_usado }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Instrucciones -->
                    <div class="bg-gradient-to-br from-blue-900/30 to-indigo-900/30 border border-blue-700 rounded-lg p-6">
                        <h4 class="text-sm font-semibold text-blue-200 uppercase tracking-wider mb-4">
                            📋 Cómo Usar tu QR
                        </h4>
                        <ol class="space-y-3 text-sm text-blue-200/90">
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-500/20 flex items-center justify-center text-xs font-bold">1</span>
                                <span>Descarga tu código QR en el formato que prefieras</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-500/20 flex items-center justify-center text-xs font-bold">2</span>
                                <span>Imprime el PDF o guarda el PNG en tu teléfono</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-500/20 flex items-center justify-center text-xs font-bold">3</span>
                                <span>Presenta tu código al bedel al llegar a clase</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-500/20 flex items-center justify-center text-xs font-bold">4</span>
                                <span>El sistema registrará automáticamente tu asistencia</span>
                            </li>
                        </ol>
                    </div>

                    <!-- Ayuda -->
                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700">
                        <button onclick="toggleAccordion('faq1')" 
                                class="w-full px-6 py-4 text-left flex items-center justify-between text-slate-300 hover:bg-slate-750 transition">
                            <span class="font-medium">¿Qué pasa si pierdo mi QR?</span>
                            <svg class="w-5 h-5 transform transition-transform" id="faq1-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div id="faq1" class="hidden px-6 pb-4 text-sm text-slate-400">
                            No hay problema. Puedes volver a esta página cuando quieras y descargar tu código QR nuevamente. 
                            El código es el mismo y seguirá funcionando.
                        </div>
                    </div>

                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700">
                        <button onclick="toggleAccordion('faq2')" 
                                class="w-full px-6 py-4 text-left flex items-center justify-between text-slate-300 hover:bg-slate-750 transition">
                            <span class="font-medium">¿Puedo compartir mi QR?</span>
                            <svg class="w-5 h-5 transform transition-transform" id="faq2-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div id="faq2" class="hidden px-6 pb-4 text-sm text-slate-400">
                            <strong class="text-red-400">No.</strong> Este código es personal e intransferible. 
                            Está vinculado a tu identidad y solo debe ser usado por ti. Compartirlo constituye una falta grave.
                        </div>
                    </div>

                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700">
                        <button onclick="toggleAccordion('faq3')" 
                                class="w-full px-6 py-4 text-left flex items-center justify-between text-slate-300 hover:bg-slate-750 transition">
                            <span class="font-medium">¿El QR cambia cada día?</span>
                            <svg class="w-5 h-5 transform transition-transform" id="faq3-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div id="faq3" class="hidden px-6 pb-4 text-sm text-slate-400">
                            No. Tu código QR es válido para toda la gestión académica actual. 
                            Solo necesitas descargarlo una vez y lo usarás durante todo el semestre.
                        </div>
                    </div>

                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700">
                        <button onclick="toggleAccordion('faq4')" 
                                class="w-full px-6 py-4 text-left flex items-center justify-between text-slate-300 hover:bg-slate-750 transition">
                            <span class="font-medium">¿Necesito internet para usarlo?</span>
                            <svg class="w-5 h-5 transform transition-transform" id="faq4-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div id="faq4" class="hidden px-6 pb-4 text-sm text-slate-400">
                            No necesitas internet en tu dispositivo. Solo necesitas mostrar el código (impreso o en pantalla) 
                            para que el bedel lo escanee. El sistema del bedel es quien necesita conexión.
                        </div>
                    </div>

                    <!-- Contacto -->
                    <div class="bg-gradient-to-br from-purple-900/30 to-pink-900/30 border border-purple-700 rounded-lg p-6">
                        <h4 class="text-sm font-semibold text-purple-200 uppercase tracking-wider mb-2">
                            ¿Necesitas ayuda?
                        </h4>
                        <p class="text-sm text-purple-300/90">
                            Si tienes problemas con tu código QR, contacta a tu coordinador de carrera o 
                            escribe a <a href="mailto:soporte@fictic.edu" class="text-purple-400 hover:text-purple-300 underline">soporte@fictic.edu</a>
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <script>
        function toggleAccordion(id) {
            const content = document.getElementById(id);
            const icon = document.getElementById(id + '-icon');
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }
    </script>
</x-app-layout>
