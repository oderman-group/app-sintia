# 🔧 Sistema de Auto-Configuración de Módulos

## 📋 Descripción

Cuando se activan ciertos módulos específicos en una institución, el sistema **automáticamente** crea las configuraciones necesarias en sus respectivas bases de datos para que funcionen correctamente desde el primer momento.

---

## 🎯 Módulos con Auto-Configuración

### 1. 💰 Módulo Financiero (ID: 2)

**¿Qué hace?**
Al activar el módulo Financiero, se inserta automáticamente un registro en la tabla `configuration` de la base de datos financiera.

**Base de datos:** `BD_FINANCIERA` (ej: `mobiliar_financial_local`)

**Tabla:** `configuration`

**Campos insertados:**
```sql
INSERT INTO configuration(
    consecutive_start,    -- Valor: '1'
    invoice_footer,       -- Valor: 'Gracias por su preferencia'
    institucion,          -- ID de la institución
    year                  -- Año actual de la sesión
)
```

**Validación:**
- ✅ Verifica que NO exista ya una configuración para esa institución y año
- ✅ Solo inserta si es necesario
- ✅ No duplica registros

**Ejemplo de registro creado:**
```
id: auto_increment
consecutive_start: '1'
invoice_footer: 'Gracias por su preferencia'
institucion: 123
year: '2025'
```

---

### 2. 📝 Módulo Inscripciones (ID: 8)

**¿Qué hace?**
Al activar el módulo de Inscripciones, se inserta automáticamente un registro en la tabla `config_instituciones` de la base de datos de admisiones.

**Base de datos:** `BD_ADMISIONES` (ej: `mobiliar_sintia_admisiones_local`)

**Tabla:** `config_instituciones`

**Campos insertados:**
```sql
INSERT INTO config_instituciones(
    cfgi_id_institucion,           -- ID de la institución
    cfgi_year,                     -- Año actual de la sesión
    cfgi_color_barra_superior,     -- Color de la barra (de la inst.)
    cfgi_inscripciones_activas,    -- Valor: '0' (inactivas por defecto)
    cfgi_politicas_texto,          -- Valor: 'Lorem ipsum...'
    cfgi_color_texto,              -- Valor: 'white'
    cfgi_mostrar_banner,           -- Valor: '0'
    cfgi_year_inscripcion          -- Año siguiente al actual
)
```

