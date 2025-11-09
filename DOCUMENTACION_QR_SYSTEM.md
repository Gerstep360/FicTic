# 📋 DOCUMENTACIÓN COMPLETA - SISTEMA QR DE ASISTENCIA

## 🎯 RESUMEN DEL SISTEMA

Sistema de generación de códigos QR personalizados para control de asistencia docente con overlay profesional que incluye:
- ✅ Nombre completo del docente
- ✅ Email institucional
- ✅ Roles/Perfiles asignados
- ✅ Facultad (FCYT)
- ✅ Gestión académica
- ✅ Fecha de generación
- ✅ Sistema institucional

---

## 📍 RUTAS PRINCIPALES

### **1. Panel Administrativo - Listado de QR**
**Ruta:** `GET /admin/qr-docente`  
**Nombre:** `qr-docente.index`  
**Controlador:** `QrDocenteController@index`  
**Archivo:** `app/Http/Controllers/QrDocenteController.php` (línea 21)  
**Vista:** `resources/views/qr-docente/index.blade.php`  
**Descripción:** Muestra tabla con todos los códigos QR generados, con filtros por gestión, docente, estado y búsqueda.  
**Permisos:** `generar_qr_docente` o `Admin DTIC`

---

### **2. Ver QR Individual (con overlay)**
**Ruta:** `GET /admin/qr-docente/{token}`  
**Nombre:** `qr-docente.ver`  
**Controlador:** `QrDocenteController@ver`  
**Archivo:** `app/Http/Controllers/QrDocenteController.php` (línea 156)  
**Vista:** `resources/views/qr-docente/ver.blade.php`  
**Descripción:** Muestra el código QR completo con overlay personalizado (400x400px) con todos los datos del docente.  
**Método clave:** `generarQrConOverlay($token, 400)`  
**Datos mostrados:**
- Facultad de Ciencias y Tecnología
- "CÓDIGO QR DE ASISTENCIA"
- Nombre del docente (MAYÚSCULAS, destacado)
- Email del docente
- Roles del docente
- Gestión académica
- Fecha de generación
- Sistema FicTic

---

### **3. Descargar QR como PDF**
**Ruta:** `GET /admin/qr-docente/{token}/pdf`  
**Nombre:** `qr-docente.descargar-pdf`  
**Controlador:** `QrDocenteController@descargarPdf`  
**Archivo:** `app/Http/Controllers/QrDocenteController.php` (línea 261)  
**Vista:** `resources/views/qr-docente/pdf.blade.php`  
**Descripción:** Genera PDF para imprimir con QR personalizado (400x400px).  
**Método clave:** `generarQrConOverlay($token, 400)` → convierte a base64 → embebe en PDF  
**Biblioteca:** `Barryvdh\DomPDF\Facade\Pdf`  
**Nombre archivo:** `QR_{nombre_docente}_{gestion}.pdf`

---

### **4. Descargar QR como PNG**
**Ruta:** `GET /admin/qr-docente/{token}/png`  
**Nombre:** `qr-docente.descargar-imagen`  
**Controlador:** `QrDocenteController@descargarImagen`  
**Archivo:** `app/Http/Controllers/QrDocenteController.php` (línea 274)  
**Descripción:** Descarga imagen PNG de alta resolución (600x600px) con overlay completo.  
**Método clave:** `generarQrConOverlay($token, 600)`  
**Tipo MIME:** `image/png`  
**Nombre archivo:** `QR_{nombre_docente}_{gestion}.png`

---

### **5. Mi QR (Vista del Docente)**
**Ruta:** `GET /mi-qr`  
**Nombre:** `qr-docente.mi-qr`  
**Controlador:** `QrDocenteController@miQr`  
**Archivo:** `app/Http/Controllers/QrDocenteController.php` (línea 363)  
**Vista:** `resources/views/qr-docente/mi-qr.blade.php`  
**Descripción:** Vista pública donde cada docente puede ver y descargar su propio QR.  
**Acceso:** Cualquier usuario autenticado  
**Funcionalidad:** Auto-genera QR si no existe para la gestión actual

