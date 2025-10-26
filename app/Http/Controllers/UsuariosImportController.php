<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\LogsBitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\CredencialesGeneradas;

class UsuariosImportController extends Controller
{
    use LogsBitacora;

    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:importar_usuarios']);
    }

    /**
     * Formulario de importación (CSV).
     * CSV esperado (UTF-8): ID,NOMBRE,CORREO,CONTRASEÑA
     */
    public function create(Request $request)
    {
        $id_gestion = $request->get('id_gestion'); // opcional (para bitácora)
        // La vista puede mostrar un botón/link a route('usuarios.import.plantilla')
        return view('usuarios.import.create', compact('id_gestion'));
    }

    /**
     * Descarga de la PLANTILLA CSV (cabeceras en español).
     */
    public function plantilla()
    {
        $filename = 'plantilla_usuarios.csv';
        $bom = "\xEF\xBB\xBF"; // BOM UTF-8 para Excel
        $csv = $bom . implode("\r\n", [
            'ID,NOMBRE,CORREO,CONTRASEÑA',
            ',María Pérez,maria.perez@ficct.uagrm.edu.bo,',       // sin contraseña -> se genera
            '12,Carlos Rojas,carlos.rojas@ficct.uagrm.edu.bo,',    // actualiza por ID
            ',Juan Gómez,juan.gomez@ficct.uagrm.edu.bo,Pass!2025', // con contraseña explícita
        ]);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    /**
     * Procesa el CSV y crea/actualiza usuarios por lote.
     * Cabeceras esperadas: ID,NOMBRE,CORREO,CONTRASEÑA (CONTRASEÑA opcional).
     * Si CONTRASEÑA está vacía al crear: se genera una temporal y se envía por email (CU-06).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'archivo'    => ['required','file','mimes:csv,txt','max:20480'], // 20MB
            'id_gestion' => ['sometimes','integer'],
        ]);

        $path  = $request->file('archivo')->getRealPath();
        $delim = $this->detectarDelimitador($path);
        $fh    = fopen($path, 'r');

        if (!$fh) {
            return back()->withErrors(['archivo' => 'No se pudo leer el archivo subido.'])->withInput();
        }

        // Lee cabecera
        $header = $this->leerLineaCsv($fh, $delim);
        if (!$header) {
            return back()->withErrors(['archivo' => 'El archivo está vacío o sin cabecera.'])->withInput();
        }

        // Mapeo de cabeceras a índices (español como prioridad)
        $map = $this->mapearCabecera($header); // indices para: id, nombre, correo, contraseña
        if (!isset($map['correo']) && !isset($map['id'])) {
            return back()->withErrors(['archivo' => 'La cabecera debe incluir al menos "CORREO" o "ID".'])->withInput();
        }
        if (!isset($map['nombre'])) {
            return back()->withErrors(['archivo' => 'La cabecera debe incluir "NOMBRE".'])->withInput();
        }

        $creados = 0; $actualizados = 0; $omitidos = 0; $notificados = 0; $errores = [];
        $linea = 1; // cabecera

        while (($row = $this->leerLineaCsv($fh, $delim)) !== false) {
            $linea++;

            // Extrae valores por índice
            $id          = $this->val($row, $map['id'] ?? null);
            $nombre      = $this->val($row, $map['nombre'] ?? null);
            $correo      = $this->val($row, $map['correo'] ?? null);
            $contrasena  = $this->val($row, $map['contraseña'] ?? null);

            // Normalizaciones
            $nombre = $nombre ? trim($nombre) : null;
            $correo = $correo ? strtolower(trim($correo)) : null;

            // Validaciones mínimas
            if (empty($id) && empty($correo)) {
                $omitidos++; $errores[] = "Línea {$linea}: sin 'ID' ni 'CORREO'.";
                continue;
            }
            if (empty($nombre)) {
                $omitidos++; $errores[] = "Línea {$linea}: falta 'NOMBRE'.";
                continue;
            }
            if (!empty($correo) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $omitidos++; $errores[] = "Línea {$linea}: CORREO inválido '{$correo}'.";
                continue;
            }

            try {
                DB::beginTransaction();

                /** @var \App\Models\User|null $user */
                $user = null;

                if (!empty($id) && ctype_digit((string)$id)) {
                    $user = User::find((int)$id);
                }
                if (!$user && !empty($correo)) {
                    $user = User::where('email', $correo)->first();
                }

                $debeNotificar = false;
                $passwordPlano = null;

                if ($user) {
                    // Actualiza nombre; si viene CONTRASEÑA explícita, también la cambia
                    $user->name = $nombre;
                    if (!empty($contrasena)) {
                        $user->password = $contrasena; // model cast 'hashed' la encripta
                        // Por seguridad, NO enviamos email cuando la proporcionan explícitamente
                    }
                    $user->save();
                    $actualizados++;
                } else {
                    // Crear nuevo. Si no hay contraseña, generar temporal y NOTIFICAR.
                    $user = new User();
                    $user->name  = $nombre;
                    $user->email = $correo; // requerido para alta

                    if (!empty($contrasena)) {
                        $user->password = $contrasena;
                    } else {
                        $passwordPlano  = Str::random(12);
                        $user->password = $passwordPlano;
                        $debeNotificar  = !empty($correo); // solo si se tiene correo válido
                    }

                    $user->save();
                    $creados++;

                    if ($debeNotificar) {
                        Mail::to($user->email)->send(new CredencialesGeneradas(
                            nombre: $user->name,
                            email:  $user->email,
                            passwordPlano: $passwordPlano,
                            urlLogin: route('login', absolute: false) // si tienes route('login')
                        ));
                        $notificados++;
                    }
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                $omitidos++;
                $errores[] = "Línea {$linea}: ".$e->getMessage();
            }
        }

        fclose($fh);

        // Bitácora del proceso
        $this->logBitacora($request, [
            'accion'         => 'IMPORTAR_USUARIOS',
            'modulo'         => 'USUARIOS',
            'tabla_afectada' => 'users',
            'registro_id'    => null,
            'descripcion'    => "Importación de usuarios por lote (CSV).",
            'id_gestion'     => $data['id_gestion'] ?? null,
            'metadata'       => [
                'resumen'  => compact('creados','actualizados','omitidos','notificados'),
                'errores'  => array_slice($errores, 0, 50),
                'archivo'  => [
                    'mime'       => $request->file('archivo')->getClientMimeType(),
                    'size_bytes' => $request->file('archivo')->getSize(),
                    'delimitador'=> $delim,
                ],
                'cabeceras' => $header,
            ],
        ]);

        // Vista de resultado (puedes mostrar tabla con errores, etc.)
        $reporte = compact('creados','actualizados','omitidos','notificados','errores');
        return view('usuarios.import.result', compact('reporte'));
    }

    /* ========================= Helpers ========================= */

    /** Detecta delimitador probable: ',', ';' o "\t". */
    private function detectarDelimitador(string $path): string
    {
        $sample = file_get_contents($path, false, null, 0, 4096) ?: '';
        $cands = [',' => substr_count($sample, ','), ';' => substr_count($sample, ';'), "\t" => substr_count($sample, "\t")];
        arsort($cands);
        $top = array_key_first($cands);
        return $top ?: ',';
    }

    /** Lee una línea CSV con fgetcsv manejando BOM. */
    private function leerLineaCsv($fh, string $delim)
    {
        $row = fgetcsv($fh, 0, $delim);
        if ($row === false) return false;

        // Remover BOM UTF-8 del primer campo si existe
        if (isset($row[0])) {
            $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string)$row[0]);
        }
        return $row;
    }

    /**
     * Mapea cabeceras a índices con prioridad a español:
     *  - id → ID, (admite: "id","user_id")
     *  - nombre → NOMBRE, (admite: "name")
     *  - correo → CORREO, (admite: "email","correo_e")
     *  - contraseña → CONTRASEÑA, (admite: "contrasena","password","pass")
     */
    private function mapearCabecera(array $header): array
    {
        $map = [];
        foreach ($header as $i => $col) {
            $key = $this->normalizarCabecera($col);

            if (in_array($key, ['id','user_id'], true))                     $map['id'] = $i;
            if (in_array($key, ['nombre','name'], true))                    $map['nombre'] = $i;
            if (in_array($key, ['correo','email','correo_e'], true))        $map['correo'] = $i;
            if (in_array($key, ['contraseña','contrasena','password','pass'], true))
                $map['contraseña'] = $i;
        }
        return $map;
    }

    /** Normaliza nombres de columna: minúsculas, sin acentos, guiones/espacios → '_' */
    private function normalizarCabecera(?string $s): string
    {
        $s = strtolower(trim((string)$s));
        $s = strtr($s, [
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n','ü'=>'u',
        ]);
        $s = preg_replace('/[\s\.\-]+/','_',$s);
        return $s ?? '';
    }

    /** Obtiene valor trimmed por índice; null si no existe. */
    private function val(array $row, ?int $idx): ?string
    {
        if ($idx === null) return null;
        return array_key_exists($idx, $row) ? trim((string)$row[$idx]) : null;
    }
}
