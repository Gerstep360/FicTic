<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-600/10 dark:bg-indigo-400/10 rounded-xl shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                        Código QR de Asistencia
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Gestión {{ $token->gestion->nombre }}</p>
                </div>
            </div>
            <a href="{{ route('qr-docente.index') }}" class="btn-ghost inline-flex items-center text-sm shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 mb-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center gap-3 animate-in fade-in slide-in-from-top-2">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid lg:grid-cols-5 gap-8">
                
                <div class="lg:col-span-3 order-last lg:order-first">
                    <div class="bg-slate-100 dark:bg-slate-900/50 rounded-3xl p-4 sm:p-8 md:p-12 flex items-center justify-center min-h-[500px] shadow-inner border border-slate-200/50 dark:border-slate-800/50" id="qr-target-container">
                        {!! $qrHtml !!}
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    
                    <div class="card p-6 bg-white dark:bg-gray-800 border border-indigo-100 dark:border-indigo-900/30 shadow-xl shadow-indigo-500/5">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg text-indigo-600 dark:text-indigo-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Exportar</h3>
                        </div>
                        
                        <div class="space-y-3">
                            <button onclick="downloadRealisticPNG()" id="btn-download-png" class="btn-primary w-full flex justify-center items-center gap-2 py-3 text-base shadow-lg shadow-indigo-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Descargar Imagen PNG
                            </button>
                            <a href="{{ route('qr-docente.descargar-pdf', $token->id_qr_token) }}" class="btn-ghost w-full flex justify-center items-center gap-2 py-3 text-base border-2 border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Descargar como PDF
                            </a>
                        </div>
                    </div>

                    <div class="card overflow-hidden dark:bg-gray-800">
                         <div class="p-4 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-700/50">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Información</h4>
                        </div>
                        <div class="p-5 space-y-4">
                             <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Estado</span>
                                @if($token->activo)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        <span class="w-2 h-2 mr-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                        Inactivo
                                    </span>
                                @endif
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Creado el</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $token->fecha_generacion->format('d/m/Y') }}</span>
                            </div>
                             <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Escaneos totales</span>
                                <span class="text-sm font-bold font-mono bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">{{ $token->veces_usado }}</span>
                            </div>
                        </div>
                        
                         <div class="bg-gray-50 dark:bg-gray-800/50 p-4 border-t border-gray-100 dark:border-gray-700/50 grid grid-cols-2 gap-3">
                            @if($token->activo)
                                <form action="{{ route('qr-docente.desactivar', $token->id_qr_token) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" onclick="return confirm('¿Desactivar?')" class="w-full py-2 px-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                        Desactivar
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('qr-docente.activar', $token->id_qr_token) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" onclick="return confirm('¿Activar?')" class="w-full py-2 px-3 bg-white dark:bg-gray-700 border border-green-200 dark:border-green-800 rounded-lg text-xs font-bold text-green-700 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 transition">
                                        Activar
                                    </button>
                                </form>
                            @endif
                             <form action="{{ route('qr-docente.regenerar', $token->id_qr_token) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" onclick="return confirm('¡ATENCIÓN!\nEl código actual dejará de funcionar.\n¿Generar uno nuevo?')" class="w-full py-2 px-3 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-lg text-xs font-bold text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                                    Regenerar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        async function downloadRealisticPNG() {
            const btn = document.getElementById('btn-download-png');
            const originalContent = btn.innerHTML;
            const sourceElement = document.getElementById('qr-card-export'); // ID del elemento en el template

            if (!sourceElement) {
                alert('Error: No se encontró el diseño del QR.');
                return;
            }

            try {
                // 1. Feedback visual de carga
                btn.disabled = true;
                btn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Preparando...';

                // 2. CLONACIÓN (Clave para evitar problemas de responsividad al exportar)
                // Clonamos el elemento y lo preparamos fuera de la vista del usuario.
                // Esto asegura que se renderice a tamaño completo incluso en móviles.
                const clone = sourceElement.cloneNode(true);
                clone.style.position = 'fixed';
                clone.style.left = '-9999px';
                clone.style.top = '0';
                // Forzamos un ancho ideal para la exportación (mayor calidad)
                clone.style.width = '500px'; 
                clone.style.maxWidth = 'none'; // Quitamos limites responsivos para el clone
                clone.style.transform = 'none';
                clone.style.margin = '0';
                document.body.appendChild(clone);

                // 3. Pequeña espera para asegurar que el navegador pinte el clon
                await new Promise(resolve => setTimeout(resolve, 300));

                // 4. Captura con html2canvas
                const canvas = await html2canvas(clone, {
                    scale: 2, // Calidad Retina (2x)
                    useCORS: true, // Necesario para imágenes externas si las hubiera
                    allowTaint: true,
                    backgroundColor: null, // Mantiene transparencias externas si las hay (bordes redondeados)
                    logging: false,
                     // Importante: esperar a que las imágenes internas (el QR SVG) carguen
                    onclone: (clonedDoc) => {
                        const images = clonedDoc.getElementsByTagName('img');
                        for (let img of images) {
                             // Forzar recarga si es necesario en algunos navegadores
                             img.src = img.src; 
                        }
                    }
                });

                // 5. Limpieza y Descarga
                document.body.removeChild(clone);
                
                const link = document.createElement('a');
                link.download = 'QR_{{ Str::slug($token->docente->name) }}.png';
                link.href = canvas.toDataURL('image/png', 1.0);
                link.click();

            } catch (err) {
                console.error('Error al exportar:', err);
                alert('No se pudo generar la imagen. Intente desde un ordenador si está en móvil.');
            } finally {
                btn.innerHTML = originalContent;
                btn.disabled = false;
            }
        }
    </script>
</x-app-layout>