---

### **6. Descargar Mi QR (Docente)**
**Ruta:** `GET /mi-qr/descargar`  
**Nombre:** `qr-docente.descargar-mi-qr`  
**Controlador:** `QrDocenteController@descargarMiQr`  
**Archivo:** `app/Http/Controllers/QrDocenteController.php` (línea 421)  
**Parámetro:** `formato=pdf` o `formato=png`  
**Descripción:** Permite al docente descargar su propio QR en formato PDF o PNG.

---

### **7. Generación Masiva**
**Ruta:** `POST /admin/qr-docente/masivo`  
**Nombre:** `qr-docente.generar-masivo`  
**Controlador:** `QrDocenteController@generarMasivo`  
**Archivo:** `app/Http/Controllers/QrDocenteController.php` (línea 67)  
**Descripción:** Genera QR para todos los docentes de una gestión específica.  
**Input:** `id_gestion`  
**Funcionalidad:** Crea o actualiza tokens para todos los usuarios con roles: Docente, Coordinador, Director

---

### **8. Generar QR Individual**
**Ruta:** `POST /admin/qr-docente/generar`  
**Nombre:** `qr-docente.generar`  
**Controlador:** `QrDocenteController@generar`  
**Archivo:** `app/Http/Controllers/QrDocenteController.php` (línea 120)  
**Input:** `id_docente`, `id_gestion`  
**Descripción:** Genera o actualiza QR para un docente específico en una gestión.

---

### **9. Activar QR**
**Ruta:** `PATCH /admin/qr-docente/{token}/activar`  
**Nombre:** `qr-docente.activar`  
**Controlador:** `QrDocenteController@activar`  
**Archivo:** `app/Http/Controllers/QrDocenteController.php` (línea 318)  
**Descripción:** Reactiva un código QR previamente desactivado.

---

### **10. Desactivar QR**
**Ruta:** `PATCH /admin/qr-docente/{token}/desactivar`  
**Nombre:** `qr-docente.desactivar`  
**Controlador:** `QrDocenteController@desactivar`  
**Archivo:** `app/Http/Controllers/QrDocenteController.php` (línea 296)  
**Descripción:** Desactiva temporalmente un código QR (sin eliminarlo).

---

### **11. Regenerar Token**
**Ruta:** `PATCH /admin/qr-docente/{token}/regenerar`  
**Nombre:** `qr-docente.regenerar`  
**Controlador:** `QrDocenteController@regenerar`  
**Archivo:** `app/Http/Controllers/QrDocenteController.php` (línea 340)  
**Descripción:** Genera un nuevo token de seguridad (el anterior deja de funcionar).

---

### **12. Estadísticas**
**Ruta:** `GET /admin/qr-docente/estadisticas`  
**Nombre:** `qr-docente.estadisticas`  
**Controlador:** `QrDocenteController@estadisticas`  
**Archivo:** `app/Http/Controllers/QrDocenteController.php` (línea 445)  
**Descripción:** Dashboard con métricas de uso de códigos QR.

---

## 🎨 MÉTODO PRINCIPAL: `generarQrConOverlay()`

**Ubicación:** `app/Http/Controllers/QrDocenteController.php` (línea 170)  
**Visibilidad:** `private`  
**Parámetros:**
- `DocenteQrToken $token` - Token con relaciones cargadas
- `int $size = 500` - Tamaño del QR en píxeles

### **Proceso de Generación:**

1. **Carga de datos dinámicos:**
   ```php
   $token->load(['docente.roles', 'gestion']);
   $facultad = "FACULTAD DE CIENCIAS Y TECNOLOGIA";
   $nombreDocente = strtoupper($token->docente->name);
   $emailDocente = $token->docente->email;
   $roles = $token->docente->roles->pluck('name')->join(', ');
   ```

2. **Generación del QR base:**
   - Formato: SVG convertido a GD
   - Error correction: `H` (30% tolerancia)
   - Margen: 2px
   - Tamaño: variable (400px para vista/PDF, 600px para descarga)

