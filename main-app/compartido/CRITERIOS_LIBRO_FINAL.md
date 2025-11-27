# 📋 CRITERIOS PARA "NO HAY DATOS PARA MOSTRAR"

## 🎯 ¿CUÁNDO SE MUESTRA EL MENSAJE?

El mensaje **"No hay datos para mostrar"** aparece cuando:

```php
$hayContenido = isset($estudiantes) && is_array($estudiantes) && count($estudiantes) > 0;

if (!$hayContenido) {
    // Mostrar mensaje "No hay datos"
}
```

---

## ✅ CRITERIOS ESPECÍFICOS:

### **1. Variable $estudiantes NO existe**
```php
isset($estudiantes) // → FALSE
```
**Causa:**
- El archivo `agrupar-datos-boletin-periodos-mejorado.php` no se ejecutó
- Hubo un error antes de crear la variable

---

### **2. Variable $estudiantes NO es array**
```php
is_array($estudiantes) // → FALSE
```
**Causa:**
- La variable se creó pero con valor incorrecto
- Error en el procesamiento de datos

---

### **3. Array $estudiantes está VACÍO**
```php
count($estudiantes) // → 0
```
**Causa:**
- La consulta SQL no devolvió estudiantes
- Parámetros incorrectos (curso, grupo, año, id)
- No hay estudiantes matriculados en ese curso/grupo

---

## 🔍 DIAGNÓSTICO CON DEBUG INFO

Cuando veas "No hay datos para mostrar", verás esta información:

```
Información de Debug:
- Año: 2025
- Curso: 11
- Grupo: A
- ID Estudiante: N/A
- Método: GET (o POST)
- Estudiantes encontrados: 0
- Periodo Final Config: 4
```

### **Interpretación:**

#### **Caso 1: Método GET con parámetros vacíos**
```
- Año: 2025
- Curso: (vacío)
- Grupo: (vacío)
- Método: GET
- Estudiantes encontrados: 0
```
**Problema:** Los parámetros GET no se guardaron correctamente desde POST.

**Solución:** Verificar que la URL tenga parámetros:
```
?year=MjAyNQ==&curso=MTE=&grupo=QQ==
```

---

#### **Caso 2: Método POST pero curso/grupo vacío**
```
- Año: 2025
- Curso: (vacío)
- Grupo: (vacío)
- Método: POST
- Estudiantes encontrados: 0
```
**Problema:** Formulario no envió curso/grupo correctamente.

**Solución:** Verificar que el modal tenga los valores seleccionados.

---

#### **Caso 3: Parámetros correctos pero 0 estudiantes**
```
- Año: 2025
- Curso: 11
- Grupo: A
- Método: POST
- Estudiantes encontrados: 0
```
**Problema:** La consulta no devuelve datos.

**Posibles Causas:**
1. No hay estudiantes matriculados en ese curso/grupo
2. Error en la consulta SQL
3. El año está en otra base de datos
4. Problema con `agrupar-datos-boletin-periodos-mejorado.php`

---

## 🐛 PROBLEMA REPORTADO POR USUARIO

### **Síntoma:**
```
1. Primera generación → Se detiene en algún punto (no completa)
2. Refresco (F5) → Muestra "No hay datos para mostrar"
```

### **Causa Raíz:**
```
POST → PHP procesa →  Se detiene por error/timeout
     → No actualiza URL con GET
     ↓
F5 (Refresco) → Sin parámetros GET → No hay datos
```

---

## ✅ SOLUCIÓN IMPLEMENTADA

### **1. Manejo de Parámetros Mejorado**
```php
// Priorizar POST primero, luego GET
if (isset($_POST["curso"]) && !empty($_POST["curso"])) {
    $curso = $_POST["curso"];
    $_GET["curso"] = base64_encode($curso); // Guardar en GET
} elseif (isset($_GET["curso"]) && !empty($_GET["curso"])) {
    $curso = base64_decode($_GET["curso"]);
}
```

