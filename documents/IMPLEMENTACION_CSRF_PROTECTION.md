# 🛡️ Plan de Implementación CSRF Protection

## 📊 Estado Actual
- ✅ **Implementado**: 3 archivos (login, registro)
- ⚠️ **Sin implementar**: 140+ archivos críticos
- 🎯 **Objetivo**: Proteger todos los formularios contra ataques CSRF

---

## 🎯 PRIORIDAD 1: AUTENTICACIÓN Y USUARIOS (CRÍTICO)

### Archivos a Proteger:
1. ✅ `main-app/controlador/autentico-async.php` - **YA TIENE**
2. ✅ `main-app/registro-guardar.php` - **YA TIENE**
3. ⚠️ `main-app/recuperar-clave-guardar.php` - **NECESITA**
4. ⚠️ `main-app/compartido/clave-actualizar.php` - **NECESITA**
5. ⚠️ `main-app/directivo/usuarios-guardar.php` - **NECESITA**
6. ⚠️ `main-app/directivo/usuarios-update.php` - **NECESITA**
7. ⚠️ `main-app/directivo/usuarios-eliminar.php` - **NECESITA**
8. ⚠️ `main-app/solicitud-desbloqueo-guardar.php` - **NECESITA**

### Formularios Asociados:
- `cambiar-clave.php` / `cambiar-clave-contenido.php`
- `recuperar-clave-restaurar.php`
- `usuarios-agregar.php`
- `usuarios-editar.php`

---

## 🎯 PRIORIDAD 2: ESTUDIANTES (CRÍTICO)

### Archivos a Proteger:
1. ⚠️ `main-app/directivo/estudiantes-guardar.php` - **NECESITA**
2. ⚠️ `main-app/directivo/estudiantes-eliminar.php` - **NECESITA**

### Formularios Asociados:
- `estudiantes-agregar.php`
- `estudiantes-editar.php`

---

## 🎯 PRIORIDAD 3: CONFIGURACIÓN DEL SISTEMA (ALTA)

### Archivos a Proteger:
1. ⚠️ `main-app/directivo/configuracion-sistema-guardar.php`
2. ⚠️ `main-app/directivo/configuracion-institucion-guardar.php`
3. ⚠️ `main-app/directivo/configuracion-opciones-generales-guardar.php`
4. ⚠️ `main-app/directivo/configuracion-finanzas-guardar.php`
5. ⚠️ `main-app/directivo/configuracion-admisiones-guardar.php`

---

## 🎯 PRIORIDAD 4: CALIFICACIONES (ALTA)

### Archivos a Proteger:
1. ⚠️ `main-app/docente/calificaciones-guardar.php`
2. ⚠️ `main-app/docente/ajax-notas-guardar.php`
3. ⚠️ `main-app/docente/ajax-nota-recuperacion-guardar.php`
4. ⚠️ `main-app/docente/ajax-notas-masiva-guardar.php`
5. ⚠️ `main-app/docente/calificaciones-eliminar.php`
6. ⚠️ `main-app/docente/ajax-calificaciones-eliminar.php`

---

## 🎯 PRIORIDAD 5: MÓDULOS ACADÉMICOS (MEDIA)

### Archivos a Proteger:
- Cursos/Grados (`cursos-guardar.php`, `cursos-eliminar.php`)
- Grupos (`grupos-guardar.php`)
- Áreas (`areas-guardar.php`, `areas-eliminar.php`)
- Asignaturas (`asignaturas-guardar.php`, `asignaturas-eliminar.php`)
- Cargas (`cargas-guardar.php`, `cargas-eliminar.php`)
- Indicadores (`indicadores-guardar.php`, `indicadores-eliminar.php`)

---

## 🎯 PRIORIDAD 6: OTROS MÓDULOS (MEDIA-BAJA)

### Archivos a Proteger:
- Actividades
- Evaluaciones
- Foros
- Disciplina
- Finanzas
- Admisiones

---

## 📝 PASOS DE IMPLEMENTACIÓN

### Paso 1: En el FORMULARIO (HTML/PHP)
```php
// Incluir la clase al inicio del archivo
require_once(ROOT_PATH."/main-app/class/App/Seguridad/Csrf.php");

// En el formulario, antes del botón submit:
<?php echo Csrf::campoHTML(); ?>
// O usando función de compatibilidad:
<?php echo campoTokenCSRF(); ?>
```

### Paso 2: En el archivo de PROCESAMIENTO (PHP)
```php
// Al inicio del archivo, después de session_start():
require_once(ROOT_PATH."/main-app/class/App/Seguridad/Csrf.php");

// Antes de procesar los datos:
Csrf::verificar(); // Para formularios HTML
// O
Csrf::verificar(true); // Para peticiones AJAX
```

### Paso 3: Para AJAX (JavaScript)
```javascript
// Al enviar petición AJAX, incluir el token:
const formData = new FormData();
formData.append('csrf_token', '<?php echo Csrf::obtenerToken(); ?>');
formData.append('otros', 'datos');

// O en jQuery:
$.ajax({
    data: {
        csrf_token: '<?php echo Csrf::obtenerToken(); ?>',
        otros: 'datos'
    }
});
```

