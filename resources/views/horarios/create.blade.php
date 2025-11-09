<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('horarios.index') }}" class="text-slate-400 hover:text-slate-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                {{ __('Asignar Horario Manualmente') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6" x-data="{ 
        validando: false, 
        conflictos: [],
        async validar() {
            const form = this.$el.querySelector('form');
            const data = new FormData(form);
            
            this.validando = true;
            this.conflictos = [];
            
            try {
                const response = await fetch('{{ route('horarios.validar') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        dia_semana: data.get('dia_semana'),
                        id_bloque: data.get('id_bloque'),
                        id_aula: data.get('id_aula'),
                        id_docente: data.get('id_docente')
                    })
                });
                
                const result = await response.json();
                this.conflictos = result.conflictos || [];
            } catch (error) {
                console.error('Error validando:', error);
            } finally {
                this.validando = false;
            }
        }
    }">
        @if($errors->any())
            <div class="card p-4 bg-red-500/10 border-red-500/20">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-red-400 font-medium mb-2">Hay errores en el formulario:</h3>
                        <ul class="list-disc list-inside text-sm text-red-300 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Alerta de conflictos en tiempo real --}}
        <div x-show="conflictos.length > 0" class="card p-4 bg-amber-500/10 border-amber-500/20">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1">
                    <h3 class="text-amber-400 font-medium mb-2">⚠️ Conflictos detectados:</h3>
                    <ul class="list-disc list-inside text-sm text-amber-300 space-y-1">
                        <template x-for="conflicto in conflictos" :key="conflicto">
                            <li x-text="conflicto"></li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('horarios.store') }}" class="space-y-6">
            @csrf

            {{-- Selección de grupo --}}
            <div class="card p-6 space-y-4">
                <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Seleccionar Grupo
                </h3>

                <div>
                    <label for="id_grupo" class="block text-sm font-medium text-slate-300 mb-1">Grupo/Materia *</label>
                    <select id="id_grupo" name="id_grupo" required class="input w-full">
                        <option value="">Seleccione un grupo</option>
                        @foreach($grupos as $grupo)
                            <option value="{{ $grupo->id_grupo }}" {{ old('id_grupo') == $grupo->id_grupo ? 'selected' : '' }}>
                                {{ $grupo->materia->nombre }} - Grupo {{ $grupo->nombre_grupo }} 
                                ({{ $grupo->turno }}, {{ $grupo->modalidad }})
                                @if($grupo->docente)
                                    - Doc: {{ $grupo->docente->name }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-400">Selecciona el grupo al que asignarás horario</p>
                </div>
            </div>

            {{-- Asignación de horario --}}
            <div class="card p-6 space-y-4">
                <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Horario
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="dia_semana" class="block text-sm font-medium text-slate-300 mb-1">Día de la Semana *</label>
                        <select id="dia_semana" name="dia_semana" required class="input w-full" @change="validar()">
                            <option value="">Seleccione...</option>
                            <option value="1" {{ old('dia_semana') == 1 ? 'selected' : '' }}>Lunes</option>
                            <option value="2" {{ old('dia_semana') == 2 ? 'selected' : '' }}>Martes</option>
                            <option value="3" {{ old('dia_semana') == 3 ? 'selected' : '' }}>Miércoles</option>
                            <option value="4" {{ old('dia_semana') == 4 ? 'selected' : '' }}>Jueves</option>
                            <option value="5" {{ old('dia_semana') == 5 ? 'selected' : '' }}>Viernes</option>
                            <option value="6" {{ old('dia_semana') == 6 ? 'selected' : '' }}>Sábado</option>
                        </select>
                    </div>

                    <div>
                        <label for="id_bloque" class="block text-sm font-medium text-slate-300 mb-1">Bloque Horario *</label>
                        <select id="id_bloque" name="id_bloque" required class="input w-full" @change="validar()">
                            <option value="">Seleccione...</option>
                            @foreach($bloques as $bloque)
                                <option value="{{ $bloque->id_bloque }}" {{ old('id_bloque') == $bloque->id_bloque ? 'selected' : '' }}>
                                    {{ $bloque->etiqueta ?? "Bloque {$bloque->id_bloque}" }} 
                                    ({{ \Carbon\Carbon::parse($bloque->hora_inicio)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($bloque->hora_fin)->format('H:i') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Asignación de recursos --}}
            <div class="card p-6 space-y-4">
                <h3 class="text-lg font-semibold text-slate-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Aula y Docente
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="id_aula" class="block text-sm font-medium text-slate-300 mb-1">Aula *</label>
                        <select id="id_aula" name="id_aula" required class="input w-full" @change="validar()">
                            <option value="">Seleccione...</option>
                            @foreach($aulas as $aula)
                                <option value="{{ $aula->id_aula }}" {{ old('id_aula') == $aula->id_aula ? 'selected' : '' }}>
                                    {{ $aula->codigo }} - {{ $aula->tipo }} 
                                    @if($aula->capacidad)
                                        (Cap: {{ $aula->capacidad }})
                                    @endif
                                    @if($aula->edificio)
                                        - {{ $aula->edificio }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="id_docente" class="block text-sm font-medium text-slate-300 mb-1">Docente *</label>
                        <select id="id_docente" name="id_docente" required class="input w-full" @change="validar()">
                            <option value="">Seleccione...</option>
                            @foreach($docentes as $docente)
                                <option value="{{ $docente->id }}" {{ old('id_docente') == $docente->id ? 'selected' : '' }}>
                                    {{ $docente->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-sm text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>El sistema validará automáticamente que no haya conflictos de aula o docente</span>
                </div>

                <div x-show="validando" class="flex items-center gap-2 text-sm text-blue-400">
                    <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Validando conflictos...</span>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center gap-3">
                <a href="{{ route('horarios.index') }}" class="btn-secondary text-center">
                    Cancelar
                </a>
                <button type="submit" class="btn-primary flex-1" :disabled="validando || conflictos.length > 0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span x-show="!validando">Asignar Horario</span>
                    <span x-show="validando">Validando...</span>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
