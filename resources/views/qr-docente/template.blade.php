{{-- resources/views/qr-docente/template.blade.php --}}
<div id="qr-card-export" class="relative bg-white w-full max-w-[400px] mx-auto rounded-[2.5rem] p-8 text-center shadow-2xl shadow-slate-200/50 border border-slate-100 font-sans overflow-hidden">
    
    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-indigo-500 opacity-80"></div>

    <div class="mb-8 mt-4">
        <p class="text-[11px] font-bold tracking-[0.3em] text-slate-400 uppercase mb-3">
            Facultad de Ciencias y Tecnología
        </p>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight leading-none">
            Control de Asistencia
        </h1>
    </div>

    <div class="mb-8 relative inline-block">
        <div class="p-4 bg-slate-50 rounded-[2rem] border border-slate-100 shadow-inner">
            <div class="bg-white p-3 rounded-[1.5rem] shadow-sm ring-1 ring-slate-900/5 qr-container">
                @php
                    $svgBase64 = base64_encode($qrSvg);
                    $qrDataUrl = 'data:image/svg+xml;base64,' . $svgBase64;
                @endphp
                <img src="{{ $qrDataUrl }}" alt="QR Code" class="w-48 h-48 sm:w-56 sm:h-56 object-contain mx-auto">
            </div>
        </div>
        
        <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 bg-white px-4 py-1.5 rounded-full shadow-lg shadow-indigo-500/10 border border-indigo-50 flex items-center gap-2">
            <span class="relative flex h-2.5 w-2.5">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-indigo-600"></span>
            </span>
            <span class="text-[11px] font-bold text-indigo-900 uppercase tracking-wider">Escanear aquí</span>
        </div>
    </div>

    <div class="space-y-2 px-2">
        <h2 class="text-xl sm:text-2xl font-black text-slate-900 uppercase leading-tight break-words">
            {{ $token->docente->name }}
        </h2>
        <div class="inline-block px-3 py-1 bg-slate-100 rounded-lg">
             <p class="text-xs font-bold text-slate-600 uppercase tracking-wide break-words">
                {{ $token->docente->roles->pluck('name')->join(' / ') }}
            </p>
        </div>
    </div>

    <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col items-center">
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Gestión Académica</span>
        <span class="text-sm font-bold text-indigo-600">{{ $token->gestion->nombre }}</span>
    </div>

</div>