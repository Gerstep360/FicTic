<x-layouts.fictic :title="config('app.name','FicTic').' — UAGRM'">
    <x-hero-fictic />

    {{-- ¿Qué hace diferente a FicTic? --}}
    <section id="diferencias" class="container-app py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl lg:text-4xl font-bold tracking-tight mb-3">
                ¿Qué hace diferente a <span class="text-sky-400">FicTic</span>?
            </h2>
            <p class="text-slate-400 max-w-2xl mx-auto">
                Un sistema pensado para simplificar la gestión académica con herramientas modernas y transparentes.
            </p>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 max-w-6xl mx-auto">
            {{-- Feature 1 --}}
            <div class="card p-6 hover:border-sky-500/30 transition-all duration-300">
                <div class="w-12 h-12 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-lg mb-2">Horarios impecables</h3>
                <p class="text-sm text-slate-400 leading-relaxed">
                    Asigna materias, grupos, docentes y aulas sin conflictos y con reglas claras de la facultad.
                </p>
            </div>

            {{-- Feature 2 --}}
            <div class="card p-6 hover:border-emerald-500/30 transition-all duration-300">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-lg mb-2">Asistencia confiable</h3>
                <p class="text-sm text-slate-400 leading-relaxed">
                    Marca entradas y salidas con QR operado por personal autorizado; rápido y sin papeles.
                </p>
            </div>

            {{-- Feature 3 --}}
            <div class="card p-6 hover:border-fuchsia-500/30 transition-all duration-300">
                <div class="w-12 h-12 rounded-xl bg-fuchsia-500/10 border border-fuchsia-500/20 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-lg mb-2">Transparencia para todos</h3>
                <p class="text-sm text-slate-400 leading-relaxed">
                    Consulta pública de horarios por docente, grupo o aula, siempre actualizada.
                </p>
            </div>

            {{-- Feature 4 --}}
            <div class="card p-6 hover:border-amber-500/30 transition-all duration-300">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-lg mb-2">Decisiones con datos</h3>
                <p class="text-sm text-slate-400 leading-relaxed">
                    Reportes listos para imprimir o exportar cuando los necesites.
                </p>
            </div>
        </div>
    </section>

    {{-- Empieza ahora --}}
    <section class="container-app py-16">
        <div class="hero-shell bg-aurora p-8 lg:p-10">
            <div class="text-center mb-10">
                <h2 class="text-3xl lg:text-4xl font-bold tracking-tight mb-3">
                    Empieza ahora
                </h2>
                <p class="text-slate-400 max-w-2xl mx-auto">
                    Porque cada minuto cuenta, FicTic convierte la gestión académica en una experiencia ágil y confiable.
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 max-w-5xl mx-auto">
                <a href="#" class="card p-6 hover:bg-white/5 hover:border-sky-500/30 transition-all duration-300 group">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-sky-500/10 flex items-center justify-center shrink-0 group-hover:bg-sky-500/20 transition">
                            <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-1 group-hover:text-sky-400 transition">Ver horarios publicados</h3>
                            <p class="text-sm text-slate-400">Encuentra grupos, aulas o docentes en segundos.</p>
                        </div>
                    </div>
                </a>

                <a href="#" class="card p-6 hover:bg-white/5 hover:border-emerald-500/30 transition-all duration-300 group">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center shrink-0 group-hover:bg-emerald-500/20 transition">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-1 group-hover:text-emerald-400 transition">Programar mi carrera</h3>
                            <p class="text-sm text-slate-400">Crea o ajusta horarios con validación automática.</p>
                        </div>
                    </div>
                </a>

                <a href="#" class="card p-6 hover:bg-white/5 hover:border-fuchsia-500/30 transition-all duration-300 group">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-fuchsia-500/10 flex items-center justify-center shrink-0 group-hover:bg-fuchsia-500/20 transition">
                            <svg class="w-5 h-5 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-1 group-hover:text-fuchsia-400 transition">Registrar asistencias</h3>
                            <p class="text-sm text-slate-400">Escanea QR y confirma en tiempo real.</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>
</x-layouts.fictic>
