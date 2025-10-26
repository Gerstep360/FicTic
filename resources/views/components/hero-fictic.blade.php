<section class="container-app pb-10 pt-4">
    <div class="hero-shell bg-aurora p-8 lg:p-12">
        <div class="max-w-4xl mx-auto text-center">
            {{-- Título principal --}}
            <h1 class="text-5xl lg:text-7xl font-bold tracking-tight mb-6">
                Bienvenido a <span class="text-sky-400">FicTic</span>
            </h1>
            
            <p class="text-xl lg:text-2xl text-slate-300 font-medium mb-4">
                La nueva forma de organizar el tiempo en la FICCT.
            </p>
            
            <p class="text-lg text-slate-400 max-w-3xl mx-auto leading-relaxed">
                Planifica sin choques, controla asistencias con precisión y comparte horarios de forma clara.
                <strong class="text-slate-300">FicTic</strong> reúne en un solo lugar la programación académica y el registro digital de asistencia docente 
                para que todo fluya: simple, ordenado y transparente.
            </p>

            {{-- Botones principales --}}
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-primary text-base px-6 py-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                        Ir al panel
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary text-base px-6 py-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Ingresar
                    </a>
                @endauth
                
                <a href="#diferencias" class="btn-ghost text-base px-6 py-3">
                    Conocer más
                </a>
            </div>
        </div>
    </div>
</section>
