<?php

namespace App\Http\Controllers;

use App\Models\DocenteQrToken;
use App\Models\User;
use App\Models\Gestion;
use App\Support\LogsBitacora;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
//use Intervention\Image\ImageManager;
//use Intervention\Image\Drivers\Gd\Driver;

class QrDocenteController extends Controller
{
    use LogsBitacora;
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:generar_qr_docente|Admin DTIC')->except(['miQr', 'descargarMiQr']);
    }
    
    /**
     * Panel administrativo: listado de QR generados
     */
    public function index(Request $request)
    {
        $query = DocenteQrToken::with(['docente:id,name,email', 'gestion:id_gestion,nombre'])
                                ->orderBy('fecha_generacion', 'desc');
        
        // Filtros
        if ($request->filled('id_gestion')) {
            $query->where('id_gestion', $request->id_gestion);
        }
        
        if ($request->filled('id_docente')) {
            $query->where('id_docente', $request->id_docente);
        }
        
        if ($request->filled('activo')) {
            $query->where('activo', $request->activo);
        }
        
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('docente', function($q) use ($buscar) {
                $q->where('name', 'like', "%{$buscar}%")
                  ->orWhere('email', 'like', "%{$buscar}%");
            });
        }
        
        $tokens = $query->paginate(20)->appends($request->query());
        
        // Para los filtros
        $gestiones = Gestion::orderBy('nombre', 'desc')->get();
        $docentes = User::role(['Docente', 'Coordinador', 'Director'])
                        ->orderBy('name')
                        ->get();
        
        return view('qr-docente.index', compact('tokens', 'gestiones', 'docentes'));
    }
    
    /**
     * Generar QR masivo para todos los docentes de una gestión
     */
    public function generarMasivo(Request $request)
    {
        $validated = $request->validate([
            'id_gestion' => 'required|exists:gestiones,id_gestion',
        ]);
        
        $gestion = Gestion::findOrFail($validated['id_gestion']);
        
        // Obtener todos los docentes (roles: Docente, Coordinador, Director)
        $docentes = User::role(['Docente', 'Coordinador', 'Director'])->get();
        
        $generados = 0;
        $actualizados = 0;
        
        foreach ($docentes as $docente) {
            $token = DocenteQrToken::where('id_docente', $docente->id)
                                   ->where('id_gestion', $gestion->id_gestion)
                                   ->first();
            
            if ($token) {
                if (!$token->activo) {
                    $token->update(['activo' => true]);
                    $actualizados++;
                }
            } else {
                DocenteQrToken::create([
                    'id_docente' => $docente->id,
                    'id_gestion' => $gestion->id_gestion,
                    'token' => DocenteQrToken::generarToken($docente->id, $gestion->id_gestion),
                    'activo' => true,
                    'fecha_generacion' => now(),
                ]);
                $generados++;
            }
        }
        
        // Bitácora
        $this->logBitacora($request, [
            'accion' => 'generar_masivo',
            'tabla_afectada' => 'docente_qr_tokens',
            'descripcion' => "Generación masiva de QR para gestión {$gestion->nombre}: {$generados} nuevos, {$actualizados} reactivados",
            'id_gestion' => $gestion->id_gestion,
            'exitoso' => true
        ]);
        
        return redirect()
            ->back()
            ->with('success', "QR generados: {$generados} nuevos, {$actualizados} reactivados para la gestión {$gestion->nombre}");
    }
    
    /**
     * Generar/obtener QR individual
     */
    public function generar(Request $request)
    {
        $validated = $request->validate([
            'id_docente' => 'required|exists:users,id',
            'id_gestion' => 'required|exists:gestiones,id_gestion',
        ]);
        
        $token = DocenteQrToken::obtenerOCrear(
            $validated['id_docente'],
            $validated['id_gestion']
        );
        
        $docente = User::findOrFail($validated['id_docente']);
        $gestion = Gestion::findOrFail($validated['id_gestion']);
        
        // Bitácora
        $this->logBitacora($request, [
            'accion' => 'generar',
            'tabla_afectada' => 'docente_qr_tokens',
            'registro_id' => $token->id_qr_token,
            'descripcion' => "QR generado para {$docente->name} en gestión {$gestion->nombre}",
            'id_gestion' => $gestion->id_gestion,
            'exitoso' => true
        ]);
        
        return redirect()
            ->route('qr-docente.ver', $token->id_qr_token)
            ->with('success', 'QR generado exitosamente');
    }
    
    /**
     * Ver QR individual con overlay personalizado
     */
    public function ver(DocenteQrToken $token)
    {
        $token->load(['docente.roles', 'gestion']);

        // 1. Generamos SOLO el SVG del QR (limpio, sin márgenes extra)
        // Usamos colores que combinen con el diseño Tailwind
        $qrSvg = QrCode::format('svg')
                    ->size(300) // Tamaño interno del QR
                    ->margin(0)
                    ->color(30, 41, 59) // Slate-800 para los módulos
                    ->backgroundColor(255, 255, 255)
                    ->errorCorrection('H')
                    ->generate($token->url_escaneo);

        // 2. El backend "cocina" la plantilla
        // Renderizamos la vista 'template' a una cadena de texto HTML
        $qrHtml = view('qr-docente.template', compact('token', 'qrSvg'))->render();

        // 3. Pasamos el HTML ya listo a la vista principal
        return view('qr-docente.ver', compact('token', 'qrHtml'));
    }
    
    /**
     * Generar QR con overlay personalizado PROFESIONAL (PNG con datos completos del docente)
     */
