# 🔍 Guía de Depuración - Edición Masiva de Cargas

## Problema Reportado

1. ❌ Al cambiar el docente → Error de collation
2. ❌ Al cambiar la I.H → No dice nada pero tampoco guarda

---

## 📝 Cómo Depurar el Problema

### Paso 1: Abrir la Consola del Navegador

1. Abre Chrome/Firefox
2. Presiona `F12` para abrir DevTools
3. Ve a la pestaña **Console**
4. Deja la consola abierta

### Paso 2: Intentar el Cambio

#### Test A: Cambiar Solo I.H

1. Selecciona 1-2 cargas
2. Clic en "Editar Seleccionadas"
3. **Solo** cambia la Intensidad Horaria (ej: 3)
4. Deja los demás campos vacíos
5. Clic en "Aplicar Cambios Masivos"

**En la consola verás:**
```javascript
=== FORMULARIO DE EDICIÓN MASIVA ENVIADO ===
FormData serializado: [...]
Procesando campo: ih = 3 (tipo: string)
✓ Campo agregado: ih = 3
Campos finales a actualizar: {ih: "3"}
Hay cambios: true
Iniciando edición masiva...
Cargas seleccionadas: [123, 456]
Campos a actualizar: {ih: "3"}
```

**Si funciona:**
- Verás "Respuesta del servidor: {success: true, actualizadas: 2, ...}"
- Toast verde de éxito

**Si NO funciona:**
- Verás "==== ERROR EN EDICIÓN MASIVA ===="
- **COPIA TODO EL ERROR** y envíamelo

#### Test B: Cambiar Solo Docente

1. Selecciona 1-2 cargas
2. Clic en "Editar Seleccionadas"
3. **Solo** cambia el Docente
4. Deja los demás campos vacíos
5. Clic en "Aplicar Cambios Masivos"

**En la consola verás algo similar:**
```javascript
Procesando campo: docente = XXXX (tipo: string)
✓ Campo agregado: docente = XXXX
```

**Si sale el error de collation:**
- Verás el error completo en la consola
- **COPIA TODO EL ERROR** (especialmente el Response Text)

---

### Paso 3: Ver los Logs del Backend

**Archivo de logs:** `config-general/errores_local.log`

1. Abre el archivo con un editor de texto
2. Ve al final del archivo (las últimas líneas)
3. Busca las líneas que empiezan con:

```
=== INICIO EDICIÓN MASIVA ===
POST recibido: Array(...)
Cargas recibidas: Array(...)
Campos recibidos: Array(...)
```

**Lo que debes buscar:**

Para el **problema de I.H:**
```
Procesando campo: ih (columna: car_ih), valor: 3
Campo numérico 'ih' sanitizado: 3
Datos a actualizar preparados: Array([car_ih] => 3)
Total de cargas a actualizar: 2
Procesando carga ID: 123
Preparando UPDATE para columna: car_ih, valor: 3, tipo: integer
SQL generado: UPDATE `mobiliar_academic_local`.`academico_cargas` SET car_ih = 3 WHERE car_id = 123 AND institucion = 1 AND year = 2025
Query ejecutada exitosamente. Filas afectadas: 1
✓ Carga 123 actualizada correctamente
```

**Si NO ves esto**, significa que hay un problema antes de llegar a ejecutar el SQL.

Para el **problema del Docente:**
```
Procesando campo: docente (columna: car_docente), valor: XXXX
Campo docente sanitizado: 'XXXX'
SQL generado: UPDATE ... SET car_docente = 'XXXX' ...
ERROR al ejecutar query para carga 123
Error #1267: Illegal mix of collations (latin1_swedish_ci,IMPLICIT) and (utf8mb4_general_ci,COERCIBLE) for operation '='
```

---

## 🔧 Qué Necesito de Ti

**Por favor, haz ambas pruebas (I.H y Docente) y envíame:**

### Para cada prueba, envíame:

