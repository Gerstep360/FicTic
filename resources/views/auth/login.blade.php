{{-- resources/views/auth/login.blade.php --}}
<x-layouts.fictic :title="'Iniciar sesión — '.config('app.name','FicTic')">
    <section class="container-app py-12 min-h-[calc(100vh-200px)] flex items-center justify-center">
        <div class="w-full max-w-md">
            {{-- Card de login mejorado --}}
            <div class="hero-shell bg-aurora p-0">
                <div class="p-8 sm:p-10">
                    {{-- Header con logo --}}
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-sky-500/10 border border-sky-500/20 mb-4">
                            <svg class="w-8 h-8 text-sky-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M4 7h8l8 10H4z" stroke-width="1.6"/>
                                <path d="M12 7v10" stroke-width="1.6"/>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold tracking-tight mb-2">Bienvenido de vuelta</h1>
                        <p class="text-slate-400">Ingresa con tu cuenta institucional</p>
                    </div>

                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        {{-- Email --}}
                        <div>
                            <x-input-label for="email" :value="__('Correo electrónico')" class="text-slate-300 mb-2 block font-medium"/>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                    </svg>
                                </div>
                                <x-text-input 
                                    id="email" 
                                    name="email" 
                                    type="email"
                                    :value="old('email')" 
                                    required 
                                    autofocus 
                                    autocomplete="username"
                                    placeholder="tucorreo@uagrm.edu.bo" 
                                    class="input pl-10 block w-full" 
                                />
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        {{-- Password --}}
                        <div>
                            <x-input-label for="password" :value="__('Contraseña')" class="text-slate-300 mb-2 block font-medium"/>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <x-text-input 
                                    id="password" 
                                    name="password" 
                                    type="password"
                                    required 
                                    autocomplete="current-password" 
                                    placeholder="••••••••"
                                    class="input pl-10 block w-full" 
                                />
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        {{-- Remember & Forgot --}}
                        <div class="flex items-center justify-between text-sm">
                            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                                <input id="remember_me" type="checkbox" name="remember" class="checkbox">
                                <span class="text-slate-300">Recordarme</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="link-muted hover:text-sky-400 transition">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif
                        </div>

                        {{-- Botones --}}
                        <div class="space-y-3 pt-2">
                            <x-primary-button class="btn-primary w-full justify-center text-base py-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                {{ __('Ingresar') }}
                            </x-primary-button>

                            <div class="relative">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-white/10"></div>
                                </div>
                                <div class="relative flex justify-center text-xs">
                                    <span class="px-2 bg-slate-900/50 text-slate-400">¿No tienes cuenta?</span>
                                </div>
                            </div>

                            <a href="mailto:dtic@uagrm.edu.bo?subject=Solicitud%20de%20alta%20de%20cuenta%20FicTic&body=Hola%20DTIC%2C%20necesito%20una%20cuenta%20para%20FicTic."
                               class="btn-ghost w-full justify-center text-sm"
                               title="Escríbenos para solicitar tu alta">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Contactar al DTIC
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Link de regreso --}}
            <div class="text-center mt-6">
                <a href="{{ url('/') }}" class="text-sm text-slate-400 hover:text-slate-200 transition inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver al inicio
                </a>
            </div>
        </div>
    </section>
</x-layouts.fictic>
