<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Resultado de la Importación') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            {{-- Resumen de resultados --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl mb-6">
                <div class="p-6 sm:p-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
                        Resumen de la importación
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        {{-- Creados --}}
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-green-700 dark:text-green-300">
                                        {{ $reporte['creados'] }}
                                    </p>
                                    <p class="text-sm text-green-600 dark:text-green-400">
                                        Creados
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Actualizados --}}
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                                        {{ $reporte['actualizados'] }}
                                    </p>
                                    <p class="text-sm text-blue-600 dark:text-blue-400">
                                        Actualizados
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Notificados --}}
                        <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-xl p-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-purple-700 dark:text-purple-300">
                                        {{ $reporte['notificados'] }}
                                    </p>
                                    <p class="text-sm text-purple-600 dark:text-purple-400">
                                        Notificados
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Omitidos --}}
                        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-amber-700 dark:text-amber-300">
                                        {{ $reporte['omitidos'] }}
                                    </p>
                                    <p class="text-sm text-amber-600 dark:text-amber-400">
                                        Omitidos
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Errores detallados --}}
            @if(!empty($reporte['errores']))
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl mb-6">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-4">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Errores encontrados ({{ count($reporte['errores']) }})
                            </h3>
                        </div>

                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 max-h-96 overflow-y-auto">
                            <ul class="space-y-2">
                                @foreach($reporte['errores'] as $error)
                                    <li class="flex items-start gap-2 text-sm text-red-700 dark:text-red-300">
                                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $error }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Mensaje de éxito si todo salió bien --}}
            @if(empty($reporte['errores']) && ($reporte['creados'] > 0 || $reporte['actualizados'] > 0))
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-2xl p-6">
                    <div class="flex items-start gap-4">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-green-900 dark:text-green-100 mb-1">
                                ¡Importación completada exitosamente!
                            </h3>
                            <p class="text-sm text-green-700 dark:text-green-300">
                                Todos los usuarios se procesaron correctamente. 
                                @if($reporte['notificados'] > 0)
                                    Se enviaron {{ $reporte['notificados'] }} correos con las credenciales generadas.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Acciones --}}
            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('usuarios.import.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl font-semibold text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Importar más usuarios
                </a>

                <a href="{{ url('/dashboard') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-sky-600 hover:bg-sky-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Ir al dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
