<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Importar Usuarios') }}
            </h2>
                <a href="{{ route('usuarios.import.plantilla') }}" 
                    class="btn-ghost inline-flex items-center gap-2 px-4 py-2 font-semibold text-xs uppercase tracking-widest">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Descargar plantilla Excel (.xlsx)
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Instrucciones --}}
            <div class="bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-800 rounded-2xl p-6 mb-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-sky-900 dark:text-sky-100 mb-2">
                            Instrucciones para importar usuarios
                        </h3>
                        <ul class="space-y-2 text-sm text-sky-800 dark:text-sky-200">
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-sky-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span><strong>Descarga la plantilla xlsx</strong> con las cabeceras correctas: ID, NOMBRE, CORREO, CONTRASEÑA</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-sky-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Si dejas la <strong>CONTRASEÑA vacía</strong>, se generará automáticamente y se enviará por email</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-sky-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Si incluyes un <strong>ID existente</strong>, se actualizará ese usuario</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-sky-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>El archivo debe estar en formato <strong>UTF-8</strong> y usar comas como delimitador</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Formulario de subida --}}
            <div class="card overflow-hidden">
                <div class="p-6 sm:p-8">
                    <form method="POST" action="{{ route('usuarios.import.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        {{-- Gestión (opcional) --}}
                        @if(request('id_gestion'))
                            <input type="hidden" name="id_gestion" value="{{ request('id_gestion') }}">
                        @endif

                        {{-- Archivo CSV --}}
                        <div>
                            <label for="archivo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Archivo CSV
                            </label>
                            
                            <div class="relative">
                                <input 
                                    type="file" 
                                    id="archivo" 
                                    name="archivo" 
                                    accept=".xlsx,.xls,.csv,.txt"
                                    required
                     class="input file:mr-4 file:py-3 file:px-4 file:rounded-l-xl file:border-0 file:text-sm file:font-semibold"
                                    onchange="mostrarNombreArchivo(this)"
                                >
                            </div>

                            @error('archivo')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror

                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Formatos permitidos: CSV, TXT. Tamaño máximo: 20 MB
                            </p>

                            {{-- Preview del nombre del archivo --}}
                            <div id="archivo-preview" class="hidden mt-3 p-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100" id="archivo-nombre"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400" id="archivo-size"></p>
                                    </div>
                                    <button type="button" onclick="limpiarArchivo()" class="text-red-500 hover:text-red-700 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Errores generales --}}
                        @if($errors->any())
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-red-800 dark:text-red-200 mb-1">
                                            Se encontraron errores:
                                        </h4>
                                        <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300 space-y-1">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Botones --}}
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                                     <a href="{{ url()->previous() }}" 
                                         class="btn-ghost inline-flex items-center gap-2 px-4 py-2 font-semibold text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Cancelar
                            </a>

                <button type="submit" 
                    class="btn-primary inline-flex items-center gap-2 px-6 py-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                Importar usuarios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Ejemplo de formato --}}
            <div class="mt-6 card p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">
                    Ejemplo de formato:
                </h3>
                <pre class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 text-xs text-gray-800 dark:text-gray-200 overflow-x-auto"><code>ID,NOMBRE,CORREO,CONTRASEÑA
,María Pérez,maria.perez@ficct.uagrm.edu.bo,
12,Carlos Rojas,carlos.rojas@ficct.uagrm.edu.bo,
,Juan Gómez,juan.gomez@ficct.uagrm.edu.bo,Pass!2025</code></pre>
                <p class="mt-3 text-xs text-gray-600 dark:text-gray-400">
                    <strong>Nota:</strong> Si CONTRASEÑA está vacía, se generará automáticamente y se enviará por correo electrónico.
                </p>
            </div>
        </div>
    </div>

    <script>
        function mostrarNombreArchivo(input) {
            const preview = document.getElementById('archivo-preview');
            const nombre = document.getElementById('archivo-nombre');
            const size = document.getElementById('archivo-size');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                nombre.textContent = file.name;
                size.textContent = formatBytes(file.size);
                preview.classList.remove('hidden');
            } else {
                preview.classList.add('hidden');
            }
        }

        function limpiarArchivo() {
            document.getElementById('archivo').value = '';
            document.getElementById('archivo-preview').classList.add('hidden');
        }

        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }
    </script>
</x-app-layout>
