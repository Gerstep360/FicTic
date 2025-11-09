<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-200 leading-tight">
            CU-20. Registrar Asistencia por QR
        </h2>
    </x-slot>

    <style>
        @keyframes scan {
            0% { top: 0; }
            50% { top: 100%; }
            100% { top: 0; }
        }
        .animate-scan {
            animation: scan 2s ease-in-out infinite;
        }
        #qr-video {
            transform: scaleX(-1); /* Efecto espejo para mejor UX */
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Panel Principal: Escáner -->
                <div class="lg:col-span-2">
                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 overflow-hidden">
                        
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-blue-900/50 to-indigo-900/50 border-b border-blue-700 px-6 py-4">
                            <h3 class="text-lg font-semibold text-blue-200">Escáner de QR</h3>
                            <p class="text-sm text-blue-300/80 mt-1">
                                Escanea el código QR del docente para registrar su asistencia
                            </p>
                        </div>

                        <!-- Área del escáner -->
                        <div class="p-6">
                            <div class="relative bg-slate-900 rounded-lg border-2 border-slate-700 overflow-hidden mx-auto" style="max-width: 640px;">
                                
                                <!-- Video de la cámara -->
                                <video id="qr-video" class="w-full h-auto block" autoplay playsinline></video>
                                
                                <!-- Canvas oculto para procesamiento -->
                                <canvas id="qr-canvas" class="hidden"></canvas>
                                
                                <!-- Overlay de guía -->
                                <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                                    <!-- Marco de escaneo -->
                                    <div class="relative w-64 h-64 border-4 border-blue-500 rounded-xl shadow-lg">
                                        <!-- Esquinas animadas -->
                                        <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-green-400 -mt-1 -ml-1 rounded-tl-lg animate-pulse"></div>
                                        <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-green-400 -mt-1 -mr-1 rounded-tr-lg animate-pulse"></div>
                                        <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-green-400 -mb-1 -ml-1 rounded-bl-lg animate-pulse"></div>
                                        <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-green-400 -mb-1 -mr-1 rounded-br-lg animate-pulse"></div>
                                        
                                        <!-- Línea de escaneo animada -->
                                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-green-400 to-transparent animate-scan"></div>
                                    </div>
                                </div>

                                <!-- Estado del escáner -->
                                <div id="scanner-status" class="absolute top-4 left-4 right-4 flex items-center justify-center z-10">
                                    <span class="px-4 py-2 rounded-lg bg-slate-900/90 text-white text-sm font-medium backdrop-blur border border-slate-600">
                                        📷 Cámara inactiva
                                    </span>
                                </div>
                            </div>

                            <!-- Controles -->
                            <div class="mt-4 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <button id="btn-start-camera" 
                                            onclick="startCamera()"
                                            class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white rounded-lg transition">
                                        📷 Activar Cámara
                                    </button>
                                    <button id="btn-stop-camera" 
                                            onclick="stopCamera()"
                                            class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white rounded-lg transition"
                                            style="display: none;">
                                        ⏹️ Detener
                                    </button>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <label class="text-sm text-slate-400">Tipo:</label>
                                    <select id="tipo-marca" class="rounded-lg bg-slate-700 border-slate-600 text-slate-200 text-sm">
                                        <option value="ENTRADA">Entrada</option>
                                        <option value="SALIDA">Salida</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Área de resultados -->
                    <div id="scan-result" class="mt-6" style="display: none;">
                        <!-- Se llenará dinámicamente con JavaScript -->
                    </div>
                </div>

                <!-- Panel Lateral: Información y Historial reciente -->
                <div class="space-y-6">
                    
                    <!-- Instrucciones -->
                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6">
                        <h4 class="text-sm font-semibold text-slate-300 uppercase tracking-wider mb-4">
                            📋 Instrucciones
                        </h4>
                        <ol class="space-y-2 text-sm text-slate-400">
                            <li class="flex gap-2">
                                <span class="text-blue-400 font-bold">1.</span>
                                <span>Haz clic en "Activar Cámara"</span>
                            </li>
                            <li class="flex gap-2">
                                <span class="text-blue-400 font-bold">2.</span>
                                <span>Permite el acceso a la cámara si el navegador lo solicita</span>
                            </li>
                            <li class="flex gap-2">
                                <span class="text-blue-400 font-bold">3.</span>
                                <span>Apunta la cámara al código QR del docente</span>
                            </li>
                            <li class="flex gap-2">
                                <span class="text-blue-400 font-bold">4.</span>
                                <span>El sistema registrará automáticamente la asistencia</span>
                            </li>
                            <li class="flex gap-2">
                                <span class="text-blue-400 font-bold">5.</span>
                                <span>Verifica el mensaje de confirmación</span>
                            </li>
                        </ol>
                    </div>

                    <!-- Estadísticas del día -->
                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6">
                        <h4 class="text-sm font-semibold text-slate-300 uppercase tracking-wider mb-4">
                            📊 Hoy
                        </h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-400">Asistencias registradas:</span>
                                <span id="stats-total" class="text-lg font-bold text-blue-400">0</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-400">Última hace:</span>
                                <span id="stats-last" class="text-sm text-slate-300">-</span>
                            </div>
                        </div>
                        <a href="{{ route('asistencia-qr.historial') }}" 
                           class="mt-4 block text-center px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-lg transition text-sm">
                            Ver Historial Completo
                        </a>
                    </div>

                    <!-- Últimos registros -->
                    <div class="bg-slate-800 rounded-lg shadow-xl border border-slate-700 p-6">
                        <h4 class="text-sm font-semibold text-slate-300 uppercase tracking-wider mb-4">
                            🕐 Últimos Registros
                        </h4>
                        <div id="recent-scans" class="space-y-2">
                            <p class="text-sm text-slate-500 text-center py-4">No hay registros recientes</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Incluir librería jsQR -->
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

    <script>
        let stream = null;
        let scanning = false;
        let recentScans = [];
        let lastScannedToken = null;
        let lastScanTime = 0;
        const video = document.getElementById('qr-video');
        const canvas = document.getElementById('qr-canvas');
        const context = canvas.getContext('2d', { willReadFrequently: true });

        async function startCamera() {
            try {
                // Solicitar cámara trasera con alta resolución
                const constraints = {
                    video: {
                        facingMode: { ideal: 'environment' },
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    }
                };
                
                stream = await navigator.mediaDevices.getUserMedia(constraints);
                video.srcObject = stream;
                
                // Esperar a que el video esté listo
                video.onloadedmetadata = () => {
                    video.play();
                    
                    // Configurar canvas con las dimensiones del video
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    
                    scanning = true;
                    document.getElementById('btn-start-camera').style.display = 'none';
                    document.getElementById('btn-stop-camera').style.display = 'block';
                    updateStatus('🎥 Escaneando... Centre el código QR', 'bg-blue-600/90');
                    
                    requestAnimationFrame(scanFrame);
                };
            } catch (error) {
                console.error('Error al acceder a la cámara:', error);
                updateStatus('❌ Error: ' + error.message, 'bg-red-600/90');
                alert('No se pudo acceder a la cámara. Verifica los permisos del navegador.');
            }
        }

        function stopCamera() {
            scanning = false;
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            video.srcObject = null;
            
            document.getElementById('btn-start-camera').style.display = 'block';
            document.getElementById('btn-stop-camera').style.display = 'none';
            updateStatus('⏹️ Cámara detenida', 'bg-slate-600/90');
        }

        function updateStatus(message, bgClass = 'bg-blue-600/90') {
            const statusDiv = document.getElementById('scanner-status');
            statusDiv.innerHTML = `<span class="px-4 py-2 rounded-lg ${bgClass} text-white text-sm font-medium backdrop-blur border border-slate-600">${message}</span>`;
        }

        function scanFrame() {
            if (!scanning) return;

            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                // Dibujar el frame actual en el canvas
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                // Obtener los datos de la imagen
                const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                
                // Intentar detectar código QR
                const code = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: 'attemptBoth',
                });

                if (code && code.data) {
                    const currentTime = Date.now();
                    const token = code.data;
                    
                    // Evitar escaneos duplicados (mínimo 3 segundos entre escaneos del mismo código)
                    if (lastScannedToken === token && (currentTime - lastScanTime) < 3000) {
                        requestAnimationFrame(scanFrame);
                        return;
                    }
                    
                    // Validar y extraer token
                    let tokenValue = extractToken(token);
                    
                    if (tokenValue) {
                        lastScannedToken = token;
                        lastScanTime = currentTime;
                        
                        // Feedback visual
                        updateStatus('✅ QR detectado! Procesando...', 'bg-green-600/90');
                        
                        // Pausar escaneo temporalmente
                        scanning = false;
                        processQRCode(tokenValue);
                        return;
                    } else {
                        updateStatus('⚠️ QR no válido. Intente de nuevo', 'bg-orange-600/90');
                        setTimeout(() => {
                            if (!scanning) return;
                            updateStatus('🎥 Escaneando... Centre el código QR', 'bg-blue-600/90');
                        }, 1500);
                    }
                }
            }

            requestAnimationFrame(scanFrame);
        }

        function extractToken(data) {
            // Validar que sea una URL o token válido
            try {
                // Caso 1: Es una URL completa
                if (data.includes('/asistencia/escanear-qr/') || data.includes('token=')) {
                    const urlMatch = data.match(/token=([a-f0-9]{64})/i) || 
                                   data.match(/escanear-qr\/([a-f0-9]{64})/i);
                    if (urlMatch) {
                        return urlMatch[1];
                    }
                }
                
                // Caso 2: Es el token directo (64 caracteres hexadecimales)
                if (/^[a-f0-9]{64}$/i.test(data)) {
                    return data;
                }
                
                return null;
            } catch (error) {
                console.error('Error al extraer token:', error);
                return null;
            }
        }

        async function processQRCode(token) {
            const tipoMarca = document.getElementById('tipo-marca').value;
            
            updateStatus('⏳ Procesando asistencia...', 'bg-yellow-600/90');

            try {
                const response = await fetch('{{ route("asistencia.escanear-qr") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ token, tipo_marca: tipoMarca })
                });

                const data = await response.json();

                if (data.success) {
                    showSuccess(data);
                    updateStats();
                    addToRecentScans(data.data);
                    updateStatus('✅ Registrado! Listo para siguiente QR', 'bg-green-600/90');
                } else {
                    showError(data.message, data);
                    updateStatus('❌ Error. Intente nuevamente', 'bg-red-600/90');
                }
            } catch (error) {
                console.error('Error:', error);
                showError('Error de conexión. Verifica tu red e intenta nuevamente.');
                updateStatus('❌ Error de conexión', 'bg-red-600/90');
            }

            // Reanudar escaneo después de 2 segundos
            setTimeout(() => {
                scanning = true;
                updateStatus('🎥 Escaneando... Centre el código QR', 'bg-blue-600/90');
                requestAnimationFrame(scanFrame);
            }, 2000);
        }

        function showSuccess(data) {
            const resultDiv = document.getElementById('scan-result');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = `
                <div class="bg-gradient-to-r from-green-900/50 to-emerald-900/50 border-2 border-green-500 rounded-lg p-6 animate-pulse">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 text-4xl">✅</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-green-200 mb-2">${data.message}</h3>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div><span class="text-green-300/70">Docente:</span> <span class="text-green-100 font-medium">${data.data.docente}</span></div>
                                <div><span class="text-green-300/70">Hora:</span> <span class="text-green-100 font-medium">${data.data.hora_registro}</span></div>
                                <div><span class="text-green-300/70">Materia:</span> <span class="text-green-100 font-medium">${data.data.materia}</span></div>
                                <div><span class="text-green-300/70">Grupo:</span> <span class="text-green-100 font-medium">${data.data.grupo}</span></div>
                                <div><span class="text-green-300/70">Aula:</span> <span class="text-green-100 font-medium">${data.data.aula}</span></div>
                                <div><span class="text-green-300/70">Bloque:</span> <span class="text-green-100 font-medium">${data.data.bloque}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            setTimeout(() => {
                resultDiv.style.display = 'none';
            }, 5000);
        }

        function showError(message, data = null) {
            const resultDiv = document.getElementById('scan-result');
            resultDiv.style.display = 'block';
            
            let extraInfo = '';
            if (data && data.docente) {
                extraInfo = `<p class="text-sm text-red-200 mt-2">Docente: ${data.docente}</p>`;
            }
            
            resultDiv.innerHTML = `
                <div class="bg-gradient-to-r from-red-900/50 to-orange-900/50 border-2 border-red-500 rounded-lg p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 text-4xl">❌</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-red-200">${message}</h3>
                            ${extraInfo}
                        </div>
                    </div>
                </div>
            `;
            
            setTimeout(() => {
                resultDiv.style.display = 'none';
            }, 5000);
        }

        function addToRecentScans(data) {
            recentScans.unshift(data);
            if (recentScans.length > 5) recentScans.pop();
            
            const container = document.getElementById('recent-scans');
            container.innerHTML = recentScans.map(scan => `
                <div class="bg-slate-900/50 rounded p-3 border border-slate-700">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-slate-200">${scan.docente}</span>
                        <span class="text-xs text-slate-400">${scan.hora_registro}</span>
                    </div>
                    <div class="text-xs text-slate-400">${scan.materia} - ${scan.aula}</div>
                </div>
            `).join('');
        }

        async function updateStats() {
            try {
                const response = await fetch('{{ route("asistencia-qr.historial") }}?ajax=1');
                const html = await response.text();
                // Aquí podrías parsear el HTML o mejor hacer un endpoint API
                const total = recentScans.length;
                document.getElementById('stats-total').textContent = total;
                if (total > 0) {
                    document.getElementById('stats-last').textContent = 'hace un momento';
                }
            } catch (error) {
                console.error('Error al actualizar stats:', error);
            }
        }
    </script>
</x-app-layout>