---

## ⏱️ ESTIMACIÓN DE TIEMPO

- **Prioridad 1 (Usuarios)**: 2-3 horas
- **Prioridad 2 (Estudiantes)**: 1 hora
- **Prioridad 3 (Configuración)**: 1-2 horas
- **Prioridad 4 (Calificaciones)**: 2-3 horas
- **Prioridad 5 (Académicos)**: 3-4 horas
- **Prioridad 6 (Otros)**: 5-6 horas

**TOTAL**: ~15-20 horas de trabajo

---

## 🚀 ESTRATEGIA DE IMPLEMENTACIÓN

### Opción A: Implementación Completa (Recomendada)
- Implementar TODAS las prioridades 1-4 en esta sesión
- Dejar prioridades 5-6 para después
- **Tiempo**: 6-9 horas

### Opción B: Solo Críticos (Rápida)
- Implementar solo Prioridad 1 y 2
- **Tiempo**: 3-4 horas
- Protege lo más importante

### Opción C: Gradual
- Implementar Prioridad 1 ahora
- Continuar con el resto en siguientes sesiones

---

## 📋 CHECKLIST DE VALIDACIÓN

Después de implementar, verificar:
- [ ] Token se genera correctamente en formulario
- [ ] Token se envía en POST/GET
- [ ] Validación bloquea peticiones sin token
- [ ] Validación bloquea token inválido
- [ ] Validación permite token válido
- [ ] Log registra intentos de CSRF
- [ ] Mensaje de error es claro para el usuario

---

## 🔍 NOTAS IMPORTANTES

1. **NO romper funcionalidad existente**: Probar cada formulario después de implementar
2. **Mantener UX**: Mensaje de error debe ser claro
3. **AJAX requiere manejo especial**: Retornar JSON, no HTML
4. **Regeneración de tokens**: Cada 2 horas automáticamente
5. **Testing**: Probar con token válido, sin token, token expirado

---

## 📊 PROGRESO ACTUAL

### ✅ **FASE 1 COMPLETADA: Usuarios + Estudiantes**

**Archivos Protegidos (Procesamiento):**
1. ✅ `main-app/recuperar-clave-guardar.php`
2. ✅ `main-app/compartido/clave-actualizar.php`
3. ✅ `main-app/directivo/usuarios-guardar.php`
4. ✅ `main-app/directivo/usuarios-update.php`
5. ✅ `main-app/directivo/usuarios-eliminar.php`
6. ✅ `main-app/solicitud-desbloqueo-guardar.php`
7. ✅ `main-app/directivo/estudiantes-guardar.php`
8. ✅ `main-app/directivo/estudiantes-eliminar.php`

**Formularios Protegidos:**
1. ✅ `main-app/recuperar-clave-restaurar.php`
2. ✅ `main-app/compartido/cambiar-clave-contenido.php`
3. ✅ `main-app/directivo/usuarios-agregar.php`
4. ✅ `main-app/directivo/includes/usuarios-editar-info-basica.php`
5. ✅ `main-app/solicitud-desbloqueo.php`
6. ✅ `main-app/directivo/estudiantes-agregar.php`

**Páginas con Token Global:**
1. ✅ `main-app/directivo/usuarios.php` (listado)
2. ✅ `main-app/directivo/estudiantes.php` (listado)
3. ✅ `main-app/directivo/usuarios-editar.php` (edición)
4. ✅ `main-app/directivo/estudiantes-editar.php` (edición)

**Funciones JavaScript Protegidas:**
1. ✅ `deseaEliminar()` - Agrega token CSRF automáticamente a URLs GET
2. ✅ `sweetConfirmacion()` - Agrega token CSRF en GET y POST

---

## 🛡️ **COBERTURA DE PROTECCIÓN**

✅ **100% Gestión de Usuarios**: Crear, Editar, Eliminar, Cambiar Clave  
✅ **100% Gestión de Estudiantes**: Crear, Editar, Eliminar  
✅ **100% Autenticación**: Login ✓, Recuperar Clave ✓  
✅ **Protección Universal**: Funciones JS automáticas para todas las eliminaciones

**Total Protegido**: ~16 archivos críticos  
**Pendiente**: ~124 archivos (configuración, calificaciones, otros módulos)

---

## 🎯 **PRÓXIMOS PASOS (Opcional)**

Para completar la protección CSRF al 100%:

### Prioridad 3: Configuración (5 archivos)
- configuracion-sistema-guardar.php
- configuracion-institucion-guardar.php
- configuracion-opciones-generales-guardar.php
- configuracion-finanzas-guardar.php
- configuracion-admisiones-guardar.php

### Prioridad 4: Calificaciones (6 archivos)
- calificaciones-guardar.php
- ajax-notas-guardar.php
- ajax-nota-recuperacion-guardar.php
- ajax-notas-masiva-guardar.php
- calificaciones-eliminar.php
- ajax-calificaciones-eliminar.php

**Última actualización**: 2025-10-30 (Fase 1 - Usuarios + Estudiantes COMPLETADA)