**Beneficio:** Los parámetros se guardan en $_GET inmediatamente.

---

### **2. Debug Visible**
```html
<div>
    <strong>Información de Debug:</strong><br>
    <strong>Año:</strong> <?= $year ?><br>
    <strong>Curso:</strong> <?= $curso ?><br>
    <!-- ... -->
</div>
```

**Beneficio:** El usuario puede ver exactamente qué parámetros se recibieron.

---

### **3. Configuración PHP**
```php
set_time_limit(300); // 5 minutos
ini_set('memory_limit', '256M');
```

**Beneficio:** Evita que PHP se detenga por timeout.

---

### **4. Try-Catch**
```php
try {
    $datos = Boletin::datosBoletin(...);
    // ...
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo "<!-- Error: " . $e->getMessage() . " -->";
}
```

**Beneficio:** Captura errores sin romper la página.

---

## 🧪 CÓMO DIAGNOSTICAR EL PROBLEMA

### **Paso 1: Ver el mensaje de debug**
Al ver "No hay datos", mira la sección "Información de Debug".

### **Paso 2: Ver código fuente**
```
Clic derecho → Ver código fuente
Buscar: <!-- DEBUG INFO:
```

### **Paso 3: Ver Network en DevTools**
```
F12 → Network → Buscar el request
Ver si es POST o GET
Ver parámetros enviados
```

### **Paso 4: Ver Console**
```
F12 → Console
Buscar errores JavaScript o PHP warnings
```

---

## 📊 FLUJO CORRECTO ESPERADO

```
1. Usuario selecciona:
   - Año: 2025
   - Curso: 11
   - Grupo: A
   ↓
2. Submit formulario (POST)
   POST: year=2025, curso=11, grupo=A
   ↓
3. PHP recibe y convierte:
   $_POST["curso"] → $_GET["curso"] = base64_encode(11)
   ↓
4. PHP ejecuta consulta:
   Boletin::datosBoletin(11, A, [1,2,3,4], 2025)
   ↓
5. PHP agrupa datos:
   include("agrupar-datos-boletin-periodos-mejorado.php")
   → Crea $estudiantes[]
   ↓
6. PHP verifica:
   count($estudiantes) > 0 → $hayContenido = TRUE
   ↓
7. HTML se genera con contenido
   ↓
8. JavaScript actualiza URL:
   window.history.pushState(... ?year=...&curso=...)
   ↓
9. Usuario presiona F5:
   GET: ?year=MjAyNQ==&curso=MTE=&grupo=QQ==
   ↓
10. PHP procesa desde GET:
    $curso = base64_decode($_GET["curso"]) = 11
    ↓
11. Todo funciona igual
```

---

## 🎯 CHECKLIST PARA RESOLVER

Si ves "No hay datos para mostrar":

- [ ] Ver "Información de Debug"
- [ ] Verificar que Año, Curso y Grupo NO estén vacíos
- [ ] Verificar método (POST primera vez, GET al refrescar)
- [ ] Ver código fuente completo (¿llegó hasta `</html>`?)
- [ ] Ver Console en DevTools (¿hay errores?)
- [ ] Verificar que existan estudiantes en BD para ese curso/grupo/año
- [ ] Probar con otro curso/grupo que tenga menos estudiantes

---

## 🔧 SI EL PROBLEMA PERSISTE

### **Opción 1: Usar versión antigua**
```php
// En el modal, cambiar de:
action="../compartido/matricula-libro-curso-3-mejorado.php"

// A:
action="../compartido/matricula-libro-curso-3.php"
```

### **Opción 2: Aumentar límites PHP**
En `php.ini`:
```ini
max_execution_time = 600
memory_limit = 512M
```

### **Opción 3: Simplificar consulta**
Generar libro de UN estudiante a la vez en vez de todo el curso.

---

**El criterio es simple: Si `count($estudiantes) > 0`, hay contenido. Si no, no hay datos.** 🎯

