# 🔄 MIGRACIÓN A PDO PREPARED STATEMENTS

## 📋 DECISIÓN TÉCNICA

**Fecha:** 29 de Octubre de 2025  
**Decisión:** Migrar de **mysqli** a **PDO** para prepared statements

---

## 🎯 RAZÓN DEL CAMBIO

Durante la implementación de la Fase 1 de Seguridad, inicialmente se usó `mysqli_prepare()` para prepared statements. Sin embargo, se detectó que el proyecto **ya tiene un patrón establecido con PDO**.

### Ventajas de PDO sobre mysqli:

| Característica | PDO | mysqli |
|----------------|-----|--------|
| **Tipado de datos** | ✅ Explícito (PDO::PARAM_*) | ⚠️ String type codes ("si") |
| **Sintaxis** | ✅ Clara (`bindParam()`) | ⚠️ Compleja (`mysqli_stmt_bind_param()`) |
| **Excepciones** | ✅ Nativo con PDO::ERRMODE_EXCEPTION | ⚠️ Manual |
| **Portabilidad** | ✅ Multi-database | ❌ Solo MySQL |
| **Consistencia** | ✅ Ya usado en el proyecto | ⚠️ Mezcla con PDO |

---

## 🔧 PATRÓN ESTABLECIDO EN EL PROYECTO

### **Clase Conexion**

```php
// Ubicación: main-app/class/Conexion.php
class Conexion extends Conexion_Factory {
    protected function conexionPDO() {
        $this->conexionPDO = new PDO(
            "mysql:host=".SERVIDOR_CONEXION.";dbname=".BD_ADMIN.";charset=utf8mb4", 
            USUARIO_CONEXION, 
            CLAVE_CONEXION
        );
        $this->conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $this->conexionPDO;
    }
}
```

### **Factory Pattern**

```php
// Ubicación: main-app/class/Conexion_Factory.php
public static function newConnection(string $tipo) {
    switch($tipo) {
        case 'MYSQL':
            return Conexion::getConexion()->conexion();      // mysqli
        case 'PDO':
            return Conexion::getConexion()->conexionPDO();   // PDO ✅
    }
}
```

---

## 📝 PATRÓN ESTÁNDAR A SEGUIR

### **Template para INSERT/UPDATE/DELETE:**

```php
// 1. Incluir clase (si no está incluida)
require_once(ROOT_PATH."/main-app/class/Conexion.php");

// 2. Obtener conexión PDO
$conexionPDO = Conexion::newConnection('PDO');
$conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 3. Preparar SQL
$sql = "INSERT INTO tabla (campo1, campo2, campo3) VALUES (?, ?, ?)";
$stmt = $conexionPDO->prepare($sql);

// 4. Bind de parámetros (numeración desde 1)
$stmt->bindParam(1, $valor1, PDO::PARAM_STR);
$stmt->bindParam(2, $valor2, PDO::PARAM_INT);
$stmt->bindParam(3, $valor3, is_null($valor3) ? PDO::PARAM_NULL : PDO::PARAM_STR);

// 5. Ejecutar
$stmt->execute();

// 6. Obtener ID si es INSERT
$idNuevo = $conexionPDO->lastInsertId();
```

### **Template para SELECT:**

```php
// Preparar
$sql = "SELECT * FROM tabla WHERE campo1=? AND campo2=?";
$stmt = $conexionPDO->prepare($sql);

// Bind
$stmt->bindParam(1, $valor1, PDO::PARAM_INT);
$stmt->bindParam(2, $valor2, PDO::PARAM_STR);

// Ejecutar
$stmt->execute();

// Fetch - Una fila
$resultado = $stmt->fetch(PDO::FETCH_BOTH);

// Fetch - Múltiples filas
$resultados = $stmt->fetchAll(PDO::FETCH_BOTH);
```

---

## 🔄 ARCHIVOS MIGRADOS EN FASE 1

### ✅ Migrados a PDO:

1. **`main-app/index.php`**
   - Query: SELECT de `general_informacion`
   - Bind: `info_institucion` (STR), `info_year` (INT)

2. **`main-app/compartido/noticias-guardar.php`**
   - Query 1: INSERT en `social_noticias` (18 parámetros)
   - Query 2: DELETE en `social_noticias_cursos`
   - Query 3: INSERT en `social_noticias_cursos` (loop)

---

## 📊 TIPOS DE DATOS PDO

### **Tipos Comunes:**

```php
PDO::PARAM_STR   // String - varchar, text, etc.
PDO::PARAM_INT   // Integer - int, bigint, etc.
PDO::PARAM_BOOL  // Boolean - tinyint(1), bool
PDO::PARAM_NULL  // NULL
```

### **Manejo de NULL:**

