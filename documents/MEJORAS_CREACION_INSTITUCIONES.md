# 🏢 MEJORAS EN CREACIÓN DE INSTITUCIONES

## 📋 RESUMEN DE CAMBIOS

Se implementaron 3 mejoras importantes en el proceso de creación de nuevas instituciones (`dev-crear-nueva-bd-v2.php`):

---

## ✅ CAMBIOS IMPLEMENTADOS

### 1️⃣ **Asignación Automática de TODOS los Módulos Activos**

**Antes:**
```php
// Solo asignaba 5 módulos específicos (hardcoded)
$sqlModulos = "INSERT INTO instituciones_modulos (ipmod_institucion,ipmod_modulo) 
               VALUES ($idInsti,4),($idInsti,5),($idInsti,7),($idInsti,17),($idInsti,22)";
```

**Después:**
```php
// Consulta TODOS los módulos activos y los relaciona automáticamente
$consultaModulos = mysqli_query($conexion, "SELECT mod_id FROM modulos WHERE mod_estado = 1");
if ($consultaModulos && mysqli_num_rows($consultaModulos) > 0) {
    $valoresModulos = [];
    while ($modulo = mysqli_fetch_array($consultaModulos, MYSQLI_BOTH)) {
        $valoresModulos[] = "($idInsti, ".$modulo['mod_id'].")";
    }
    
    if (!empty($valoresModulos)) {
        $sqlModulos = "INSERT INTO instituciones_modulos (ipmod_institucion, ipmod_modulo) 
                       VALUES " . implode(',', $valoresModulos);
        mysqli_query($conexion, $sqlModulos);
    }
}
```

**Ventajas:**
- ✅ No requiere actualización manual al agregar nuevos módulos
- ✅ Todas las instituciones nuevas tienen acceso a todos los módulos desde el inicio
- ✅ Dinámico y escalable

---

### 2️⃣ **Campo Personalizado para Usuario de Acceso (`uss_usuario`)**

**Antes:**
- El usuario se generaba automáticamente: `{documento}-{idInsti}`
- No era personalizable

**Después:**
- Campo nuevo en formulario: **"Usuario de Acceso"**
- El administrador puede elegir el nombre de usuario
- Validación en tiempo real (solo letras, números, `.`, `-`, `_`)
- Mínimo 3 caracteres
- Fallback al formato anterior si no se especifica

**Cambios en HTML:**
```html
<div class="form-group-modern">
    <label>
        Usuario de Acceso
        <span class="required-asterisk">*</span>
    </label>
    <input type="text" 
           id="usuarioAcceso" 
           name="usuarioAcceso" 
           placeholder="Ej: admin.institucion"
           pattern="[a-zA-Z0-9._-]+">
</div>
```

**Cambios en Backend:**
```php
$usuarioAcceso = mysqli_real_escape_string($conexion, $_POST['usuarioAcceso'] ?? $documento."-".$idInsti);

// Se usa en la creación del usuario directivo (ID: 2)
('2', '".$usuarioAcceso."', SHA1('".$clave."'), 5, ...)
```

**Campo en BD:**
- Campo `uss_usuario`: Contiene el usuario personalizado
- Campo `uss_documento`: Sigue conteniendo el documento (sin cambios)

---

### 3️⃣ **Checkbox para Enviar Correo de Bienvenida**

**Nueva funcionalidad:**
- Checkbox en el paso 3 (Contacto Principal)
- Permite decidir si enviar o no el correo de bienvenida
- Usa la plantilla de email existente: `plantilla-email-bienvenida.php`

**Cambios en HTML:**
```html
<div class="form-group-modern">
    <div style="background: #f0f8ff; padding: 20px; border-radius: 8px;">
        <label>
            <input type="checkbox" 
                   id="enviarCorreoBienvenida" 
                   name="enviarCorreoBienvenida" 
                   value="1">
            <i class="fa fa-envelope"></i>
            Enviar correo de bienvenida con credenciales de acceso
        </label>
        <small>El usuario recibirá un correo con sus credenciales y un enlace para acceder al sistema</small>
    </div>
</div>
```

