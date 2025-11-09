<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Solicitar Justificación') }}
            </h2>
            <a href="{{ route('justificaciones.mis-justificaciones') }}" class="btn-ghost inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Mis Justificaciones
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            @if($errors->any())
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 dark:border-red-600 text-red-700 dark:text-red-300 p-4 rounded" role="alert">
                    <p class="font-bold">Errores en el formulario</p>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold mb-6 text-gray-900 dark:text-gray-100">Complete el formulario de justificación</h3>
                    
                    <form action="{{ route('justificaciones.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        {{-- Fecha de Clase --}}
                        <div>
                            <label for="fecha_clase" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Fecha de la Clase <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="fecha_clase" 
                                   id="fecha_clase" 
                                   value="{{ old('fecha_clase') }}"
                                   max="{{ date('Y-m-d') }}"
                                   required 
                                   class="input">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Seleccione la fecha de la clase a justificar</p>
                        </div>

                        {{-- Tipo de Justificación --}}
                        <div>
                            <label for="tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tipo de Justificación <span class="text-red-500">*</span>
                            </label>
                            <select name="tipo" 
                                    id="tipo" 
                                    required 
                                    class="input">
                                <option value="">-- Seleccione --</option>
                                <option value="ENFERMEDAD" {{ old('tipo') == 'ENFERMEDAD' ? 'selected' : '' }}>Enfermedad</option>
                                <option value="EMERGENCIA" {{ old('tipo') == 'EMERGENCIA' ? 'selected' : '' }}>Emergencia Familiar</option>
                                <option value="TRAMITE" {{ old('tipo') == 'TRAMITE' ? 'selected' : '' }}>Trámite Administrativo</option>
                                <option value="OTRO" {{ old('tipo') == 'OTRO' ? 'selected' : '' }}>Otro</option>
                            </select>
                        </div>

                        {{-- Motivo --}}
                        <div>
                            <label for="motivo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Motivo de la Justificación <span class="text-red-500">*</span>
                            </label>
                            <textarea name="motivo" 
                                      id="motivo" 
                                      rows="5" 
                                      required 
                                      minlength="10"
                                      maxlength="1000"
                                      class="input"
                                      placeholder="Explique detalladamente el motivo de su ausencia (mínimo 10 caracteres)...">{{ old('motivo') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mínimo 10 caracteres, máximo 1000</p>
                        </div>

                        {{-- Documento Adjunto --}}
                        <div>
                            <label for="documento_adjunto" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Documento de Respaldo (opcional)
                            </label>
                            <input type="file" 
                                   name="documento_adjunto" 
                                   id="documento_adjunto" 
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   class="input file:mr-4 file:py-3 file:px-4 file:rounded-l-xl file:border-0 file:text-sm file:font-semibold">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Formatos permitidos: PDF, JPG, PNG (máx. 5MB). Se recomienda adjuntar certificado médico, constancia, etc.</p>
                        </div>

                        {{-- Información Adicional --}}
                        <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 dark:border-blue-500 p-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        <strong>Importante:</strong> Su solicitud será revisada por el Coordinador o Director de Carrera. 
                                        Procure ser claro y preciso en su justificación. Si adjunta documentos de respaldo, aumentará las posibilidades de aprobación.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Botones --}}
                        <div class="flex gap-4 pt-4">
                            <button type="submit" class="btn-primary">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                Enviar Solicitud
                            </button>
                            <a href="{{ route('justificaciones.mis-justificaciones') }}" class="btn-ghost">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