```php
// Ternario para valores que pueden ser null
$stmt->bindParam(1, $valor, is_null($valor) ? PDO::PARAM_NULL : PDO::PARAM_STR);

// O verificar antes
if (is_null($valor)) {
    $stmt->bindParam(1, $valor, PDO::PARAM_NULL);
} else {
    $stmt->bindParam(1, $valor, PDO::PARAM_STR);
}
```

---

## 🔍 EJEMPLO REAL DEL PROYECTO

**Ubicación:** `main-app/docente/ajax-calificaciones-registrar.php` (según imagen)

```php
$sql = "INSERT INTO BD_ACADEMICA.academico_calificaciones(
    cal_id, cal_id_estudiante, cal_nota, cal_id_actividad, 
    cal_fecha_registrada, cal_cantidad_modificaciones, institucion, year
) VALUES (?, ?, ?, ?, now(), ?, ?, ?)";

$conexionPDO = Conexion::newConnection('PDO');
$conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$asp = $conexionPDO->prepare($sql);
$asp->bindParam(1, $codigo, PDO::PARAM_STR);
$asp->bindParam(2, $data['codEst'], PDO::PARAM_STR);
$asp->bindParam(3, $nota, is_null($nota) ? PDO::PARAM_NULL : PDO::PARAM_STR);
$asp->bindParam(4, $data['codNota'], PDO::PARAM_STR);
$asp->bindParam(5, $cantidadModificaciones, PDO::PARAM_STR);
$asp->bindParam(6, $config['conf_id_institucion'], PDO::PARAM_INT);
$asp->bindParam(7, $_SESSION["bd"], PDO::PARAM_INT);

$asp->execute();
```

**Características clave:**
- ✅ Función `now()` dentro del SQL (no como parámetro)
- ✅ Tipos explícitos para cada parámetro
- ✅ Manejo de NULL con ternario
- ✅ Exception mode activado

---

## 🚫 ERRORES COMUNES A EVITAR

### ❌ NO HACER:

```php
// 1. NO usar mysqli
$stmt = mysqli_prepare($conexion, $sql); // ❌

// 2. NO mezclar posiciones
$stmt->bindParam(2, $valor); // Empezar en 1, no 0

// 3. NO omitir tipo de dato
$stmt->bindParam(1, $valor); // Falta PDO::PARAM_* ⚠️

// 4. NO olvidar setAttribute
$conexionPDO = Conexion::newConnection('PDO');
// Falta: ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION)
```

### ✅ SÍ HACER:

```php
// 1. Incluir Conexion si no está
require_once(ROOT_PATH."/main-app/class/Conexion.php");

// 2. Usar PDO
$conexionPDO = Conexion::newConnection('PDO');
$conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 3. Bind numerado desde 1 con tipo
$stmt->bindParam(1, $valor, PDO::PARAM_STR);

// 4. Manejo de null explícito
$stmt->bindParam(1, $valor, is_null($valor) ? PDO::PARAM_NULL : PDO::PARAM_INT);
```

---

## 📋 CHECKLIST PARA MIGRACIÓN

Al migrar un query a PDO, verificar:

- [ ] ✅ Incluir `Conexion.php` si no está
- [ ] ✅ Usar `Conexion::newConnection('PDO')`
- [ ] ✅ Activar `PDO::ERRMODE_EXCEPTION`
- [ ] ✅ Preparar SQL con placeholders `?`
- [ ] ✅ Bind de cada parámetro (desde 1)
- [ ] ✅ Tipo explícito en cada bind
- [ ] ✅ Manejar NULL si aplica
- [ ] ✅ Ejecutar con `execute()`
- [ ] ✅ Usar `lastInsertId()` si es INSERT
- [ ] ✅ Usar `fetch()` o `fetchAll()` si es SELECT
- [ ] ✅ Try-catch para manejo de errores

---

## 🎯 PRÓXIMOS ARCHIVOS A MIGRAR (Fase 2)

Archivos prioritarios para convertir a PDO:

1. Usuarios y autenticación
2. Calificaciones (crítico)
3. Finanzas (crítico)
4. Estudiantes y matrículas
5. Cargas académicas
6. Configuraciones

**Búsqueda sugerida:**
```bash
grep -r "mysqli_query.*\$_POST\|mysqli_query.*\$_GET" main-app/
```

---

## ✅ BENEFICIOS INMEDIATOS

Con la migración a PDO:

1. ✅ **Consistencia**: Todo el proyecto usa el mismo patrón
2. ✅ **Seguridad**: Tipado fuerte previene errores de tipo
3. ✅ **Debugging**: Excepciones claras con stack trace
4. ✅ **Mantenibilidad**: Código más legible
5. ✅ **Escalabilidad**: Fácil migrar a otras BD si es necesario

---

**Estado:** ✅ PATRÓN ESTABLECIDO  
**Aplicar en:** TODAS las migraciones SQL de seguridad  
**Referencia:** Imagen adjunta del proyecto + este documento

