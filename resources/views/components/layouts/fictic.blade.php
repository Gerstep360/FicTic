@props(['title' => config('app.name', 'FicTic').' — UAGRM'])

<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>

    {{-- Fuentes --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|instrument-serif:400,600" rel="stylesheet" />

    {{-- Assets Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col">
    {{-- Header mejorado --}}
    <header class="sticky top-0 z-50 border-b border-white/10 bg-slate-950/80 backdrop-blur-xl">
        <div class="container-app py-4">
            <div class="flex items-center justify-between gap-4">
                {{-- Logo y marca --}}
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 text-white shadow-lg shadow-sky-500/25 group-hover:shadow-sky-500/40 transition-all duration-200 group-hover:scale-105">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 7h8l8 10H4z"/>
                            <path d="M12 7v10"/>
                        </svg>
                    </span>
                    <div>
                        <div class="font-bold tracking-tight text-slate-100 group-hover:text-sky-400 transition">FicTic</div>
                        <div class="text-xs text-slate-400 -mt-0.5">Gestión Académica UAGRM</div>
                    </div>
                </a>

                {{-- Navegación --}}
                <nav class="flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-primary text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                                <span class="hidden sm:inline">Panel</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-ghost text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                Ingresar
                            </a>
                        @endauth
                    @endif
                </nav>
            </div>
        </div>
    </header>

    {{-- Contenido principal --}}
    <main class="flex-1">
        {{ $slot }}
    </main>

    {{-- Footer mejorado --}}
    <footer class="border-t border-white/10 bg-slate-950/50 backdrop-blur-xl mt-auto">
        <div class="container-app py-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm">
                <p class="text-slate-400">
                    © {{ date('Y') }} <span class="text-slate-300 font-medium">FicTic</span> — 
                    <span class="text-slate-500">FICCT UAGRM</span>
                </p>
                <div class="flex items-center gap-4 text-slate-400">
                    <a href="mailto:dtic@uagrm.edu.bo" class="hover:text-sky-400 transition">
                        Soporte
                    </a>
                    <span class="text-slate-600">•</span>
                    <a href="#" class="hover:text-sky-400 transition">
                        Documentación
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
