<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BitacoraController extends Controller
{
    public function __construct()
    {
        // Todas requieren auth
        $this->middleware(['auth']);

        // Solo vistas y export exigen ver_reportes
        $this->middleware(['permission:ver_reportes'])->only(['index','show','export']);
    }

    /**
     * Vista: listado con filtros + paginación.
     */
    public function index(Request $request)
    {
        $request->validate([
            'accion'         => ['sometimes','string','max:50'],
            'modulo'         => ['sometimes','string','max:50'],
            'tabla_afectada' => ['sometimes','string','max:100'],
            'registro_id'    => ['sometimes','numeric'],
            'id_usuario'     => ['sometimes','numeric'],
            'id_gestion'     => ['sometimes','numeric'],
            'exitoso'        => ['sometimes','boolean'],
            'fecha_desde'    => ['sometimes','date'],
            'fecha_hasta'    => ['sometimes','date','after_or_equal:fecha_desde'],
            'q'              => ['sometimes','string','max:255'],
            'order_by'       => ['sometimes','in:created_at,id_bitacora'],
            'sort'           => ['sometimes','in:asc,desc'],
            'per_page'       => ['sometimes','integer','min:1','max:100'],
        ]);

        $q = Bitacora::query()->with(['usuario:id,name','gestion:id_gestion,nombre']);

        if ($v = $request->accion)         { $q->accion($v); }
        if ($v = $request->modulo)         { $q->modulo($v); }
        if ($v = $request->tabla_afectada) { $q->where('tabla_afectada', $v); }
        if ($v = $request->registro_id)    { $q->where('registro_id', $v); }
        if ($v = $request->id_usuario)     { $q->where('id_usuario', $v); }
        if ($v = $request->id_gestion)     { $q->enGestion($v); }
        if (!is_null($request->exitoso))   { $q->where('exitoso', (bool)$request->boolean('exitoso')); }

        if ($request->filled('fecha_desde') || $request->filled('fecha_hasta')) {
            $desde = $request->fecha_desde ?? '1970-01-01';
            $hasta = $request->fecha_hasta ?? now()->toDateString();
            $q->rangoFechas($desde, $hasta);
        }

        if ($term = $request->q) {
            $like = '%'.strtolower($term).'%';
            $q->where(function ($qq) use ($like) {
                $qq->whereRaw('LOWER(descripcion) LIKE ?', [$like])
                   ->orWhereRaw('LOWER(CAST(metadata AS TEXT)) LIKE ?', [$like]);
            });
        }

        $orderBy   = $request->get('order_by', 'created_at');
        $sort      = $request->get('sort', 'desc');
        $perPage   = (int) $request->get('per_page', 20);
        $bitacoras = $q->orderBy($orderBy, $sort)->paginate($perPage)->appends($request->query());

        return view('bitacora.index', compact('bitacoras'));
    }

    /**
     * Vista: detalle de un evento.
     */
    public function show(Bitacora $bitacora)
    {
        $bitacora->load(['usuario:id,name', 'gestion:id_gestion,nombre']);
        return view('bitacora.show', compact('bitacora'));
    }

    /**
     * Exportar CSV respetando los filtros actuales.
     */
    public function export(Request $request): StreamedResponse
    {
        $base = Bitacora::query()->with(['usuario:id,name','gestion:id_gestion,nombre']);

        if ($v = $request->accion)         { $base->accion($v); }
        if ($v = $request->modulo)         { $base->modulo($v); }
        if ($v = $request->tabla_afectada) { $base->where('tabla_afectada', $v); }
        if ($v = $request->registro_id)    { $base->where('registro_id', $v); }
        if ($v = $request->id_usuario)     { $base->where('id_usuario', $v); }
        if ($v = $request->id_gestion)     { $base->enGestion($v); }
        if (!is_null($request->exitoso))   { $base->where('exitoso', (bool)$request->boolean('exitoso')); }

        if ($request->filled('fecha_desde') || $request->filled('fecha_hasta')) {
            $desde = $request->fecha_desde ?? '1970-01-01';
            $hasta = $request->fecha_hasta ?? now()->toDateString();
            $base->rangoFechas($desde, $hasta);
        }

        if ($term = $request->q) {
            $like = '%'.strtolower($term).'%';
            $base->where(function ($qq) use ($like) {
                $qq->whereRaw('LOWER(descripcion) LIKE ?', [$like])
                   ->orWhereRaw('LOWER(CAST(metadata AS TEXT)) LIKE ?', [$like]);
            });
        }

        $base->orderBy('created_at', 'desc');
        $filename = 'bitacora_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($base) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id_bitacora','fecha','usuario','accion','modulo','tabla_afectada','registro_id','descripcion','id_gestion','gestion','ip','metodo','url','exitoso']);
            foreach ($base->cursor() as $row) {
                fputcsv($out, [
                    $row->id_bitacora,
                    optional($row->created_at)->format('Y-m-d H:i:s'),
                    optional($row->usuario)->name,
                    $row->accion, $row->modulo, $row->tabla_afectada, $row->registro_id,
                    $row->descripcion, $row->id_gestion, optional($row->gestion)->nombre,
                    $row->ip, $row->metodo, $row->url, $row->exitoso ? '1' : '0',
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Registrar en bitácora (graba IP real de cliente y analiza navegador/dispositivo).
     * Campos mínimos: accion, tabla_afectada.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'accion'          => ['required','string','max:50'],
            'modulo'          => ['nullable','string','max:50'],
            'tabla_afectada'  => ['required','string','max:100'],
            'registro_id'     => ['nullable','numeric'],
            'descripcion'     => ['nullable','string'],
            'id_gestion'      => ['nullable','integer'],
            'exitoso'         => ['nullable','boolean'],
            'metadata'        => ['nullable'],
            'cambios_antes'   => ['nullable'],
            'cambios_despues' => ['nullable'],
        ]);

        // IP real + UA + URL + método
        $clientMeta = $this->clientMeta($request);

        // Parsear User-Agent / Client Hints
        $uaInfo = $this->parseUserAgent($clientMeta['user_agent'], $request);

        // Mezclar metadata del request (string json o array) con los datos del cliente
        $metaInput = $data['metadata'] ?? [];
        if (is_string($metaInput)) {
            $decoded = json_decode($metaInput, true);
            $metaInput = is_array($decoded) ? $decoded : ['_raw' => $metaInput];
        }
        $metadata = array_merge($metaInput, ['client' => $uaInfo]);

        $bitacora = Bitacora::create(array_merge([
            'id_usuario' => auth()->id(),
            'ip'         => $clientMeta['ip'],
            'user_agent' => $clientMeta['user_agent'],
            'url'        => $clientMeta['url'],
            'metodo'     => $clientMeta['metodo'],
            'exitoso'    => $data['exitoso'] ?? true,
            'metadata'   => $metadata,
        ], collect($data)->except(['metadata'])->toArray()));

        return response()->json(['ok' => true, 'id' => $bitacora->id_bitacora], 201);
    }

    /**
     * Obtiene meta del CLIENTE priorizando encabezados de proxy/CDN.
     */
    protected function clientMeta(Request $request): array
    {
        $candidates = [];

        foreach (['CF-Connecting-IP', 'True-Client-IP', 'X-Real-IP'] as $h) {
            if ($v = $request->headers->get($h)) $candidates[] = $v;
        }
        if ($xff = $request->headers->get('X-Forwarded-For')) {
            foreach (explode(',', $xff) as $ip) {
                $ip = trim($ip);
                if ($ip) $candidates[] = $ip;
            }
        }
        $candidates[] = $request->ip();

        $clientIp = $this->pickPublicIp($candidates);

        return [
            'ip'         => $clientIp,
            'user_agent' => $request->header('User-Agent'),
            'url'        => $request->fullUrl(),
            'metodo'     => $request->method(),
        ];
    }

    /**
     * Selecciona la primera IP pública válida; si no hay, devuelve la primera válida.
     */
    protected function pickPublicIp(array $candidates): ?string
    {
        $firstValid = null;
        foreach ($candidates as $raw) {
            $ip = trim($raw);
            if (!filter_var($ip, FILTER_VALIDATE_IP)) continue;
            $firstValid ??= $ip;
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
        return $firstValid;
    }

    /**
     * Analiza User-Agent y Client Hints para identificar SO, navegador, versión y tipo de dispositivo.
     * - Devuelve: ['so','navegador','version','dispositivo','movil','tablet','desktop','ua','hints'=>[]]
     */
    protected function parseUserAgent(?string $ua, Request $request): array
    {
        $ua = $ua ?? '';

        // Client Hints (si están disponibles, los usamos para ajustar)
        $hints = [
            'sec_ch_ua'         => $request->headers->get('Sec-CH-UA'),
            'sec_ch_ua_mobile'  => $request->headers->get('Sec-CH-UA-Mobile'),
            'sec_ch_ua_platform'=> $request->headers->get('Sec-CH-UA-Platform'),
        ];

        // Detectar SO
        $so = 'desconocido';
        if (stripos($ua, 'Windows') !== false) {
            if (stripos($ua, 'Windows 11') !== false) {
                $so = 'Windows 11';
            } elseif (stripos($ua, 'Windows NT 10.0') !== false) {
                $so = 'Windows 10';
            } elseif (stripos($ua, 'Windows NT 6.3') !== false) {
                $so = 'Windows 8.1';
            } elseif (stripos($ua, 'Windows NT 6.2') !== false) {
                $so = 'Windows 8';
            } elseif (stripos($ua, 'Windows NT 6.1') !== false) {
                $so = 'Windows 7';
            } else {
                $so = 'Windows';
            }
        } elseif (preg_match('/(iPhone|iPad|iPod)/i', $ua)) {
            if (preg_match('/OS\s([\d_]+)/i', $ua, $m)) {
                $ver = str_replace('_', '.', $m[1]);
                $so = 'iOS '.$ver;
            } else {
                $so = 'iOS';
            }
        } elseif (stripos($ua, 'Android') !== false) {
            if (preg_match('/Android\s([\d\.]+)/i', $ua, $m)) {
                $so = 'Android '.$m[1];
            } else {
                $so = 'Android';
            }
        } elseif (stripos($ua, 'Mac OS X') !== false || stripos($ua, 'Macintosh') !== false) {
            if (preg_match('/Mac OS X\s?([\d_\.]+)/i', $ua, $m)) {
                $ver = str_replace(['_', ' '], '.', $m[1]);
                $so = 'macOS '.$ver;
            } else {
                $so = 'macOS';
            }
        } elseif (stripos($ua, 'Linux') !== false) {
            $so = 'Linux';
        }

        // Usar Client Hint de plataforma si existe
        if (!empty($hints['sec_ch_ua_platform'])) {
            $plat = trim($hints['sec_ch_ua_platform'], "\"'");
            // Normalización simple
            if (stripos($plat, 'Windows') !== false && $so === 'Windows 10') {
                $so = 'Windows 10/11';
            } elseif (stripos($plat, 'Android') !== false) {
                $so = preg_match('/Android/i', $so) ? $so : 'Android';
            } elseif (stripos($plat, 'iOS') !== false) {
                $so = preg_match('/iOS/i', $so) ? $so : 'iOS';
            } elseif (stripos($plat, 'macOS') !== false || stripos($plat, 'Mac') !== false) {
                $so = preg_match('/macOS/i', $so) ? $so : 'macOS';
            }
        }

        // Detectar navegador y versión
        $nav = 'Desconocido'; $ver = null;
        // Orden importa: Edge antes que Chrome, Opera antes que Chrome, Samsung antes que Chrome
        if (preg_match('/Edg\/([\d\.]+)/', $ua, $m)) {
            $nav = 'Microsoft Edge'; $ver = $m[1];
        } elseif (preg_match('/OPR\/([\d\.]+)/', $ua, $m)) {
            $nav = 'Opera'; $ver = $m[1];
        } elseif (preg_match('/SamsungBrowser\/([\d\.]+)/', $ua, $m)) {
            $nav = 'Samsung Internet'; $ver = $m[1];
        } elseif (preg_match('/CriOS\/([\d\.]+)/', $ua, $m)) {
            $nav = 'Chrome (iOS)'; $ver = $m[1];
        } elseif (preg_match('/Chrome\/([\d\.]+)/', $ua, $m)) {
            $nav = 'Google Chrome'; $ver = $m[1];
        } elseif (preg_match('/Firefox\/([\d\.]+)/', $ua, $m)) {
            $nav = 'Mozilla Firefox'; $ver = $m[1];
        } elseif (preg_match('/Version\/([\d\.]+).*Safari/i', $ua, $m)) {
            $nav = 'Safari'; $ver = $m[1];
        } elseif (stripos($ua, 'Safari') !== false) {
            $nav = 'Safari';
        }

        // Hints pueden contener marcas (Chromium, "Google Chrome", "Microsoft Edge")
        if (!empty($hints['sec_ch_ua'])) {
            $brands = strtolower($hints['sec_ch_ua']);
            if (str_contains($brands, 'edge'))      { $nav = 'Microsoft Edge'; }
            elseif (str_contains($brands, 'chrome')){ $nav = 'Google Chrome'; }
            elseif (str_contains($brands, 'safari')){ $nav = 'Safari'; }
            elseif (str_contains($brands, 'firefox')){ $nav = 'Mozilla Firefox'; }
        }

        // Tipo de dispositivo
        $isTablet = (bool) preg_match('/(iPad|Tablet|Nexus 7|Nexus 10|SM-T|Tab)/i', $ua)
                    || (isset($hints['sec_ch_ua_mobile']) && $hints['sec_ch_ua_mobile'] === '?0'
                        && stripos($so, 'Android') !== false && stripos($ua, 'Mobile') === false);
        $isMobile = (bool) preg_match('/(Mobile|iPhone|Android.*Mobile)/i', $ua) && !$isTablet;
        $device   = $isTablet ? 'Tablet' : ($isMobile ? 'Móvil' : 'Desktop');

        return [
            'so'        => $so,                 // Windows 10/11, macOS X.Y, Android, iOS, etc.
            'navegador' => $nav,                // Chrome/Edge/Safari/Firefox/Opera/Samsung
            'version'   => $ver,                // versión del navegador si se detectó
            'dispositivo'=> $device,            // Desktop/Móvil/Tablet
            'movil'     => $isMobile,
            'tablet'    => $isTablet,
            'desktop'   => !$isMobile && !$isTablet,
            'ua'        => $ua,                 // UA crudo
            'hints'     => $hints,              // CH si llegaron
        ];
    }
}