**Validación:**
- ✅ Verifica que NO exista ya una configuración para esa institución y año
- ✅ Toma el color de la barra de la institución (o usa #41c4c4 por defecto)
- ✅ Calcula automáticamente el año de inscripción (año actual + 1)

**Ejemplo de registro creado:**
```
cfgi_id: auto_increment
cfgi_id_institucion: 123
cfgi_year: 2025
cfgi_color_barra_superior: '#41c4c4'
cfgi_inscripciones_activas: 0
cfgi_year_inscripcion: 2026
```

---

## 🔄 Flujo de Auto-Configuración

```
Usuario activa módulo (ej: Financiero)
    ↓
Sistema guarda en instituciones_modulos
    ↓
Sistema detecta que es módulo especial
    ↓
Verifica si YA existe configuración
    ↓
Si NO existe:
    ├─ Inserta configuración inicial
    └─ Mensaje: "Módulo asignado (Financiero configurado)"
    ↓
Si SÍ existe:
    └─ Mensaje: "Módulo asignado correctamente"
```

---

## 📁 Archivo Responsable

**Backend:** `main-app/directivo/ajax-instituciones-modulos-guardar.php`

**Líneas clave:**
- **62-67**: Detección de módulos especiales
- **78-107**: Configuración de Inscripciones
- **109-133**: Configuración de Financiero
- **140-162**: Mensajes informativos

---

## 💡 ¿Cómo Extender esta Funcionalidad?

### Para agregar un nuevo módulo auto-configurable:

```php
// 1. En el loop de inserción, detectar el módulo
if ($moduloId == Modulos::MODULO_NUEVO) {
    $modulosConfigurados['nuevo'] = true;
}

// 2. Después del loop, configurar el módulo
if (!empty($modulosConfigurados['nuevo'])) {
    try {
        // Verificar si existe
        $consultaConfig = mysqli_query($conexion, "SELECT id FROM {$baseDatosNueva}.tabla_config 
            WHERE institucion = {$institucionId} AND year = {$_SESSION["bd"]}");
        
        if (mysqli_num_rows($consultaConfig) == 0) {
            // Insertar configuración
            $sql = "INSERT INTO {$baseDatosNueva}.tabla_config(
                campo1,
                campo2,
                institucion,
                year
            ) VALUES (?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($stmt, "ssis", $valor1, $valor2, $institucionId, $_SESSION["bd"]);
            mysqli_stmt_execute($stmt);
        }
    } catch (Exception $e) {
        error_log("Error configurando módulo nuevo: " . $e->getMessage());
    }
}

// 3. Agregar al mensaje
if (!empty($modulosConfigurados['nuevo'])) {
    $mensajeExtra[] = 'Nuevo módulo configurado';
}
```

---

## 🧪 Testing

### Probar Módulo Financiero:
1. Ir a `dev-instituciones-editar-v2.php`
2. Seleccionar una institución
3. Activar el módulo "Financiero" (ID: 2)
4. Verificar en BD:
   ```sql
   SELECT * FROM mobiliar_financial_local.configuration 
   WHERE institucion = [ID_INST] AND year = [YEAR];
   ```
5. Debe existir un registro con consecutive_start = '1'

### Probar Módulo Inscripciones:
1. Ir a `dev-instituciones-editar-v2.php`
2. Seleccionar una institución
3. Activar el módulo "Inscripciones" (ID: 8)
4. Verificar en BD:
   ```sql
   SELECT * FROM mobiliar_sintia_admisiones_local.config_instituciones 
   WHERE cfgi_id_institucion = [ID_INST] AND cfgi_year = [YEAR];
   ```
5. Debe existir un registro con cfgi_inscripciones_activas = 0

---

## 🛡️ Seguridad y Validaciones

### ✅ Validaciones Implementadas:

1. **No duplicar configuraciones**
   - Verifica que NO exista ya un registro
   - Usa `SELECT` antes de `INSERT`

2. **Manejo de errores robusto**
   - Try-catch en cada configuración
   - Errores se registran en error_log
   - No interrumpe el flujo si falla uno

3. **Prepared statements**
   - Usa `mysqli_prepare` y `mysqli_stmt_bind_param`
   - Protección contra SQL Injection

4. **Valores por defecto sensatos**
   - Inscripciones: inactivas por defecto (0)
   - Financiero: consecutivo empieza en 1
   - Textos descriptivos apropiados

---

## 📊 Mensaje de Respuesta

### Ejemplo de respuesta JSON:

**Módulo individual:**
```json
{
    "success": true,
    "message": "Módulo asignado correctamente (Financiero configurado)",
    "total_procesados": 1,
    "total_errores": 0,
    "modulos_configurados": {
        "financiero": true
    }
}
```

**Múltiples módulos:**
```json
{
    "success": true,
    "message": "15 módulos asignados correctamente | Inscripciones configurado, Financiero configurado",
    "total_procesados": 15,
    "total_errores": 0,
    "modulos_configurados": {
        "inscripciones": true,
        "financiero": true
    }
}
```

---

## 🎯 Beneficios

### Para Administradores:
- ✅ No necesitan configurar manualmente cada módulo
- ✅ Evita errores de configuración
- ✅ Ahorra tiempo significativo
- ✅ Consistencia en todas las instituciones

### Para el Sistema:
- ✅ Módulos listos para usar inmediatamente
- ✅ Configuraciones estándar garantizadas
- ✅ Menos tickets de soporte
- ✅ Mejor experiencia de usuario

---

## 📝 Notas Importantes

1. **Solo se configura si NO existe:**
   - Si ya hay configuración, NO se sobrescribe
   - Respeta configuraciones existentes

2. **Errores no interrumpen:**
   - Si falla la configuración de un módulo, continúa con los demás
   - Errores se registran en log pero no detienen el proceso

3. **Compatibilidad:**
   - Funciona tanto para módulos individuales como masivos
   - Compatible con la versión anterior del sistema

4. **Variables de sesión requeridas:**
   - `$_SESSION["bd"]` - Año actual
   - `$_SESSION["datosUnicosInstitucion"]` - Datos de la institución

---

## 🚀 Módulos Actualmente Soportados

| Módulo | ID | Auto-Configura | Tabla | Base de Datos |
|--------|----|--------------|---------| --------------|
| Financiero | 2 | ✅ | `configuration` | BD_FINANCIERA |
| Inscripciones | 8 | ✅ | `config_instituciones` | BD_ADMISIONES |
| Otros | - | ❌ | - | - |

---

## 🔮 Futuras Expansiones

Módulos que podrían beneficiarse de auto-configuración:

- [ ] Disciplinario (ID: 3) - Configurar sistema de observador
- [ ] Media Técnica (ID: 10) - Configurar especialidades
- [ ] Reserva de Cupo (ID: 9) - Configurar períodos de reserva
- [ ] Chat/Mensajería - Configurar salas por defecto

---

## ✅ Testing Completado

- ✅ Módulo Financiero: Configuración automática funcional
- ✅ Módulo Inscripciones: Configuración automática funcional
- ✅ No duplica registros existentes
- ✅ Manejo de errores robusto
- ✅ Mensajes informativos claros
- ✅ Compatible con acciones masivas

---

**Versión**: 1.0  
**Fecha**: Octubre 2025  
**Estado**: ✅ Funcional  
**Mantenedor**: Sistema SINTIA