**Lógica de envío (Backend):**
```php
$enviarCorreoBienvenida = ($_POST['enviarCorreoBienvenida'] ?? '0') === '1';
$mensajeCorreo = '';
$correoExitoso = false;

if ($enviarCorreoBienvenida) {
    try {
        $data = [
            'institucion_id'   => $idInsti,
            'institucion_agno' => $year,
            'institucion_nombre' => $nombreInsti,
            'usuario_id'       => '2',
            'usuario_email'    => $email,
            'usuario_nombre'   => trim($nombre1." ".$nombre2." ".$apellido1." ".$apellido2),
            'usuario_usuario'  => $usuarioAcceso,
            'usuario_clave'    => $clave,
            'url_acceso'       => REDIRECT_ROUTE.'/index.php?inst='.base64_encode($idInsti).'&year='.base64_encode($year)
        ];
        $asunto = 'Bienvenido a la Plataforma SINTIA - Credenciales de Acceso';
        $bodyTemplateRoute = ROOT_PATH.'/config-general/plantilla-email-bienvenida.php';
        
        // EnviarEmail::enviar() retorna void, lanza excepción si falla
        EnviarEmail::enviar($data, $asunto, $bodyTemplateRoute, null, null);
        
        // Si llegamos aquí, el correo se envió exitosamente (no hubo excepción)
        $mensajeCorreo = '✉️ Correo de bienvenida enviado exitosamente a '.$email;
        $correoExitoso = true;
        
    } catch(Exception $emailError) {
        // Email opcional - no detener el proceso si falla
        $mensajeCorreo = '⚠️ No se pudo enviar el correo de bienvenida. Comunica las credenciales manualmente.';
        $correoExitoso = false;
        error_log("Error al enviar correo de bienvenida - Institución: ".$idInsti." - Error: ".$emailError->getMessage());
    }
}

// La respuesta incluye el estado REAL del envío
$finalResponse['correoEnviado'] = $correoExitoso; // true solo si se envió sin errores
```

**IMPORTANTE:** `EnviarEmail::enviar()` retorna `void` (no devuelve booleano). Se determina el éxito por la **ausencia de excepción**.

**Información incluida en el correo:**
- Nombre completo del usuario
- Usuario de acceso personalizado
- Contraseña temporal (12345678)
- Enlace directo para acceder al sistema
- Nombre de la institución
- Año académico

**Feedback visual:**
- ✅ Verde: Correo enviado exitosamente
- ⚠️ Amarillo: No se pudo enviar, avisar para comunicar credenciales manualmente
- Sin mensaje: No se marcó el checkbox (no se envía correo)

---

## 📊 RESUMEN TÉCNICO

### Archivos Modificados:

1. **`main-app/directivo/dev-crear-nueva-bd-v2.php`**
   - Agregado campo `usuarioAcceso` (input text con validación pattern)
   - Agregado checkbox `enviarCorreoBienvenida` con diseño destacado
   - Tooltip informativo en campo usuario
   - Small text con descripción del checkbox

2. **`main-app/directivo/dev-crear-nueva-bd-v2.js`**
   - Agregado `usuarioAcceso` y `enviarCorreoBienvenida` a `formData`
   - Actualizado `validateContacto()` para validar usuario (regex, min 3 chars)
   - Actualizado `checkStepCompletion()` para incluir usuario
   - Actualizado `saveCurrentStepData()` para guardar nuevos campos
   - Actualizado `buildConfirmation()` para mostrar usuario y estado de correo
   - Actualizado `showSuccessResult()` para mostrar mensaje de envío de correo
   - Agregada validación en tiempo real con auto-formato
   - Agregado event listener para checkbox

3. **`main-app/directivo/ajax-crear-bd-procesar-v2.php`**
   - Modificado PASO 2: Query dinámica para obtener TODOS los módulos activos
   - Modificado PASO 11: Uso de `$usuarioAcceso` en lugar de formato fijo
   - Agregada variable `$enviarCorreoBienvenida` desde POST
   - Modificado PASO 15: Envío condicional de correo de bienvenida
   - Agregados campos `correoEnviado` y `mensajeCorreo` a respuesta JSON
   - Incluido `institucion_nombre` en data del correo

---

## 🎯 FLUJO DE USUARIO

### Paso 3 (Contacto Principal):

1. Usuario completa datos personales
2. **Nuevo:** Escribe el usuario de acceso deseado
   - Validación en tiempo real
   - Auto-formato (solo caracteres permitidos)
   - Feedback visual (✅ válido / ❌ inválido)