private function generarQrConOverlay(DocenteQrToken $token, int $qrSize = 500)
    {
        $token->load(['docente.roles', 'gestion']);
        
        // --- 1. GENERACIÓN DE QR (MÉTODO DE ARCHIVO TEMPORAL) ---
        // Definir un path temporal único
        $tempFile = storage_path('app/temp_qr_' . uniqid() . '.png');
        $qrImage = null; // Variable para la imagen GD
        
        try {
            // Usamos las opciones de PNG más simples y compatibles para GD
            QrCode::format('png')
                ->size($qrSize)
                ->margin(1)
                ->errorCorrection('H')
                ->generate($token->url_escaneo, $tempFile); // Guardar directamente en el archivo

            // Comprobar si el archivo fue creado
            if (!file_exists($tempFile)) {
                throw new \Exception("No se pudo crear el archivo QR temporal.");
            }

            // Leer la imagen desde el ARCHIVO (mucho más robusto que desde string)
            $qrImage = @imagecreatefrompng($tempFile);

            if (!$qrImage) {
                throw new \Exception("GD no pudo leer el archivo QR temporal (imagecreatefrompng falló).");
            }

        } catch (\Throwable $e) {
            // Fallback si todo falla
            $qrImage = imagecreatetruecolor($qrSize, $qrSize);
            imagefill($qrImage, 0, 0, imagecolorallocate($qrImage, 243, 244, 246));
            $errCol = imagecolorallocate($qrImage, 185, 28, 28);
            imagestring($qrImage, 5, 20, $qrSize/2, "ERROR QR (FILE)", $errCol);
            // Opcional: Loguear el error real para ti
            // \Log::error("Fallo QR definitivo: " . $e->getMessage());

        } finally {
            // Asegurarnos de borrar el archivo temporal pase lo que pase
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
        }

        // --- 2. CONFIGURACIÓN DEL CANVAS (Sin cambios) ---
        $padding = 60;
        $headerHeight = 100;
        $footerHeight = 160;
        $canvasWidth = $qrSize + ($padding * 2);
        $canvasHeight = $qrSize + $headerHeight + $footerHeight;

        $canvas = imagecreatetruecolor($canvasWidth, $canvasHeight);
        
        // Paleta
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $black = imagecolorallocate($canvas, 17, 24, 39);
        $grayDark = imagecolorallocate($canvas, 55, 65, 81);
        $grayLight = imagecolorallocate($canvas, 156, 163, 175);
        $accent = imagecolorallocate($canvas, 79, 70, 229);

        imagefill($canvas, 0, 0, $white);

        // --- 3. DIBUJAR ELEMENTOS (Sin cambios) ---
        $this->drawCenteredText($canvas, "FACULTAD DE CIENCIAS Y TECNOLOGÍA", 4, 40, $black, $canvasWidth);
        $this->drawCenteredText($canvas, "CONTROL DE ASISTENCIA", 2, 65, $grayLight, $canvasWidth);

        // Pegar el QR (que ahora SÍ existe)
        imagecopy($canvas, $qrImage, $padding, $headerHeight, 0, 0, $qrSize, $qrSize);

        // Footer
        $footerStartY = $headerHeight + $qrSize + 30;
        $nombreDocente = mb_strtoupper($token->docente->name, 'UTF-8');
        $this->drawCenteredText($canvas, $nombreDocente, 5, $footerStartY, $black, $canvasWidth);
        $this->drawCenteredText($canvas, $nombreDocente, 5, $footerStartY, $black, $canvasWidth, 1); // Falsa negrita

        $roles = $token->docente->roles->pluck('name')->join(' / ');
        $this->drawCenteredText($canvas, $roles, 4, $footerStartY + 35, $grayDark, $canvasWidth);

        $gestionTexto = "Gestión " . $token->gestion->nombre;
        $this->drawCenteredText($canvas, $gestionTexto, 2, $footerStartY + 70, $grayLight, $canvasWidth);

        imageline($canvas, $canvasWidth/2 - 30, $canvasHeight - 20, $canvasWidth/2 + 30, $canvasHeight - 20, $accent);

        // --- 4. EXPORTAR (Sin cambios) ---
        ob_start();
        imagepng($canvas, null, 9);
        $finalData = ob_get_clean();

        imagedestroy($qrImage);
        imagedestroy($canvas);

        return $finalData;
    }
    
    /**
     * Dibuja texto centrado en el canvas
     */
