<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:items-center sm:ms-10 gap-4">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    {{-- =======================
                         Preparación institucional
                       ======================= --}}
                    @canany(['abrir_gestion','registrar_unidades_academicas','definir_roles_perfiles','configurar_catalogos'])
                        <x-dropdown align="left" width="56">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 focus:outline-none transition">
                                    <span>Preparación institucional</span>
                                    <svg class="ms-2 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"/></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                @can('abrir_gestion')
                                    <x-dropdown-link :href="route('gestiones.index')">Abrir Gestión Académica</x-dropdown-link>
                                @endcan
                                @can('registrar_unidades_academicas')
                                    <x-dropdown-link :href="route('unidades.index')">Unidades Académicas</x-dropdown-link>
                                @endcan
                                @can('definir_roles_perfiles')
                                    <x-dropdown-link :href="route('roles.index')">Roles y Perfiles</x-dropdown-link>
                                @endcan
                                @can('configurar_catalogos')
                                    <x-dropdown-link :href="route('catalogos.index')">Catálogos Académicos</x-dropdown-link>
                                @endcan
                            </x-slot>
                        </x-dropdown>
                    @endcanany

                    {{-- =======================
                         Gestión de Usuarios y Seguridad
                       ======================= --}}
                    @canany(['importar_usuarios','asignar_perfiles_ambitos'])
                        <x-dropdown align="left" width="56">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 focus:outline-none transition">
                                    <span>Usuarios y Seguridad</span>
                                    <svg class="ms-2 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"/></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                @can('importar_usuarios')
                                    <x-dropdown-link :href="route('usuarios.import.create')">Importar Usuarios</x-dropdown-link>
                                @endcan
                                @can('asignar_perfiles_ambitos')
                                    <x-dropdown-link :href="route('ambitos.index')">Asignar Perfiles y Ámbitos</x-dropdown-link>
                                @endcan
                            </x-slot>
                        </x-dropdown>
                    @endcanany

                    {{-- =======================
                         Oferta Académica y Recursos
                       ======================= --}}
                    @canany(['gestionar_asignaturas','gestionar_grupos','gestionar_aulas','registrar_carga_docente'])
                        <x-dropdown align="left" width="64">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 focus:outline-none transition">
                                    <span>Oferta y Recursos</span>
                                    <svg class="ms-2 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"/></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                @can('gestionar_asignaturas')
                                    {{-- Empieza buscando una carrera --}}
                                    <x-dropdown-link :href="route('carreras.index')">Gestionar Asignaturas</x-dropdown-link>
                                @endcan
                                @can('gestionar_grupos')
                                    {{-- Vista de materias para grupos (listado simple) --}}
                                    <x-dropdown-link :href="route('grupos.materias')">Gestionar Grupos</x-dropdown-link>
                                @endcan
                                @can('gestionar_aulas')
                                    <x-dropdown-link :href="route('aulas.index')">Gestionar Aulas</x-dropdown-link>
                                @endcan
                                @can('registrar_carga_docente')
                                    <x-dropdown-link :href="route('carga-docente.index')">CU-13. Registrar Carga Docente</x-dropdown-link>
                                @endcan
                            </x-slot>
                        </x-dropdown>
                    @endcanany

                    {{-- =======================
                         Programación de Horarios
                       ======================= --}}
                    @canany(['asignar_horarios','generar_horario_auto','validar_conflictos','aprobar_horarios','publicar_horarios'])
                        <x-dropdown align="left" width="56">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 focus:outline-none transition">
                                    <span>Programación</span>
                                    <svg class="ms-2 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"/></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                @can('asignar_horarios')
                                    <x-dropdown-link :href="route('horarios.asignar')">CU-14. Asignar Horarios</x-dropdown-link>
                                @endcan
                                @can('generar_horario_auto')
                                    <x-dropdown-link :href="route('horarios.auto')">CU-15. Generar Horario Automático</x-dropdown-link>
                                @endcan
                                @can('validar_conflictos')
                                    <x-dropdown-link :href="route('horarios.validar')">CU-16. Validar Conflictos</x-dropdown-link>
                                @endcan
                                @can('aprobar_horarios')
                                    <x-dropdown-link :href="route('aprobaciones.index')">CU-17. Aprobar Horarios</x-dropdown-link>
                                @endcan
                                @can('publicar_horarios')
                                    <x-dropdown-link :href="route('publicacion.index')">CU-18. Publicar Horarios</x-dropdown-link>
                                @endcan
                            </x-slot>
                        </x-dropdown>
                    @endcanany

                    {{-- =======================
                         Control de Asistencia Docente
                       ======================= --}}
                    @canany(['generar_qr_docente','registrar_asistencia_qr','asistencia_manual','gestionar_justificaciones','gestionar_suplencias'])
                        <x-dropdown align="left" width="60">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 focus:outline-none transition">
                                    <span>Asistencia Docente</span>
                                    <svg class="ms-2 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"/></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                @can('generar_qr_docente')
                                    <x-dropdown-link :href="route('qr-docente.index')">CU-19. Gestión QR Docentes</x-dropdown-link>
                                @endcan
                                @can('registrar_asistencia_qr')
                                    <x-dropdown-link :href="route('asistencia-qr.index')">CU-20. Registrar Asistencia (QR)</x-dropdown-link>
                                @endcan
                                @can('asistencia_manual')
                                    <x-dropdown-link :href="route('asistencia-manual.index')">CU-21. Asistencia Manual</x-dropdown-link>
                                @endcan
                                @can('gestionar_justificaciones')
                                    <x-dropdown-link :href="route('justificaciones.index')">CU-22. Justificaciones</x-dropdown-link>
                                @endcan
                                @can('gestionar_suplencias')
                                    <x-dropdown-link :href="route('suplencias.index')">CU-22. Suplencias</x-dropdown-link>
                                @endcan
                            </x-slot>
                        </x-dropdown>
                    @endcanany

                    {{-- =======================
                         Operación y Reportes
                       ======================= --}}
                    @canany(['reprogramaciones','ver_reportes'])
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 focus:outline-none transition">
                                    <span>Operación</span>
                                    <svg class="ms-2 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"/></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                @can('reprogramaciones')
                                    <x-dropdown-link :href="route('reprogramaciones.index')">CU-23. Reprogramaciones</x-dropdown-link>
                                @endcan
                                @can('ver_reportes')
                                    <x-dropdown-link :href="route('reportes.index')">CU-24. Reportes</x-dropdown-link>
                                @endcan
                            </x-slot>
                        </x-dropdown>
                    @endcanany

                    {{-- Bitácora (al final) --}}
                    @can('ver_bitacora')
                        <x-nav-link :href="route('bitacora.index')" :active="request()->routeIs('bitacora.*')">
                            Bitácora
                        </x-nav-link>
                    @endcan
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Mi QR de Asistencia (para todos los usuarios autenticados) -->
                        <x-dropdown-link :href="route('qr-docente.mi-qr')">
                            Mi QR de Asistencia
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            {{-- Secciones Responsive --}}
            @canany(['abrir_gestion','registrar_unidades_academicas','definir_roles_perfiles','configurar_catalogos'])
                <div class="px-4 pt-2 pb-1 text-xs font-semibold text-gray-500 dark:text-gray-400">Preparación institucional</div>
                @can('abrir_gestion')
                    <x-responsive-nav-link :href="route('gestiones.index')">Abrir Gestión Académica</x-responsive-nav-link>
                @endcan
                @can('registrar_unidades_academicas')
                    <x-responsive-nav-link :href="route('unidades.index')">Unidades Académicas</x-responsive-nav-link>
                @endcan
                @can('definir_roles_perfiles')
                    <x-responsive-nav-link :href="route('roles.index')">Roles y Perfiles</x-responsive-nav-link>
                @endcan
                @can('configurar_catalogos')
                    <x-responsive-nav-link :href="route('catalogos.index')">Catálogos Académicos</x-responsive-nav-link>
                @endcan
            @endcanany

            @canany(['importar_usuarios','asignar_perfiles_ambitos'])
                <div class="px-4 pt-3 pb-1 text-xs font-semibold text-gray-500 dark:text-gray-400">Usuarios y Seguridad</div>
                @can('importar_usuarios')
                    <x-responsive-nav-link :href="route('usuarios.import.create')">Importar Usuarios</x-responsive-nav-link>
                @endcan
                @can('asignar_perfiles_ambitos')
                    <x-responsive-nav-link :href="route('ambitos.index')">Asignar Perfiles y Ámbitos</x-responsive-nav-link>
                @endcan
            @endcanany

            @canany(['gestionar_asignaturas','gestionar_grupos','gestionar_aulas','registrar_carga_docente'])
                <div class="px-4 pt-3 pb-1 text-xs font-semibold text-gray-500 dark:text-gray-400">Oferta y Recursos</div>
                @can('gestionar_asignaturas')
                    <x-responsive-nav-link :href="route('carreras.index')">Gestionar Asignaturas</x-responsive-nav-link>
                @endcan
                @can('gestionar_grupos')
                    <x-responsive-nav-link :href="route('grupos.materias')">Gestionar Grupos</x-responsive-nav-link>
                @endcan
                @can('gestionar_aulas')
                    <x-responsive-nav-link :href="route('aulas.index')">Gestionar Aulas</x-responsive-nav-link>
                @endcan
            @endcanany

            {{-- Fase 4: Control de Asistencia Docente --}}
            @canany(['generar_qr_docente','registrar_asistencia_qr','asistencia_manual'])
                <div class="px-4 pt-3 pb-1 text-xs font-semibold text-gray-500 dark:text-gray-400">Control de Asistencia Docente</div>
                @can('generar_qr_docente')
                    <x-responsive-nav-link :href="route('qr-docente.index')">CU-19. Gestión QR Docentes</x-responsive-nav-link>
                @endcan
                @can('registrar_asistencia_qr')
                    <x-responsive-nav-link :href="route('asistencia-qr.index')">CU-20. Registrar Asistencia (QR)</x-responsive-nav-link>
                @endcan
                @can('asistencia_manual')
                    <x-responsive-nav-link :href="route('asistencia-manual.index')">CU-21. Asistencia Manual</x-responsive-nav-link>
                @endcan
            @endcanany

            {{-- Bitácora (al final) --}}
            @can('ver_bitacora')
                <div class="px-4 pt-3 pb-1 text-xs font-semibold text-gray-500 dark:text-gray-400">Auditoría</div>
                <x-responsive-nav-link :href="route('bitacora.index')">Bitácora</x-responsive-nav-link>
            @endcan
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('qr-docente.mi-qr')">
                    Mi QR de Asistencia
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