3. **Creación del canvas:**
   - Header: 140px (con gradiente púrpura)
   - QR: tamaño variable
   - Footer: 80px (con información de gestión)
   - Total: ~720px de altura

4. **Diseño del overlay:**

   **HEADER (140px):**
   - Gradiente vertical: Purple 900 → Purple 500
   - Línea superior decorativa (3px, purple light)
   - Textos (centrados):
     * "FACULTAD DE CIENCIAS Y TECNOLOGIA" (pequeño, accent)
     * "CÓDIGO QR DE ASISTENCIA" (mediano, blanco)
     * Separador decorativo horizontal
     * **NOMBRE DEL DOCENTE** (grande, bold, blanco)
     * Email del docente (pequeño, accent)
     * Roles del docente (pequeño, blanco)

   **SOMBRA (8px):**
   - Degradado suave entre header y QR
   - Transparencia gradual

   **FOOTER (80px):**
   - Gradiente sutil de gris claro
   - Línea superior (purple brand)
   - Textos (centrados):
     * Nombre de la gestión (mediano, purple brand)
     * Fecha de generación (pequeño, gris medio)
     * "Sistema FicTic - Control de Asistencia" (pequeño, gris oscuro)

   **DETALLES:**
   - Borde doble (purple brand + purple light)
   - Círculos decorativos en las 4 esquinas

5. **Exportación:**
   - Formato: PNG
   - Compresión: nivel 9 (máxima calidad)
   - Limpieza de memoria automática

---

## 📁 ARCHIVOS CLAVE

### **Controlador:**
`app/Http/Controllers/QrDocenteController.php`
- Métodos: 12 públicos + 2 privados
- Líneas: ~485
- Dependencias:
  - `SimpleSoftwareIO\QrCode\Facades\QrCode`
  - `Barryvdh\DomPDF\Facade\Pdf`
  - Funciones GD de PHP (imagecreatetruecolor, imagepng, etc.)

### **Rutas:**
`routes/QrDocente/QrDocente.php`
- Total rutas: 12
- Prefijo: `/admin/qr-docente`
- Middleware: `auth`, `permission:generar_qr_docente|Admin DTIC`

### **Vistas:**
1. `resources/views/qr-docente/index.blade.php` - Listado administrativo
2. `resources/views/qr-docente/ver.blade.php` - Ver QR individual
3. `resources/views/qr-docente/pdf.blade.php` - Template PDF
4. `resources/views/qr-docente/mi-qr.blade.php` - Vista del docente

### **Modelo:**
`app/Models/DocenteQrToken.php`
- Relaciones:
  - `docente()` → User (belongsTo)
  - `gestion()` → Gestion (belongsTo)
- Métodos estáticos:
  - `obtenerOCrear($docenteId, $gestionId)`
- Métodos de instancia:
  - `desactivar()`
  - `regenerar()`

---

## 🔧 CONFIGURACIÓN

### **Dependencias en composer.json:**
```json
{
  "simplesoftwareio/simple-qrcode": "^4.2",
  "barryvdh/laravel-dompdf": "^3.1"
}
```

### **Extensiones PHP requeridas:**
- ✅ GD Library (imagecreatetruecolor, imagepng, etc.)
- ✅ mbstring
- ❌ Imagick (NO requerido - evitado intencionalmente)

---

## 🎯 DATOS DINÁMICOS OBTENIDOS

El sistema obtiene automáticamente:

1. **Del Token:**
   - `token` - Hash único de seguridad
   - `url_escaneo` - URL completa del QR
   - `activo` - Estado (activo/inactivo)
   - `fecha_generacion` - Timestamp de creación
   - `veces_usado` - Contador de escaneos
   - `ultimo_uso` - Último escaneo

2. **Del Docente (User):**
   - `name` - Nombre completo
   - `email` - Email institucional
   - `roles` - Colección de roles (Docente, Coordinador, Director, etc.)

3. **De la Gestión:**
   - `nombre` - Ej: "2024-2025"
   - `fecha_inicio` - Fecha de inicio
   - `fecha_fin` - Fecha de fin