3. **Nuevo:** Marca/desmarca checkbox de correo
4. Continúa al paso 4 (Confirmación)

### Confirmación:

- Se muestra resumen incluyendo:
  - ✅ Usuario de acceso personalizado
  - ✅ Estado del envío de correo (sí/no)

### Procesamiento:

- Se crea la institución
- Se relacionan **TODOS** los módulos activos automáticamente
- Se crea el usuario con `uss_usuario` personalizado
- Se envía correo de bienvenida **solo si está marcado**
- Se muestra resultado final con:
  - Credenciales del usuario
  - Mensaje sobre el envío de correo (enviado / no enviado / error)

---

## 🔍 VALIDACIONES IMPLEMENTADAS

### Campo `usuarioAcceso`:
- ✅ **Requerido** (campo obligatorio)
- ✅ **Regex**: `^[a-zA-Z0-9._-]{3,}$`
- ✅ **Caracteres permitidos**: letras, números, punto, guión, guión bajo
- ✅ **Longitud mínima**: 3 caracteres
- ✅ **Auto-formato**: Elimina caracteres no permitidos mientras se escribe
- ✅ **Feedback visual**: Icon success/error + mensaje

### Checkbox `enviarCorreoBienvenida`:
- ✅ **Opcional**: Por defecto no marcado
- ✅ **Visual destacado**: Fondo azul claro con icono de sobre
- ✅ **Descripción clara**: Explica qué sucederá al marcarlo
- ✅ **Integrado en confirmación**: Se muestra en resumen

---

## 📧 PLANTILLA DE EMAIL

**Archivo utilizado:** `config-general/plantilla-email-bienvenida.php`

**Variables disponibles en la plantilla:**
```php
$data['institucion_id']     // ID de la institución
$data['institucion_agno']   // Año académico
$data['institucion_nombre'] // Nombre de la institución ✨ NUEVO
$data['usuario_id']         // ID del usuario (siempre 2 para directivo)
$data['usuario_email']      // Email del destinatario
$data['usuario_nombre']     // Nombre completo
$data['usuario_usuario']    // Usuario de acceso ✨ PERSONALIZADO
$data['usuario_clave']      // Contraseña temporal
$data['url_acceso']         // URL directa para acceder ✨ NUEVO
```

---

## ✅ BENEFICIOS

### 1. Módulos Automáticos:
- ✅ Escalabilidad: Al agregar un módulo nuevo al sistema, automáticamente estará disponible para todas las instituciones nuevas
- ✅ Consistencia: Todas las instituciones tienen los mismos módulos activos
- ✅ Mantenimiento: No requiere editar código al modificar módulos

### 2. Usuario Personalizado:
- ✅ Profesionalismo: Permite usar nombres de usuario institucionales (ej: `admin.colegio`, `director.csj`)
- ✅ Flexibilidad: El administrador decide el formato
- ✅ Usabilidad: Más fácil de recordar que `documento-123`
- ✅ Retrocompatibilidad: Si no se especifica, usa el formato anterior

### 3. Correo de Bienvenida Opcional:
- ✅ Control: El administrador decide si enviar o no
- ✅ Útil en local: No intenta enviar en entornos de desarrollo si no se marca
- ✅ Feedback: Informa si el correo se envió o hubo error
- ✅ No bloquea: Si falla el email, la institución se crea igual

---

## 🧪 TESTING RECOMENDADO

### Caso 1: Nueva institución CON correo
1. Seleccionar "Nueva Institución"
2. Completar datos básicos
3. Completar datos de contacto
4. Escribir usuario personalizado (ej: `admin.test`)
5. ✅ Marcar checkbox de correo
6. Confirmar y crear
7. **Verificar**: 
   - Usuario creado con nombre personalizado
   - Correo recibido en bandeja de entrada
   - Módulos activos relacionados

### Caso 2: Nueva institución SIN correo
1. Igual que caso 1
2. ❌ NO marcar checkbox
3. **Verificar**:
   - Usuario creado correctamente
   - NO se envía correo
   - Credenciales mostradas en pantalla

### Caso 3: Renovación (sin cambios)
1. Seleccionar "Renovar Año"
2. **Verificar**:
   - Proceso funciona igual que antes
   - No afecta renovaciones

---

