{{-- Responsive sidebar component (secciones por CU + permisos) --}}
<div x-data="{ open: false }">
    <!-- Mobile top bar (fixed) -->
    <div class="fixed top-0 left-0 right-0 z-40 md:hidden bg-slate-950/95 backdrop-blur-lg border-b border-white/5 p-3">
        <div class="flex items-center justify-between">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                <span class="grid h-9 w-9 place-items-center rounded-lg bg-gradient-to-br from-sky-500 to-sky-600 text-white shadow">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M4 7h8l8 10H4z" stroke-width="1.6"/>
                        <path d="M12 7v10" stroke-width="1.6"/>
                    </svg>
                </span>
                <span class="font-semibold text-slate-100">FicTic</span>
            </a>

            <button @click="open = true" class="p-2 rounded-md bg-slate-900/60 text-slate-200 hover:bg-slate-900 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Spacer for mobile fixed header -->
    <div class="h-[60px] md:hidden"></div>

    <!-- Offcanvas mobile menu -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 md:hidden"
         style="display: none;">
        <div @click="open = false" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        <aside x-show="open"
               x-transition:enter="transition ease-out duration-300 transform"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-200 transform"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               class="absolute left-0 top-0 h-full w-72 bg-slate-950 border-r border-white/10 flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-white/5">
                <div class="inline-flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 text-white shadow-lg">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M4 7h8l8 10H4z" stroke-width="1.6"/>
                            <path d="M12 7v10" stroke-width="1.6"/>
                        </svg>
                    </span>
                    <div>
                        <div class="font-bold text-slate-100">FicTic</div>
                        <div class="text-xs text-slate-400">Gestión Académica</div>
                    </div>
                </div>
                <button @click="open = false" class="p-2 text-slate-400 hover:text-slate-200 hover:bg-white/5 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Scrollable navigation -->
            <nav class="flex-1 overflow-y-auto p-4 space-y-1">
                <a href="{{ route('dashboard') }}" 
                   class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-200 transition {{ request()->routeIs('dashboard') ? 'bg-white/10 font-semibold' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </div>
                </a>

                {{-- =======================
                     Preparación institucional
                   ======================= --}}
                @canany(['abrir_gestion','registrar_unidades_academicas','definir_roles_perfiles','configurar_catalogos'])
                <div x-data="{ openPrep: {{ request()->routeIs('gestiones.*') || request()->routeIs('unidades.*') || request()->routeIs('roles.*') || request()->routeIs('catalogos.*') ? 'true' : 'false' }} }">
                    <button @click="openPrep = ! openPrep" class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-white/5 text-slate-200 transition">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h6m0 0V3m0 4l8 10m0 0h4m-4 0v4" />
                            </svg>
                            <span>Preparación institucional</span>
                        </div>
                        <svg :class="{'rotate-180': openPrep}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="openPrep" x-transition class="mt-2 space-y-1 pl-3">
                        @can('abrir_gestion')
                            <a href="{{ route('gestiones.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('gestiones.*') ? 'bg-white/5 text-slate-100' : '' }}">
                                Abrir Gestión Académica
                            </a>
                        @endcan
                        @can('registrar_unidades_academicas')
                            <a href="{{ route('unidades.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('unidades.*') ? 'bg-white/5 text-slate-100' : '' }}">
                                Unidades Académicas
                            </a>
                        @endcan
                        @can('definir_roles_perfiles')
                            <a href="{{ route('roles.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('roles.*') ? 'bg-white/5 text-slate-100' : '' }}">
                                Roles y Perfiles
                            </a>
                        @endcan
                        @can('configurar_catalogos')
                            <a href="{{ route('bloques.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('bloques.*') ? 'bg-white/5 text-slate-100' : '' }}">
                                Catálogos Académicos
                            </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                {{-- =======================
                     Gestión de Usuarios y Seguridad
                   ======================= --}}
                @canany(['importar_usuarios','asignar_perfiles_ambitos'])
                <div x-data="{ openUsr: {{ request()->routeIs('usuarios.import.*') || request()->routeIs('ambitos.*') ? 'true' : 'false' }} }">
                    <button @click="openUsr = ! openUsr" class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-white/5 text-slate-200 transition">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 11a4 4 0 10-8 0 4 4 0 008 0z"/>
                            </svg>
                            <span>Usuarios y Seguridad</span>
                        </div>
                        <svg :class="{'rotate-180': openUsr}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="openUsr" x-transition class="mt-2 space-y-1 pl-3">
                        @can('importar_usuarios')
                            <a href="{{ route('usuarios.import.create') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('usuarios.import.*') ? 'bg-white/5 text-slate-100' : '' }}">
                                Importar Usuarios
                            </a>
                        @endcan
                        @can('asignar_perfiles_ambitos')
                            <a href="{{ route('usuarios.ambitos.browse') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('ambitos.*') ? 'bg-white/5 text-slate-100' : '' }}">
                                Asignar Perfiles y Ámbitos
                            </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                {{-- =======================
                     Oferta Académica y Recursos
                   ======================= --}}
                @canany(['gestionar_asignaturas','gestionar_grupos','gestionar_aulas'])
                <div x-data="{ openOfr: {{ request()->routeIs('carreras.*') || request()->routeIs('aulas.*') || request()->routeIs('grupos.materias') || request()->routeIs('carreras.materias.grupos.*') ? 'true' : 'false' }} }">
                    <button @click="openOfr = ! openOfr" class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-white/5 text-slate-200 transition">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10M4 18h10"/>
                            </svg>
                            <span>Oferta y Recursos</span>
                        </div>
                        <svg :class="{'rotate-180': openOfr}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="openOfr" x-transition class="mt-2 space-y-1 pl-3">
                        @can('gestionar_asignaturas')
                            <a href="{{ route('carreras.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('carreras.*') ? 'bg-white/5 text-slate-100' : '' }}">
                                CU-10. Gestionar Asignaturas
                                <span class="block text-[11px] text-slate-500 mt-0.5">Buscar carrera (p.ej. Informática, Sistemas…)</span>
                            </a>
                        @endcan
                        @can('gestionar_grupos')
                            <div x-data="{ openGrupos: {{ request()->routeIs('grupos.materias') || request()->routeIs('carreras.materias.grupos.*') ? 'true' : 'false' }} }" class="space-y-1">
                                <button @click="openGrupos = ! openGrupos"
                                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition">
                                    <span>CU-11. Gestionar Grupos</span>
                                    <svg :class="{'rotate-180': openGrupos}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="openGrupos"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    class="mt-1 pl-3 space-y-1">

                                    @php
                                        // Obtén hasta 5 carreras (ajusta orden si quieres por facultad o ámbito)
                                        $navCarreras = \App\Models\Carrera::orderBy('nombre')->limit(5)->get(['id_carrera','nombre']);
                                        $routeCarrera = optional(request()->route('carrera'));
                                        $routeCarreraId = is_object($routeCarrera) ? ($routeCarrera->id_carrera ?? null) : $routeCarrera;
                                    @endphp

                                    @forelse($navCarreras as $idx => $c)
                                        <a href="{{ route('grupos.materias', ['carrera' => $c->id_carrera]) }}"
                                        class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition
                                        {{ (request()->routeIs('grupos.materias') && (int)$routeCarreraId === (int)$c->id_carrera) ? 'bg-white/5 text-slate-100' : '' }}">
                                            {{ $idx + 1 }}. {{ $c->nombre }}
                                        </a>
                                    @empty
                                        <div class="px-3 py-2 text-xs text-slate-500">No hay carreras registradas.</div>
                                    @endforelse

                                    {{-- Enlace para ir al buscador general de carreras/materias si deseas --}}
                                    <a href="{{ route('carreras.index') }}"
                                    class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-400 text-xs transition">
                                        Ver todas las carreras…
                                    </a>
                                </div>
                            </div>
                        @endcan
                        @can('gestionar_aulas')
                            <a href="{{ route('aulas.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('aulas.*') ? 'bg-white/5 text-slate-100' : '' }}">
                                CU-12. Gestionar Aulas
                            </a>
                        @endcan
                        @can('registrar_carga_docente')
                            <a href="{{ route('cargas-docentes.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('cargas-docentes.*') ? 'bg-white/5 text-slate-100' : '' }}">
                                CU-13. Registrar Carga Docente
                            </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                {{-- =======================
                     Programación de Horarios
                   ======================= --}}
                @canany(['asignar_horarios','generar_horario_auto','validar_conflictos','aprobar_horarios','publicar_horarios'])
                <div x-data="{ openHor: {{ request()->routeIs('horarios.*') || request()->routeIs('generacion-horarios.*') || request()->routeIs('validacion-horarios.*') || request()->routeIs('aprobaciones.*') || request()->routeIs('publicacion.*') ? 'true' : 'false' }} }">
                    <button @click="openHor = ! openHor" class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-white/5 text-slate-200 transition">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Programación de Horarios</span>
                        </div>
                        <svg :class="{'rotate-180': openHor}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="openHor" x-transition class="mt-2 space-y-1 pl-3">
                        @can('asignar_horarios')
                            <a href="{{ route('horarios.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('horarios.*') ? 'bg-white/5 text-slate-100' : '' }}">
                                CU-14. Asignación Manual
                            </a>
                        @endcan
                        @can('generar_horario_auto')
                            <a href="{{ route('generacion-horarios.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('generacion-horarios.*') ? 'bg-white/5 text-slate-100' : '' }}">
                                CU-15. Generación Automática
                            </a>
                        @endcan
                        @can('validar_conflictos')
                            <a href="{{ route('validacion-horarios.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('validacion-horarios.*') ? 'bg-white/5 text-slate-100' : '' }}">
                                CU-16. Gestión de Conflictos
                            </a>
                        @endcan
                        @can('aprobar_horarios')
                            <a href="{{ route('aprobaciones.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('aprobaciones.*') ? 'bg-white/5 text-slate-100' : '' }}">
                                CU-17. Aprobación de Horarios
                            </a>
                        @endcan
                        @can('publicar_horarios')
                            <a href="{{ route('publicacion.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('publicacion.index') ? 'bg-white/5 text-slate-100' : '' }}">
                                CU-18. Publicación de Horarios
                            </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                {{-- Control de Asistencia Docente --}}
                @canany(['generar_qr_docente','registrar_asistencia_qr','asistencia_manual','gestionar_justificaciones','gestionar_suplencias'])
                <div x-data="{ openAsis: {{ request()->routeIs('qr-docente.*') || request()->routeIs('asistencia-qr.*') || request()->routeIs('asistencia-manual.*') || request()->routeIs('justificaciones.*') || request()->routeIs('suplencias.*') ? 'true' : 'false' }} }">
                    <button @click="openAsis = ! openAsis" class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-white/5 text-slate-200 transition">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                            <span>Control de Asistencia</span>
                        </div>
                        <svg :class="{'rotate-180': openAsis}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="openAsis" x-transition class="mt-2 space-y-1 pl-3">
                        @can('generar_qr_docente')
                            <a href="{{ route('qr-docente.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('qr-docente.index') || request()->routeIs('qr-docente.ver') || request()->routeIs('qr-docente.estadisticas') ? 'bg-white/5 text-slate-100' : '' }}">
                                CU-19. Generar QR Docente
                            </a>
                        @endcan
                        @can('registrar_asistencia_qr')
                            <a href="{{ route('asistencia-qr.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('asistencia-qr.*') ? 'bg-white/5 text-slate-100' : '' }}">
                                CU-20. Registrar Asistencia (QR)
                            </a>
                        @endcan
                        @can('asistencia_manual')
                            <a href="{{ route('asistencia-manual.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('asistencia-manual.*') ? 'bg-white/5 text-slate-100' : '' }}">
                                CU-21. Asistencia Manual
                            </a>
                        @endcan
                        @can('gestionar_justificaciones')
                            <a href="{{ route('justificaciones.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('justificaciones.*') ? 'bg-white/5 text-slate-100' : '' }}">
                                CU-22. Justificaciones
                            </a>
                        @endcan
                        @can('gestionar_suplencias')
                            <a href="{{ route('suplencias.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('suplencias.*') ? 'bg-white/5 text-slate-100' : '' }}">
                                CU-22. Suplencias PRUEBA
                            </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                {{-- ========================
                     Operaciones y Reportes
                   ======================== --}}
                @canany(['gestionar_reprogramaciones', 'ver_reportes'])
                <div x-data="{ openOps: {{ request()->routeIs('reprogramaciones.*') || request()->routeIs('reportes.*') ? 'true' : 'false' }} }">
                    <button @click="openOps = ! openOps" class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-white/5 text-slate-200 transition">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Operaciones y Reportes</span>
                        </div>
                        <svg :class="{'rotate-180': openOps}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="openOps" x-transition class="mt-2 space-y-1 pl-3">
                        @can('gestionar_reprogramaciones')
                            <a href="{{ route('reprogramaciones.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('reprogramaciones.*') ? 'bg-white/5 text-slate-100' : '' }}">
                                CU-23. Reprogramaciones
                            </a>
                        @endcan
                        @can('ver_reportes')
                            <a href="{{ route('reportes.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('reportes.*') ? 'bg-white/5 text-slate-100' : '' }}">
                                CU-24. Reportería y Descargas
                            </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                {{-- ===========
                     Bitácora
                   =========== --}}
                @can('ver_bitacora')
                    <div class="pt-2">
                        <div class="px-3 text-xs uppercase tracking-wider text-slate-500">Auditoría</div>
                        <a href="{{ route('bitacora.index') }}" 
                           class="mt-1 block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-200 transition {{ request()->routeIs('bitacora.*') ? 'bg-white/10 font-semibold' : '' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h8M9 7h10v10a2 2 0 01-2 2H9l-4 2V5a2 2 0 012-2h4"/>
                                </svg>
                                Bitácora
                            </div>
                        </a>
                    </div>
                @endcan
            </nav>

            <!-- Footer user section -->
            <div class="border-t border-white/5 p-4">
                @auth
                    <div class="mb-3">
                        <div class="text-sm font-medium text-slate-200">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-slate-400">{{ Auth::user()->email }}</div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-200 text-sm transition mb-1">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Perfil
                        </div>
                    </a>
                    <a href="{{ route('qr-docente.mi-qr') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-200 text-sm transition mb-1">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                            Mi QR de Asistencia
                        </div>
                    </a>
                    @can('solicitar_justificacion')
                    <a href="{{ route('justificaciones.mis-justificaciones') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-200 text-sm transition mb-1">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Mis Justificaciones
                        </div>
                    </a>
                    @endcan
                    @role('Docente')
                    <a href="{{ route('suplencias.mis-suplencias') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-200 text-sm transition mb-1">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Mis Suplencias
                        </div>
                    </a>
                    @endrole
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-lg hover:bg-red-500/10 text-red-400 hover:text-red-300 text-sm transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Cerrar sesión
                            </div>
                        </button>
                    </form>
                @endauth
            </div>
        </aside>
    </div>

    <!-- Desktop sidebar -->
    <aside class="hidden md:flex md:flex-col md:fixed md:inset-y-0 md:w-72 bg-slate-950 border-r border-white/10 z-30">
        <!-- Header -->
        <div class="flex items-center gap-3 p-6 border-b border-white/5">
            <span class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 text-white shadow-lg">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M4 7h8l8 10H4z" stroke-width="1.6"/>
                    <path d="M12 7v10" stroke-width="1.6"/>
                </svg>
            </span>
            <div>
                <div class="font-bold text-slate-100">FicTic</div>
                <div class="text-xs text-slate-400">Gestión Académica</div>
            </div>
        </div>

        <!-- Scrollable navigation -->
        <nav class="flex-1 overflow-y-auto p-6 space-y-1">
            <a href="{{ route('dashboard') }}" 
               class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-200 transition {{ request()->routeIs('dashboard') ? 'bg-white/10 font-semibold' : '' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </div>
            </a>

            {{-- Preparación institucional --}}
            @canany(['abrir_gestion','registrar_unidades_academicas','definir_roles_perfiles','configurar_catalogos'])
            <div x-data="{ open: {{ request()->routeIs('gestiones.*') || request()->routeIs('unidades.*') || request()->routeIs('roles.*') || request()->routeIs('catalogos.*') ? 'true' : 'false' }} }">
                <button @click="open = ! open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-white/5 text-slate-200 transition">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h6m0 0V3m0 4l8 10m0 0h4m-4 0v4" />
                        </svg>
                        <span>Preparación institucional</span>
                    </div>
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="mt-2 space-y-1 pl-3">
                    @can('abrir_gestion')
                        <a href="{{ route('gestiones.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('gestiones.*') ? 'bg-white/5 text-slate-100' : '' }}">
                            Abrir Gestión Académica
                        </a>
                    @endcan
                    @can('registrar_unidades_academicas')
                        <a href="{{ route('unidades.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('unidades.*') ? 'bg-white/5 text-slate-100' : '' }}">
                            Unidades Académicas
                        </a>
                    @endcan
                    @can('definir_roles_perfiles')
                        <a href="{{ route('roles.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('roles.*') ? 'bg-white/5 text-slate-100' : '' }}">
                            Roles y Perfiles
                        </a>
                    @endcan
                    @can('configurar_catalogos')
                        <a href="{{ route('bloques.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('bloques.*') ? 'bg-white/5 text-slate-100' : '' }}">
                            Catálogos Académicos
                        </a>
                    @endcan
                </div>
            </div>
            @endcanany

            {{-- Usuarios y Seguridad --}}
            @canany(['importar_usuarios','asignar_perfiles_ambitos'])
            <div x-data="{ open: {{ request()->routeIs('usuarios.import.*') || request()->routeIs('ambitos.*') ? 'true' : 'false' }} }">
                <button @click="open = ! open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-white/5 text-slate-200 transition">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 11a4 4 0 10-8 0 4 4 0 008 0z"/>
                        </svg>
                        <span>Usuarios y Seguridad</span>
                    </div>
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="mt-2 space-y-1 pl-3">
                    @can('importar_usuarios')
                        <a href="{{ route('usuarios.import.create') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('usuarios.import.*') ? 'bg-white/5 text-slate-100' : '' }}">
                            Importar Usuarios
                        </a>
                    @endcan
                    @can('asignar_perfiles_ambitos')
                        <a href="{{ route('usuarios.ambitos.browse') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('ambitos.*') ? 'bg-white/5 text-slate-100' : '' }}">
                            Asignar Perfiles y Ámbitos
                        </a>
                    @endcan
                </div>
            </div>
            @endcanany

            {{-- Oferta y Recursos --}}
            @canany(['gestionar_asignaturas','gestionar_grupos','gestionar_aulas'])
            <div x-data="{ open: {{ request()->routeIs('carreras.*') || request()->routeIs('aulas.*') || request()->routeIs('grupos.materias') || request()->routeIs('carreras.materias.grupos.*') ? 'true' : 'false' }} }">
                <button @click="open = ! open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-white/5 text-slate-200 transition">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10M4 18h10"/>
                        </svg>
                        <span>Oferta y Recursos</span>
                    </div>
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="mt-2 space-y-1 pl-3">
                    @can('gestionar_asignaturas')
                        <a href="{{ route('carreras.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('carreras.*') ? 'bg-white/5 text-slate-100' : '' }}">
                            CU-10. Gestionar Asignaturas
                            <span class="block text-[11px] text-slate-500 mt-0.5">Buscar carrera (Informática, Sistemas…)</span>
                        </a>
                    @endcan
@can('gestionar_grupos')
    <div x-data="{ openGrupos: {{ request()->routeIs('grupos.materias') || request()->routeIs('carreras.materias.grupos.*') ? 'true' : 'false' }} }" class="space-y-1">
        <button @click="openGrupos = ! openGrupos"
                class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition">
            <span>CU-11. Gestionar Grupos</span>
            <svg :class="{'rotate-180': openGrupos}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="openGrupos"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             class="mt-1 pl-3 space-y-1">

            @php
                // Obtén hasta 5 carreras (ajusta orden si quieres por facultad o ámbito)
                $navCarreras = \App\Models\Carrera::orderBy('nombre')->limit(5)->get(['id_carrera','nombre']);
                $routeCarrera = optional(request()->route('carrera'));
                $routeCarreraId = is_object($routeCarrera) ? ($routeCarrera->id_carrera ?? null) : $routeCarrera;
            @endphp

            @forelse($navCarreras as $idx => $c)
                <a href="{{ route('grupos.materias', ['carrera' => $c->id_carrera]) }}"
                   class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition
                   {{ (request()->routeIs('grupos.materias') && (int)$routeCarreraId === (int)$c->id_carrera) ? 'bg-white/5 text-slate-100' : '' }}">
                    {{ $idx + 1 }}. {{ $c->nombre }}
                </a>
            @empty
                <div class="px-3 py-2 text-xs text-slate-500">No hay carreras registradas.</div>
            @endforelse

            {{-- Enlace para ir al buscador general de carreras/materias si deseas --}}
            <a href="{{ route('carreras.index') }}"
               class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-400 text-xs transition">
                Ver todas las carreras…
            </a>
        </div>
    </div>
@endcan
                    @can('gestionar_aulas')
                        <a href="{{ route('aulas.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('aulas.*') ? 'bg-white/5 text-slate-100' : '' }}">
                            CU-12. Gestionar Aulas
                        </a>
                    @endcan
                    @can('registrar_carga_docente')
                        <a href="{{ route('cargas-docentes.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('cargas-docentes.*') ? 'bg-white/5 text-slate-100' : '' }}">
                            CU-13. Registrar Carga Docente
                        </a>
                    @endcan
                </div>
            </div>
            @endcanany

            {{-- =======================
                 Programación de Horarios
               ======================= --}}
            @canany(['asignar_horarios','generar_horario_auto','validar_conflictos','aprobar_horarios','publicar_horarios'])
            <div x-data="{ open: {{ request()->routeIs('horarios.*') || request()->routeIs('generacion-horarios.*') || request()->routeIs('validacion-horarios.*') || request()->routeIs('aprobaciones.*') || request()->routeIs('publicacion.*') ? 'true' : 'false' }} }">
                <button @click="open = ! open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-white/5 text-slate-200 transition">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Programación de Horarios</span>
                    </div>
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="mt-2 space-y-1 pl-3">
                    @can('asignar_horarios')
                        <a href="{{ route('horarios.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('horarios.*') ? 'bg-white/5 text-slate-100' : '' }}">
                            CU-14. Asignación Manual
                        </a>
                    @endcan
                    @can('generar_horario_auto')
                        <a href="{{ route('generacion-horarios.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('generacion-horarios.*') ? 'bg-white/5 text-slate-100' : '' }}">
                            CU-15. Generación Automática
                        </a>
                    @endcan
                    @can('validar_conflictos')
                        <a href="{{ route('validacion-horarios.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('validacion-horarios.*') ? 'bg-white/5 text-slate-100' : '' }}">
                            CU-16. Gestión de Conflictos
                        </a>
                    @endcan
                    @can('aprobar_horarios')
                        <a href="{{ route('aprobaciones.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('aprobaciones.*') ? 'bg-white/5 text-slate-100' : '' }}">
                            CU-17. Aprobación de Horarios
                        </a>
                    @endcan
                    @can('publicar_horarios')
                        <a href="{{ route('publicacion.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('publicacion.index') ? 'bg-white/5 text-slate-100' : '' }}">
                            CU-18. Publicación de Horarios
                        </a>
                    @endcan
                </div>
            </div>
            @endcanany

            {{-- =======================
                 Control de Asistencia Docente
               ======================= --}}
            @canany(['generar_qr_docente','registrar_asistencia_qr','asistencia_manual'])
            <div x-data="{ open: {{ request()->routeIs('qr-docente.*') || request()->routeIs('asistencia-qr.*') || request()->routeIs('asistencia-manual.*') ? 'true' : 'false' }} }">
                <button @click="open = ! open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-white/5 text-slate-200 transition">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        <span>Control de Asistencia</span>
                    </div>
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="mt-2 space-y-1 pl-3">
                    @can('generar_qr_docente')
                        <a href="{{ route('qr-docente.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('qr-docente.index') || request()->routeIs('qr-docente.ver') || request()->routeIs('qr-docente.estadisticas') ? 'bg-white/5 text-slate-100' : '' }}">
                            CU-19. Generar QR Docente
                        </a>
                    @endcan
                    @can('registrar_asistencia_qr')
                        <a href="{{ route('asistencia-qr.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('asistencia-qr.*') ? 'bg-white/5 text-slate-100' : '' }}">
                            CU-20. Registrar Asistencia (QR)
                        </a>
                    @endcan
                    @can('asistencia_manual')
                        <a href="{{ route('asistencia-manual.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('asistencia-manual.*') ? 'bg-white/5 text-slate-100' : '' }}">
                            CU-21. Asistencia Manual
                        </a>
                    @endcan
                    @can('gestionar_justificaciones')
                        <a href="{{ route('justificaciones.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('justificaciones.*') && !request()->routeIs('justificaciones.mis-*') ? 'bg-white/5 text-slate-100' : '' }}">
                            CU-22. Justificaciones
                        </a>
                    @endcan
                    @can('gestionar_suplencias')
                        <a href="{{ route('suplencias.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('suplencias.*') && !request()->routeIs('suplencias.mis-*') ? 'bg-white/5 text-slate-100' : '' }}">
                            CU-22. Suplencias
                        </a>
                    @endcan
                </div>
            </div>
            @endcanany

            {{-- =======================
                 Operaciones y Reportes
               ======================= --}}
            @canany(['gestionar_reprogramaciones', 'ver_reportes'])
            <div x-data="{ open: {{ request()->routeIs('reprogramaciones.*') || request()->routeIs('reportes.*') ? 'true' : 'false' }} }">
                <button @click="open = ! open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-white/5 text-slate-200 transition">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Operaciones y Reportes</span>
                    </div>
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="mt-2 space-y-1 pl-3">
                    @can('gestionar_reprogramaciones')
                        <a href="{{ route('reprogramaciones.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('reprogramaciones.*') ? 'bg-white/5 text-slate-100' : '' }}">
                            CU-23. Reprogramaciones
                        </a>
                    @endcan
                    @can('ver_reportes')
                        <a href="{{ route('reportes.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-300 text-sm transition {{ request()->routeIs('reportes.*') ? 'bg-white/5 text-slate-100' : '' }}">
                            CU-24. Reportería y Descargas
                        </a>
                    @endcan
                </div>
            </div>
            @endcanany

            {{-- Bitácora al final --}}
            @can('ver_bitacora')
                <div class="pt-2">
                    <div class="px-3 text-xs uppercase tracking-wider text-slate-500">Auditoría</div>
                    <a href="{{ route('bitacora.index') }}" 
                       class="mt-1 block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-200 transition {{ request()->routeIs('bitacora.*') ? 'bg-white/10 font-semibold' : '' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h8M9 7h10v10a2 2 0 01-2 2H9l-4 2V5a2 2 0 012-2h4"/>
                            </svg>
                            Bitácora
                        </div>
                    </a>
                </div>
            @endcan
        </nav>

        <!-- Footer user section -->
        <div class="border-t border-white/5 p-6">
            @auth
                <div class="mb-3">
                    <div class="text-sm font-medium text-slate-200">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</div>
                </div>
                <a href="{{ route('profile.edit') }}" 
                   class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-200 text-sm transition mb-1 {{ request()->routeIs('profile.*') ? 'bg-white/5' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Perfil
                    </div>
                </a>
                <a href="{{ route('qr-docente.mi-qr') }}" 
                   class="block px-3 py-2 rounded-lg hover:bg-white/5 text-slate-200 text-sm transition mb-1 {{ request()->routeIs('qr-docente.mi-qr') ? 'bg-white/5' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        Mi QR de Asistencia
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-lg hover:bg-red-500/10 text-red-400 hover:text-red-300 text-sm transition">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Cerrar sesión
                        </div>
                    </button>
                </form>
            @endauth
        </div>
    </aside>
</div>
