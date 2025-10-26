<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-200 leading-tight">
                Asignación de Ámbitos — Usuarios
            </h2>
            <form method="GET" action="{{ route('usuarios.ambitos.browse') }}" class="flex gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre o correo…" class="input w-64">
                <select name="per_page" class="input w-28">
                    @foreach([20,50,100] as $n)
                        <option value="{{ $n }}" @selected(request('per_page',20)==$n)>{{ $n }}</option>
                    @endforeach
                </select>
                <button class="btn-primary">Buscar</button>
            </form>
        </div>
    </x-slot>

    <div class="card overflow-hidden">
        <div class="hidden lg:block">
            <table class="w-full">
                <thead class="bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase">Usuario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase">Roles</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-800/30">
                            <td class="px-6 py-4 font-medium text-slate-200">{{ $u->name }}</td>
                            <td class="px-6 py-4 text-slate-300">{{ $u->email }}</td>
                            <td class="px-6 py-4">
                                @forelse($u->roles as $r)
                                    <span class="chip mr-1">{{ $r->name }}</span>
                                @empty
                                    <span class="text-slate-400 text-sm">—</span>
                                @endforelse
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a class="btn-ghost" href="{{ route('usuarios.ambitos.index', $u) }}">Asignar ámbitos</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center text-slate-400">Sin resultados</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Móvil --}}
        <div class="lg:hidden p-4 space-y-4">
            @forelse($users as $u)
                <div class="card p-4">
                    <div class="font-medium text-slate-200">{{ $u->name }}</div>
                    <div class="text-sm text-slate-400">{{ $u->email }}</div>
                    <div class="mt-2">
                        @forelse($u->roles as $r)
                            <span class="chip mr-1">{{ $r->name }}</span>
                        @empty
                            <span class="text-slate-400 text-sm">Sin roles</span>
                        @endforelse
                    </div>
                    <div class="mt-3">
                        <a class="btn-ghost w-full text-center" href="{{ route('usuarios.ambitos.index', $u) }}">Asignar ámbitos</a>
                    </div>
                </div>
            @empty
                <div class="text-center text-slate-400 py-8">Sin resultados</div>
            @endforelse
        </div>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</x-app-layout>