4. **Constantes del Sistema:**
   - Facultad: "FACULTAD DE CIENCIAS Y TECNOLOGIA"
   - Sistema: "Sistema FicTic - Control de Asistencia"

---

## 🚀 FLUJO DE GENERACIÓN

### **Flujo Administrativo:**
1. Admin accede a `/admin/qr-docente`
2. Filtra o busca docente
3. Click en "Ver QR" → `/admin/qr-docente/{id}`
4. Se ejecuta `ver()` → `generarQrConOverlay($token, 400)`
5. Muestra QR con overlay completo
6. Puede descargar:
   - PDF: `/admin/qr-docente/{id}/pdf` → `descargarPdf()`
   - PNG: `/admin/qr-docente/{id}/png` → `descargarImagen()`

### **Flujo del Docente:**
1. Docente accede a `/mi-qr`
2. Se ejecuta `miQr()`:
   - Busca QR existente para gestión actual
   - Si no existe, lo crea automáticamente
3. Muestra QR personalizado
4. Puede descargar:
   - PDF: `/mi-qr/descargar?formato=pdf`
   - PNG: `/mi-qr/descargar?formato=png`

---

## 🎨 PALETA DE COLORES

```php
// Header
Purple Dark:  #581C87 (rgb 88,28,135)   - Gradiente inicio
Purple Brand: #6B46C1 (rgb 107,70,193)  - Color principal
Purple Light: #A78BFA (rgb 167,139,250) - Gradiente fin / Accent

// Footer
Gray Light: #F1F5F9 (rgb 241,245,249) - Fondo
Gray Medium: #64748B (rgb 100,116,139) - Texto secundario
Gray Dark:   #1E293B (rgb 30,41,59)    - Texto principal

// Decorativos
Blue Accent: #3B82F6 (rgb 59,130,246) - Links/Botones
White:       #FFFFFF (rgb 255,255,255) - Texto sobre púrpura
Black:       #000000 (rgb 0,0,0)       - Sombras
```

---

## 📊 DIMENSIONES

```
+--------------------------------+
| Header: 140px                  |
|  - Gradiente púrpura           |
|  - Facultad                    |
|  - Título                      |
|  - Separador                   |
|  - NOMBRE DOCENTE              |
|  - Email                       |
|  - Roles                       |
+--------------------------------+
| Sombra: 8px (transparente)     |
+--------------------------------+
|                                |
|   QR Code: 500x500px           |
|   (variable según parámetro)   |
|                                |
+--------------------------------+
| Footer: 80px                   |
|  - Gestión                     |
|  - Fecha generación            |
|  - Sistema FicTic              |
+--------------------------------+

Total: 500x728px (aprox)
```

---

## 🔐 PERMISOS

- **Admin:** Permiso `generar_qr_docente` o rol `Admin DTIC`
- **Docente:** Acceso autenticado a `/mi-qr` (sin permisos especiales)

---

## 📌 NOTAS IMPORTANTES

1. ✅ **Sin dependencias externas:** No requiere Imagick
2. ✅ **Alta calidad:** PNG compression level 9
3. ✅ **Error correction:** QR con 30% de tolerancia a daños
4. ✅ **Responsive:** Funciona en cualquier dispositivo
5. ✅ **Datos dinámicos:** Todo se obtiene de la base de datos
6. ✅ **Bitácora automática:** Registra todas las acciones
7. ✅ **PDF embebido:** Base64 para evitar problemas de rutas
8. ✅ **Seguridad:** Tokens únicos con hash SHA-256

---

## 🐛 TROUBLESHOOTING

### Error: "Class not found Pdf"
**Solución:** `composer require barryvdh/laravel-dompdf`

### Error: "imagecreatetruecolor() not found"
**Solución:** Instalar extensión GD en PHP

### QR no se genera
**Solución:** Verificar permisos de escritura en storage/

### Overlay se ve mal
**Solución:** Sistema usa fuentes GD integradas, no requiere configuración adicional

---

**Última actualización:** 9 de noviembre de 2025  
**Versión del sistema:** Laravel 12.36.1 | PHP 8.4.12