## 🔧 ARCHIVOS MODIFICADOS

| Archivo | Cambios | Líneas |
|---------|---------|--------|
| `dev-crear-nueva-bd-v2.php` | HTML: campo usuario + checkbox | +38 |
| `dev-crear-nueva-bd-v2.js` | Validaciones + confirmación | +40 |
| `ajax-crear-bd-procesar-v2.php` | Módulos dinámicos + correo | +35 |

**Total:** 3 archivos, ~113 líneas agregadas

---

## 📝 NOTAS TÉCNICAS

### Módulos Activos:
```sql
-- Query utilizada
SELECT mod_id FROM BD_ADMIN.modulos WHERE mod_estado = 1
```
- Se ejecuta una sola vez al crear la institución
- Se insertan en lote (batch insert) para eficiencia
- Solo módulos con `mod_estado = 1`

### Usuario de Acceso:
```php
// Campo en BD: uss_usuario (varchar)
// Campo documento: uss_documento (varchar) - sin cambios
```
- Separación clara entre usuario de acceso y documento de identidad
- Permite múltiples usuarios con el mismo documento si es necesario
- El usuario es único por institución+año

### Correo de Bienvenida:
- Usa clase `EnviarEmail` existente
- Template: `plantilla-email-bienvenida.php`
- **No bloquea** el proceso si falla
- Se captura la excepción y se informa al usuario
- Incluye enlace directo para acceder

---

## 🎨 MEJORAS DE UX

### Validación en tiempo real:
```javascript
// Auto-formato mientras escribe
$('#usuarioAcceso').on('input', function() {
    let value = $input.val();
    value = value.replace(/[^a-zA-Z0-9._-]/g, ''); // Solo permitidos
    $input.val(value);
    
    if (/^[a-zA-Z0-9._-]{3,}$/.test(value)) {
        markFieldSuccess($input, 'Usuario válido'); // ✅ Verde
    }
});
```

### Confirmación clara:
- Muestra el usuario personalizado en color destacado
- Indica visualmente si se enviará correo (✅ verde / ❌ gris)
- Preview en tiempo real

### Resultado final mejorado:
- Muestra credenciales claramente
- Informa sobre el estado del correo
- Mensaje diferenciado por color (éxito verde / advertencia amarilla)

---

## 🔐 SEGURIDAD

### Validaciones aplicadas:
- ✅ `mysqli_real_escape_string()` en todos los inputs
- ✅ Validación de formato con regex
- ✅ Fallback seguro si no se especifica usuario
- ✅ Try-catch en envío de correo (no expone errores SMTP)
- ✅ Transacciones SQL (rollback si falla)

---

**Fecha de Implementación:** 29 de Octubre de 2025  
**Estado:** ✅ COMPLETADO + Correo de Renovación  
**Testing:** Pendiente de usuario

---

## 📧 CORREO DE CONFIRMACIÓN PARA RENOVACIONES

### ✅ Nueva Funcionalidad Implementada

**Checkbox en renovaciones:**

Similar al checkbox de nuevas instituciones, ahora las renovaciones también pueden enviar correo de confirmación.

**Ubicación:** Paso 2 - Al final de datosRenovacion

**Funcionalidad:**
- ✅ Checkbox opcional para enviar correo
- ✅ Obtiene datos del contacto principal de la institución (tabla instituciones)
- ✅ Envía correo usando **plantilla específica de renovación** (`plantilla-email-renovacion-ano.php`)
- ✅ Informa si el correo se envió o no
- ✅ No bloquea el proceso si falla

**Plantilla utilizada:**
- 📧 **Renovación**: `config-general/plantilla-email-renovacion-ano.php` (diseño azul, mensaje específico de renovación)
- 📧 **Nueva institución**: `config-general/plantilla-email-bienvenida.php` (diseño verde, mensaje de bienvenida)

**Variables adicionales en template para renovación:**
```php
$data['year_anterior']    // Año que se copió  
$data['year_nuevo']       // Año renovado
$data['institucion_nombre'] // Nombre de la institución
$data['url_acceso']       // URL directa para acceder
```

**Mensajes posibles:**
- 🟢 "Correo de confirmación enviado exitosamente a email@ejemplo.com"
- 🟡 "No se pudo enviar el correo de confirmación"
- 🟡 "No se encontró email del contacto principal en la institución"

