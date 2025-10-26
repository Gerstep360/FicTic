{{-- resources/views/components/layouts/guest.blade.php --}}
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
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased flex flex-col">
    {{-- Header simple para guest --}}
    <header class="border-b border-white/10 bg-slate-950/80 backdrop-blur-xl">
        <div class="container-app py-4">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3 group">
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
        </div>
    </header>

    {{-- Contenido principal --}}
    <main class="flex-1">
        {{ $slot }}
    </main>

    {{-- Footer simple --}}
    <footer class="border-t border-white/10 bg-slate-950/50 backdrop-blur-xl mt-auto">
        <div class="container-app py-6">
            <p class="text-center text-sm text-slate-400">
                © {{ date('Y') }} <span class="text-slate-300 font-medium">FicTic</span> — FICCT UAGRM
            </p>
        </div>
    </footer>
</body>
</html>
