<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\LogsBitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\CredencialesGeneradas;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PlantillaUsuariosExport;
use App\Imports\UsuariosHeadingImport;
use Spatie\Permission\Models\Role;

class UsuariosImportController extends Controller
{
    use LogsBitacora;

    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:importar_usuarios']);
    }

    public function create(Request $request)
    {
        $id_gestion = $request->get('id_gestion');
        return view('usuarios.import.create', compact('id_gestion'));
    }

    /**
     * NUEVO: Descarga plantilla en XLSX con casillas (sin datos).
     */
    public function plantilla()
    {
        return Excel::download(new PlantillaUsuariosExport, 'plantilla_usuarios.xlsx');
    }

    /**
     * Importa XLSX/XLS/CSV con encabezado: ID, NOMBRE, CORREO, CONTRASEÑA, ROLES
     * - CONTRASEÑA vacía al crear -> genera temporal y notifica (si hay correo).
     * - ROLES (opcional): separar por ';'. Si se proporcionan => reemplaza roles del usuario.
     */
public function store(Request $request)
{
    $data = $request->validate([
        'archivo'    => ['required','file','mimes:xlsx,xls,csv,txt','max:20480'],
        'id_gestion' => ['sometimes','integer'],
    ]);

    $file = $request->file('archivo');
    $ext  = strtolower($file->getClientOriginalExtension());

    // ← Usaremos este arreglo y lo pasaremos por referencia
    $contadores = ['creados' => 0, 'actualizados' => 0, 'omitidos' => 0, 'notificados' => 0];
    $errores    = [];

    try {
        if (in_array($ext, ['xlsx','xls'])) {
            // ---- Excel con encabezado ----
            $sheets = Excel::toCollection(new UsuariosHeadingImport, $file);
            $rows   = $sheets->first() ?? collect();

            $linea  = 1; // encabezado
            foreach ($rows as $row) {
                $linea++;

                $id         = $this->nv($row, ['id','user_id']);
                $nombre     = $this->nv($row, ['nombre','name']);
                $correo     = $this->nv($row, ['correo','email','correo_e']);
                $correo     = $correo !== null ? strtolower((string)$correo) : null;
                $contrasena = $this->nv($row, ['contrasena','contraseña','password','pass']);
                $rolesTxt   = $this->nv($row, ['roles']);

                if (empty($id) && empty($correo)) { $contadores['omitidos']++; $errores[] = "Fila {$linea}: sin 'ID' ni 'CORREO'."; continue; }
                if (empty($nombre)) { $contadores['omitidos']++; $errores[] = "Fila {$linea}: falta 'NOMBRE'."; continue; }
                if (!empty($correo) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) { $contadores['omitidos']++; $errores[] = "Fila {$linea}: correo inválido '{$correo}'."; continue; }

                // ← PASA UNA VARIABLE por referencia
                $this->upsertUsuario(
                    id: $id,
                    nombre: $nombre,
                    correo: $correo,
                    contrasena: $contrasena,
                    rolesTxt: $rolesTxt,
                    contadores: $contadores,
                    errores: $errores,
                    linea: $linea
                );
            }
        } else {
            // ---- CSV/TXT ----
            $path  = $file->getRealPath();
            $delim = $this->detectarDelimitador($path);
            $fh    = fopen($path, 'r');
            if (!$fh) {
                return back()->withErrors(['archivo' => 'No se pudo leer el archivo subido.'])->withInput();
            }
            $header = $this->leerLineaCsv($fh, $delim);
            if (!$header) {
                return back()->withErrors(['archivo' => 'El archivo está vacío o sin cabecera.'])->withInput();
            }
            $map = $this->mapearCabecera($header);

            if (!isset($map['correo']) && !isset($map['id'])) {
                return back()->withErrors(['archivo' => 'La cabecera debe incluir al menos "CORREO" o "ID".'])->withInput();
            }
            if (!isset($map['nombre'])) {
                return back()->withErrors(['archivo' => 'La cabecera debe incluir "NOMBRE".'])->withInput();
            }

            $linea = 1;
            while (($row = $this->leerLineaCsv($fh, $delim)) !== false) {
                $linea++;

                $id         = $this->val($row, $map['id'] ?? null);
                $nombre     = $this->val($row, $map['nombre'] ?? null);
                $correo     = $this->val($row, $map['correo'] ?? null);
                $correo     = $correo !== null ? strtolower(trim((string)$correo)) : null;
                $contrasena = $this->val($row, $map['contraseña'] ?? null);
                $rolesTxt   = $this->val($row, $map['roles'] ?? null);

                $nombre = $nombre ? trim($nombre) : null;

                if (empty($id) && empty($correo)) { $contadores['omitidos']++; $errores[] = "Línea {$linea}: sin 'ID' ni 'CORREO'."; continue; }
                if (empty($nombre)) { $contadores['omitidos']++; $errores[] = "Línea {$linea}: falta 'NOMBRE'."; continue; }
                if (!empty($correo) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) { $contadores['omitidos']++; $errores[] = "Línea {$linea}: CORREO inválido '{$correo}'."; continue; }

                // ← PASA UNA VARIABLE por referencia
                $this->upsertUsuario(
                    id: $id,
                    nombre: $nombre,
                    correo: $correo,
                    contrasena: $contrasena,
                    rolesTxt: $rolesTxt,
                    contadores: $contadores,
                    errores: $errores,
                    linea: $linea
                );
            }

            fclose($fh);
        }
    } catch (\Throwable $e) {
        return back()->withErrors(['archivo' => $e->getMessage()])->withInput();
    }

    // Bitácora (usa los contadores ya mutados)
    $this->logBitacora($request, [
        'accion'         => 'IMPORTAR_USUARIOS',
        'modulo'         => 'USUARIOS',
        'tabla_afectada' => 'users',
        'registro_id'    => null,
        'descripcion'    => "Importación de usuarios por lote (Excel/CSV).",
        'id_gestion'     => $data['id_gestion'] ?? null,
        'metadata'       => [
            'resumen'  => $contadores,
            'errores'  => array_slice($errores, 0, 50),
            'archivo'  => [
                'mime'       => $file->getClientMimeType(),
                'size_bytes' => $file->getSize(),
                'ext'        => $ext,
            ],
        ],
    ]);

    $reporte = array_merge($contadores, ['errores' => $errores]);
    return view('usuarios.import.result', compact('reporte'));
}


    /* ======================= Helpers ======================= */

    /** Normaliza/obtiene valor por múltiples posibles keys en colecciones Excel. */
    private function nv($row, array $keys): ?string
    {
        foreach ($keys as $k) {
            if (isset($row[$k]) && $row[$k] !== '') {
                return is_string($row[$k]) ? trim($row[$k]) : (string)$row[$k];
            }
        }
        return null;
    }

    /** Upsert + asignación opcional de roles (ROLES separados por ';') */
    private function upsertUsuario($id, $nombre, $correo, $contrasena, $rolesTxt, array &$contadores, array &$errores, int $linea): void
    {
        try {
            DB::beginTransaction();

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
                $user->name = $nombre;
                if (!empty($contrasena)) {
                    $user->password = $contrasena; // cast hashed
                }
                $user->save();
                $contadores['actualizados']++;
            } else {
                $user = new User();
                $user->name  = $nombre;
                $user->email = $correo;

                if (!empty($contrasena)) {
                    $user->password = $contrasena;
                } else {
                    $passwordPlano  = Str::random(12);
                    $user->password = $passwordPlano;
                    $debeNotificar  = !empty($correo);
                }

                $user->save();
                $contadores['creados']++;

                if ($debeNotificar) {
                    Mail::to($user->email)->send(new CredencialesGeneradas(
                        nombre: $user->name,
                        email:  $user->email,
                        passwordPlano: $passwordPlano,
                        urlLogin: route('login', absolute: false)
                    ));
                    $contadores['notificados']++;
                }
            }

            // ROLES opcional (si viene -> REEMPLAZA roles del usuario)
            if (!empty($rolesTxt)) {
                $nombres = array_values(array_filter(array_map('trim', preg_split('/[;,\|]+/', (string)$rolesTxt))));
                if (!empty($nombres)) {
                    // Solo roles existentes (no creamos nuevos aquí)
                    $validos = Role::query()->whereIn('name', $nombres)->pluck('name')->all();
                    $user->syncRoles($validos);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $contadores['omitidos']++;
            $errores[] = "Fila {$linea}: ".$e->getMessage();
        }
    }

    /** ====== Tus helpers CSV originales (con soporte a 'roles') ====== */

    private function detectarDelimitador(string $path): string
    {
        $sample = file_get_contents($path, false, null, 0, 4096) ?: '';
        $cands = [',' => substr_count($sample, ','), ';' => substr_count($sample, ';'), "\t" => substr_count($sample, "\t")];
        arsort($cands);
        $top = array_key_first($cands);
        return $top ?: ',';
    }

    private function leerLineaCsv($fh, string $delim)
    {
        $row = fgetcsv($fh, 0, $delim);
        if ($row === false) return false;
        if (isset($row[0])) {
            $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string)$row[0]);
        }
        return $row;
    }

    private function mapearCabecera(array $header): array
    {
        $map = [];
        foreach ($header as $i => $col) {
            $key = $this->normalizarCabecera($col);
            if (in_array($key, ['id','user_id'], true))                     $map['id'] = $i;
            if (in_array($key, ['nombre','name'], true))                    $map['nombre'] = $i;
            if (in_array($key, ['correo','email','correo_e'], true))        $map['correo'] = $i;
            if (in_array($key, ['contraseña','contrasena','password','pass'], true)) $map['contraseña'] = $i;
            if (in_array($key, ['roles','rol'], true))                      $map['roles'] = $i; // NUEVO
        }
        return $map;
    }

    private function normalizarCabecera(?string $s): string
    {
        $s = strtolower(trim((string)$s));
        $s = strtr($s, [
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n','ü'=>'u',
        ]);
        $s = preg_replace('/[\s\.\-]+/','_',$s);
        return $s ?? '';
    }

    private function val(array $row, ?int $idx): ?string
    {
        if ($idx === null) return null;
        return array_key_exists($idx, $row) ? trim((string)$row[$idx]) : null;
    }
}