1. **La salida completa de la consola del navegador** (todo el bloque desde "=== FORMULARIO..." hasta el error o éxito)

2. **Las últimas 50-100 líneas del archivo de logs** (`config-general/errores_local.log`)

3. **Captura de pantalla del toast** que aparece (si aparece alguno)

4. **Confirmación:**
   - ¿Se actualizó en la base de datos? (revisa manualmente la tabla `academico_cargas`)
   - ¿Qué valor tenía antes?
   - ¿Qué valor tiene ahora?

---

## 🎯 Información Específica que Necesito

### Sobre el campo Docente:

1. ¿Qué tipo de dato es `car_docente` en tu tabla?
   ```sql
   SHOW COLUMNS FROM mobiliar_academic_local.academico_cargas WHERE Field = 'car_docente';
   ```

2. ¿Qué collation tiene?
   ```sql
   SHOW FULL COLUMNS FROM mobiliar_academic_local.academico_cargas WHERE Field = 'car_docente';
   ```

3. Ejemplo de un valor actual en la tabla:
   ```sql
   SELECT car_id, car_docente FROM mobiliar_academic_local.academico_cargas LIMIT 5;
   ```

### Sobre el campo I.H:

1. ¿Qué tipo de dato es `car_ih` en tu tabla?
   ```sql
   SHOW COLUMNS FROM mobiliar_academic_local.academico_cargas WHERE Field = 'car_ih';
   ```

2. Ejemplo de valores actuales:
   ```sql
   SELECT car_id, car_ih FROM mobiliar_academic_local.academico_cargas LIMIT 5;
   ```

---

## 🐛 Posibles Causas

### Problema I.H (No Guarda):

**Hipótesis:**
1. El campo no se está enviando correctamente
2. El valor se está filtrando antes de llegar al SQL
3. La query se ejecuta pero no afecta filas (valores ya son iguales)
4. Problema de permisos en la BD

**Lo que los logs revelarán:**
- Si el campo llega al backend o se pierde en el camino
- Si el SQL se genera correctamente
- Si la query se ejecuta
- Cuántas filas se afectan

### Problema Docente (Error Collation):

**Hipótesis:**
1. La columna `car_docente` tiene collation `latin1_swedish_ci`
2. El valor que enviamos tiene encoding `utf8mb4`
3. MySQL no puede comparar ambos en el WHERE

**Soluciones posibles:**
1. Cambiar collation de la columna a UTF8MB4
2. Convertir el valor antes de comparar
3. Usar CAST en la query

---

## 💡 Comandos Útiles para Ti

### Ver todas las collations de la tabla:
```sql
SELECT 
    COLUMN_NAME,
    CHARACTER_SET_NAME,
    COLLATION_NAME
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'mobiliar_academic_local'
AND TABLE_NAME = 'academico_cargas';
```

### Ver un registro específico:
```sql
SELECT * FROM mobiliar_academic_local.academico_cargas WHERE car_id = 123;
```

### Intentar UPDATE manual para ver si funciona:
```sql
UPDATE mobiliar_academic_local.academico_cargas 
SET car_ih = 5 
WHERE car_id = 123 
AND institucion = 1 
AND year = 2025;

-- Ver si se actualizó
SELECT car_id, car_ih FROM mobiliar_academic_local.academico_cargas WHERE car_id = 123;
```

---

## 📬 Qué Enviarme

1. ✅ **Logs de consola del navegador** (ambas pruebas: I.H y Docente)
2. ✅ **Últimas líneas del archivo errores_local.log**
3. ✅ **Resultado de las queries SQL** de información de las columnas
4. ✅ **Captura de los toasts** si aparecen
5. ✅ **Confirmación** de si se guardó o no en la BD

Con esta información podré identificar exactamente dónde está el problema y darte la solución precisa.

---

**Creado:** Octubre 23, 2025  
**Propósito:** Depurar problemas de edición masiva  
**Estado:** Esperando información del usuario

