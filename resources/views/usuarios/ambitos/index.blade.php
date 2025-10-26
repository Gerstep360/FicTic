{{-- resources/views/usuarios/ambitos/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="javascript:history.back()" class="text-slate-400 hover:text-slate-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                        Asignación de Perfiles y Ámbitos
                    </h2>
                    <p class="text-sm text-slate-400">
                        Usuario: <span class="font-medium text-slate-200">{{ $user->name }}</span>
                        <span class="mx-2">•</span>
                        <span class="text-slate-400">{{ $user->email }}</span>
                    </p>
                </div>
            </div>

            @if (session('status'))
                <span class="chip">{{ session('status') }}</span>
            @endif
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-2">
        {{-- ===================== FORMULARIO NUEVA ASIGNACIÓN ===================== --}}
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-slate-200 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva asignación
            </h3>

            @if($errors->any())
                <div class="mb-4 p-4 rounded-xl bg-red-500/10 border border-red-500/20">
                    <h4 class="text-red-400 font-medium mb-1">Revisa los campos:</h4>
                    <ul class="list-disc list-inside text-sm text-red-300 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('usuarios.ambitos.store', $user) }}" class="space-y-5">
                @csrf

                {{-- Rol --}}
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Rol *</label>
                    <select id="role_id" name="role_id" class="input" required>
                        <option value="">Selecciona un rol…</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}" {{ old('role_id') == $r->id ? 'selected' : '' }}>
                                {{ $r->name }}
                            </option>
                        @endforeach
                    </select>
                    <p id="roleHelp" class="mt-2 text-xs text-slate-400 hidden"></p>
                </div>

                {{-- hidden reales que viajan al backend --}}
                <input type="hidden" name="scope_type" id="scope_type" value="{{ old('scope_type') }}"/>
                <input type="hidden" name="scope_id"   id="scope_id"   value="{{ old('scope_id')   }}"/>

                {{-- Bloques de ámbito (aparecen según rol) --}}
                <div id="ambito-admin" class="hidden">
                    <div class="rounded-xl border border-white/10 bg-slate-900/60 p-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <p class="text-sm text-slate-300">
                                <span class="font-medium text-slate-200">Admin DTIC</span> no requiere ámbito. Se asignará sólo el rol.
                            </p>
                        </div>
                    </div>
                </div>

                <div id="ambito-user" class="hidden">
                    <label class="block text-sm font-medium text-slate-300 mb-1">Ámbito *</label>
                    <div class="flex items-center justify-between gap-3">
                        <div class="chip flex-1">
                            Propio del usuario: <span class="font-medium text-slate-200">{{ $user->name }}</span>
                        </div>
                        <input type="hidden" id="scope_id_user" value="{{ $user->id }}">
                    </div>
                </div>

                <div id="ambito-carrera" class="hidden">
                    <label class="block text-sm font-medium text-slate-300 mb-1">Carrera *</label>
                    <select id="scope_id_carrera" class="input">
                        <option value="">Selecciona una carrera…</option>
                        @foreach($carreras as $c)
                            <option value="{{ $c->id_carrera }}" {{ old('scope_id') == $c->id_carrera ? 'selected' : '' }}>
                                {{ $c->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-slate-400">Director/Coordinador → debe elegir una carrera.</p>
                </div>

                <div id="ambito-facultad" class="hidden">
                    <label class="block text-sm font-medium text-slate-300 mb-1">Facultad *</label>
                    <select id="scope_id_facultad" class="input">
                        <option value="">Selecciona una facultad…</option>
                        @foreach($facultades as $f)
                            <option value="{{ $f->id_facultad }}" {{ old('scope_id') == $f->id_facultad ? 'selected' : '' }}>
                                {{ $f->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-slate-400">Decano/Vicedecano → debe elegir una facultad.</p>
                </div>

                <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center gap-3 pt-2">
                    <button type="button" class="btn-ghost" onclick="resetForm()">
                        Limpiar
                    </button>
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Asignar
                    </button>
                </div>
            </form>
        </div>

        {{-- ===================== LISTADO DE ÁMBITOS ===================== --}}
        <div class="space-y-4">
            <div class="card overflow-hidden">
                <div class="px-6 py-4 bg-slate-800/40 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-200">Ámbitos asignados</h3>
                </div>

                {{-- Desktop table --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-800/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Rol</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Ámbito</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            @forelse($ambitos as $a)
                                <tr class="hover:bg-slate-800/30">
                                    <td class="px-6 py-4">
                                        <span class="font-medium text-slate-200">{{ $a->role->name ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php $tipo = class_basename($a->scope_type); @endphp

                                        @if($a->role && $a->role->name === 'Admin DTIC')
                                            <span class="chip mr-2">Sin ámbito</span>
                                            <span class="text-slate-400 text-sm">Acceso total (rol global)</span>
                                        @elseif($a->scope_type === \App\Models\Carrera::class)
                                            <span class="chip mr-2">Carrera</span>
                                            <span class="text-slate-200">{{ $a->scope->nombre ?? "ID #{$a->scope_id}" }}</span>
                                        @elseif($a->scope_type === \App\Models\Facultad::class)
                                            <span class="chip mr-2">Facultad</span>
                                            <span class="text-slate-200">{{ $a->scope->nombre ?? "ID #{$a->scope_id}" }}</span>
                                        @elseif($a->scope_type === \App\Models\User::class)
                                            <span class="chip mr-2">Propio</span>
                                            <span class="text-slate-200">{{ $a->scope->name ?? "User #{$a->scope_id}" }}</span>
                                        @else
                                            <span class="chip mr-2">{{ $tipo }}</span>
                                            <span class="text-slate-200">ID #{{ $a->scope_id }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <form method="POST" action="{{ route('usuarios.ambitos.destroy', [$user, $a]) }}"
                                              onsubmit="return confirm('¿Eliminar esta asignación?');"
                                              class="inline-flex">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-ghost">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-slate-400">
                                        No hay ámbitos asignados aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile cards --}}
                <div class="lg:hidden p-4 space-y-3">
                    @forelse($ambitos as $a)
                        <div class="rounded-xl border border-white/10 bg-slate-900/60 p-4">
                            <div class="flex items-center justify-between">
                                <div class="font-medium text-slate-200">{{ $a->role->name ?? '-' }}</div>
                                <form method="POST" action="{{ route('usuarios.ambitos.destroy', [$user, $a]) }}"
                                      onsubmit="return confirm('¿Eliminar esta asignación?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-300 hover:text-red-200 text-sm">Eliminar</button>
                                </form>
                            </div>
                            <div class="mt-2 text-sm text-slate-300">
                                @if($a->role && $a->role->name === 'Admin DTIC')
                                    <span class="chip mr-2">Sin ámbito</span> Acceso total
                                @elseif($a->scope_type === \App\Models\Carrera::class)
                                    <span class="chip mr-2">Carrera</span> {{ $a->scope->nombre ?? "ID #{$a->scope_id}" }}
                                @elseif($a->scope_type === \App\Models\Facultad::class)
                                    <span class="chip mr-2">Facultad</span> {{ $a->scope->nombre ?? "ID #{$a->scope_id}" }}
                                @elseif($a->scope_type === \App\Models\User::class)
                                    <span class="chip mr-2">Propio</span> {{ $a->scope->name ?? "User #{$a->scope_id}" }}
                                @else
                                    <span class="chip mr-2">{{ class_basename($a->scope_type) }}</span> ID #{{ $a->scope_id }}
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-slate-400 py-8">No hay ámbitos asignados aún.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== JS: lógica rol → ámbito ===================== --}}
    <script>
        const FQCN = {
            user:     @json(\App\Models\User::class),
            carrera:  @json(\App\Models\Carrera::class),
            facultad: @json(\App\Models\Facultad::class),
        };

        const ROLE_RULES = {
            'Docente':      { kind: 'user',     help: 'Docente → ámbito propio del usuario.' },
            'Coordinador':  { kind: 'carrera',  help: 'Coordinador → seleccione la carrera.' },
            'Director':     { kind: 'carrera',  help: 'Director → seleccione la carrera.' },
            'Decano':       { kind: 'facultad', help: 'Decano → seleccione la facultad.' },
            'Vicedecano':   { kind: 'facultad', help: 'Vicedecano → seleccione la facultad.' },
            'Admin DTIC':   { kind: 'none',     help: 'Admin DTIC no requiere ámbito (rol global).' },
            '_default':     { kind: 'carrera',  help: '' },
        };

        const roles = @json($roles->map(fn($r)=>['id'=>$r->id,'name'=>$r->name])->values());

        const $role         = document.getElementById('role_id');
        const $roleHelp     = document.getElementById('roleHelp');
        const $scopeType    = document.getElementById('scope_type');
        const $scopeId      = document.getElementById('scope_id');

        const $ambAdmin     = document.getElementById('ambito-admin');
        const $ambUser      = document.getElementById('ambito-user');
        const $ambCarrera   = document.getElementById('ambito-carrera');
        const $ambFac       = document.getElementById('ambito-facultad');

        const $scopeIdUser    = document.getElementById('scope_id_user');
        const $scopeIdCarrera = document.getElementById('scope_id_carrera');
        const $scopeIdFac     = document.getElementById('scope_id_facultad');

        function hideAll() {
            $ambAdmin.classList.add('hidden');
            $ambUser.classList.add('hidden');
            $ambCarrera.classList.add('hidden');
            $ambFac.classList.add('hidden');
            $roleHelp.classList.add('hidden');
        }

        function resetForm() {
            $role.value   = '';
            $scopeType.value = '';
            $scopeId.value   = '';
            hideAll();
        }

        function onRoleChange() {
            hideAll();
            $scopeType.value = '';
            $scopeId.value   = '';

            const roleObj = roles.find(r => r.id == $role.value);
            if (!roleObj) return;

            const rule = ROLE_RULES[roleObj.name] || ROLE_RULES._default;
            if (rule.help) {
                $roleHelp.textContent = rule.help;
                $roleHelp.classList.remove('hidden');
            }

            switch (rule.kind) {
                case 'none': // Admin DTIC (sin ámbito)
                    $ambAdmin.classList.remove('hidden');
                    $scopeType.value = '';
                    $scopeId.value   = '';
                    break;

                case 'user': // Docente
                    $scopeType.value = FQCN.user;
                    $scopeId.value   = $scopeIdUser.value;
                    $ambUser.classList.remove('hidden');
                    break;

                case 'carrera': // Director/Coordinador
                    $scopeType.value = FQCN.carrera;
                    $ambCarrera.classList.remove('hidden');
                    $scopeIdCarrera.addEventListener('change', () => {
                        $scopeId.value = $scopeIdCarrera.value || '';
                    }, { once:true });
                    if ($scopeIdCarrera.value) $scopeId.value = $scopeIdCarrera.value;
                    break;

                case 'facultad': // Decano/Vicedecano
                    $scopeType.value = FQCN.facultad;
                    $ambFac.classList.remove('hidden');
                    $scopeIdFac.addEventListener('change', () => {
                        $scopeId.value = $scopeIdFac.value || '';
                    }, { once:true });
                    if ($scopeIdFac.value) $scopeId.value = $scopeIdFac.value;
                    break;
            }
        }

        $role.addEventListener('change', onRoleChange);

        (function boot() {
            if ($role.value) onRoleChange();
            const oldType = $scopeType.value;
            if (oldType === FQCN.user)     $ambUser.classList.remove('hidden');
            if (oldType === FQCN.carrera)  $ambCarrera.classList.remove('hidden');
            if (oldType === FQCN.facultad) $ambFac.classList.remove('hidden');
        })();
    </script>
</x-app-layout>