private function drawCenteredText($canvas, $text, $font, $y, $color, $canvasWidth, $offsetX = 0)
    {
        // Convertir UTF-8 a ISO-8859-1 si es posible, ya que imagestring de GD tiene soporte limitado de UTF-8
        if (function_exists('mb_convert_encoding')) {
             $text = mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
        }
        
        $textWidth = imagefontwidth($font) * strlen($text);
        $x = ($canvasWidth - $textWidth) / 2;
        
        imagestring($canvas, $font, (int)$x + $offsetX, (int)$y, $text, $color);
    }
    
    /**
     * Descargar QR como PDF con diseño personalizado
     */
    public function descargarPdf(DocenteQrToken $token)
    {
        $token->load(['docente.roles', 'gestion']);
        
        // Generar QR SVG con el mismo estilo del template
        $qrSvg = QrCode::format('svg')
                    ->size(300)
                    ->margin(0)
                    ->color(30, 41, 59)
                    ->backgroundColor(255, 255, 255)
                    ->errorCorrection('H')
                    ->generate($token->url_escaneo);
        
        // Renderizar el template HTML con todos los datos dinámicos
        $qrHtml = view('qr-docente.template', compact('token', 'qrSvg'))->render();
        
        $pdf = PDF::loadView('qr-docente.pdf', compact('token', 'qrHtml'));
        $filename = "QR_{$token->docente->name}_{$token->gestion->nombre}.pdf";
        $filename = str_replace([' ', '/'], ['_', '-'], $filename);
        return $pdf->download($filename);
    }
    
    /**
     * Descargar QR como imagen PNG con overlay
     */
    public function descargarImagen(DocenteQrToken $token)
    {
        $token->load(['docente.roles', 'gestion']);
        
        // Generar QR SVG
        $qrSvg = QrCode::format('svg')
                    ->size(300)
                    ->margin(0)
                    ->color(30, 41, 59)
                    ->backgroundColor(255, 255, 255)
                    ->errorCorrection('H')
                    ->generate($token->url_escaneo);
        
        // Renderizar el template HTML
        $qrHtml = view('qr-docente.template', compact('token', 'qrSvg'))->render();
        
        // Retornar como respuesta HTML embebida en una página completa para captura
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <link href='https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css' rel='stylesheet'>
            <style>
                body { margin: 0; padding: 20px; background: white; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
            </style>
        </head>
        <body>
            {$qrHtml}
        </body>
        </html>
        ";
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('X-Download-Filename', "QR_{$token->docente->name}_{$token->gestion->nombre}.png");
    }
    
    /**
     * Desactivar QR
     */
    public function desactivar(Request $request, DocenteQrToken $token)
    {
        $token->desactivar();
        
        // Bitácora
        $this->logBitacora($request, [
            'accion' => 'desactivar',
            'tabla_afectada' => 'docente_qr_tokens',
            'registro_id' => $token->id_qr_token,
            'descripcion' => "QR desactivado para {$token->docente->name} en gestión {$token->gestion->nombre}",
            'id_gestion' => $token->id_gestion,
            'exitoso' => true
        ]);
        
        return redirect()
            ->back()
            ->with('success', 'QR desactivado exitosamente');
    }
    
    /**
     * Activar/Reactivar QR
     */
    public function activar(Request $request, DocenteQrToken $token)
    {
        $token->update(['activo' => true]);
        
        // Bitácora
        $this->logBitacora($request, [
            'accion' => 'activar',
            'tabla_afectada' => 'docente_qr_tokens',
            'registro_id' => $token->id_qr_token,
            'descripcion' => "QR activado para {$token->docente->name} en gestión {$token->gestion->nombre}",
            'id_gestion' => $token->id_gestion,
            'exitoso' => true
        ]);
        
        return redirect()
            ->back()
            ->with('success', 'QR activado exitosamente');
    }
    
    /**
     * Regenerar token (rotación de seguridad)
     */
    public function regenerar(Request $request, DocenteQrToken $token)
    {
        $oldToken = $token->token;
        $token->regenerar();
        $this->logBitacora($request, [
            'accion' => 'regenerar',
            'tabla_afectada' => 'docente_qr_tokens',
            'registro_id' => $token->id_qr_token,
            'descripcion' => "QR regenerado para {$token->docente->name} en gestión {$token->gestion->nombre}",
            'id_gestion' => $token->id_gestion,
            'exitoso' => true,
            'metadata' => ['token_anterior' => substr($oldToken, 0, 10).'...']
        ]);
        return redirect()->back()->with('success', 'QR regenerado exitosamente. El código anterior ya no es válido.');
    }
    
    /**
     * Mi QR (vista del docente autenticado)
     */
    public function miQr(Request $request)
    {
        $docente = auth()->user();
        $gestion = Gestion::where('fecha_fin', '>=', now())
                          ->orderBy('fecha_inicio', 'desc')->first()
                   ?? Gestion::orderBy('fecha_inicio', 'desc')->first();

        if (!$gestion) {
            return view('qr-docente.mi-qr', ['error' => 'No hay gestiones registradas en el sistema']);
        }

        $token = DocenteQrToken::where('id_docente', $docente->id)
                               ->where('id_gestion', $gestion->id_gestion)->first();

        if (!$token) {
            $token = DocenteQrToken::create([
                'id_docente' => $docente->id,
                'id_gestion' => $gestion->id_gestion,
                'token' => DocenteQrToken::generarToken($docente->id, $gestion->id_gestion),
                'activo' => true,
                'fecha_generacion' => now(),
            ]);
            $this->logBitacora($request, [
                'accion' => 'auto_generar',
                'tabla_afectada' => 'docente_qr_tokens',
                'registro_id' => $token->id_qr_token,
                'descripcion' => "Docente generó su propio QR para gestión {$gestion->nombre}",
                'id_gestion' => $gestion->id_gestion,
                'exitoso' => true
            ]);
        }

        $token->load(['gestion']);
        // Mantenemos SVG para la vista web directa si funciona, o usa PNG si prefieres consistencia
        $qrSvg = QrCode::size(300)->margin(2)->generate($token->url_escaneo);

        return view('qr-docente.mi-qr', compact('token', 'qrSvg'));
    }
    
    /**
     * Descargar mi QR (docente autenticado)
     */
    public function descargarMiQr(Request $request)
    {
        $formato = $request->get('formato', 'pdf');
        $docente = auth()->user();
        $gestion = Gestion::where('fecha_fin', '>=', now())
                          ->orderBy('fecha_inicio', 'desc')->first()
                   ?? Gestion::orderBy('fecha_inicio', 'desc')->first();

        $token = DocenteQrToken::where('id_docente', $docente->id)
                               ->where('id_gestion', $gestion->id_gestion)->firstOrFail();

        if ($formato === 'png') {
            return $this->descargarImagen($token);
        }
        return $this->descargarPdf($token);
    }

    
    /**
     * Estadísticas de uso (admin)
     */
    public function estadisticas(Request $request)
    {
        $idGestion = $request->get('id_gestion');
        
        $query = DocenteQrToken::query();
        
        if ($idGestion) {
            $query->where('id_gestion', $idGestion);
        }
        
        $stats = [
            'total' => $query->count(),
            'activos' => (clone $query)->where('activo', true)->count(),
            'inactivos' => (clone $query)->where('activo', false)->count(),
            'usados' => (clone $query)->where('veces_usado', '>', 0)->count(),
            'nunca_usados' => (clone $query)->where('veces_usado', 0)->count(),
            'total_escaneos' => (clone $query)->sum('veces_usado'),
        ];
        
        $gestiones = Gestion::orderBy('nombre', 'desc')->get();
        $gestionActual = $idGestion ? Gestion::find($idGestion) : null;
        
        return view('qr-docente.estadisticas', compact('stats', 'gestiones', 'gestionActual'));
    }
}

