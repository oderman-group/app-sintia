# 🌐 Sistema de Traducción Moderno para SINTIA

## 📋 Resumen

Se ha implementado un sistema de traducción moderno y escalable que reemplaza el método tradicional de `$frases[numero][$idioma]` con un sistema basado en **claves semánticas** y **archivos JSON**.

### ✨ Ventajas del Nuevo Sistema

- ✅ **Claves descriptivas**: En lugar de `$frases[209]`, ahora usas `__('estudiantes.titulo')`
- ✅ **Fallback inteligente**: Si falta una traducción, muestra un texto formateado legible
- ✅ **Detección automática**: Registra automáticamente textos sin traducir
- ✅ **Escalable**: Fácil agregar más idiomas (francés, portugués, etc.)
- ✅ **Mantenible**: Archivos JSON separados por idioma
- ✅ **Compatible**: Convive con el sistema anterior sin romperlo

---

## 📁 Estructura de Archivos

```
config-general/
├── Traductor.php                    # Clase principal del sistema
├── idiomas.php                      # Archivo original (modificado para integrar el nuevo sistema)
└── traducciones/
    ├── ES.json                      # Traducciones en español
    ├── EN.json                      # Traducciones en inglés
    ├── pendientes.json             # Textos detectados sin traducción (se genera automáticamente)
    └── reporte-extraccion.json     # Reporte del script extractor

scripts/
└── extraer-traducciones-directivo.php  # Script para escanear y extraer textos
```

---

## 🚀 Cómo Usar el Sistema

### Uso Básico

```php
// ❌ ANTES (sistema antiguo)
echo $frases[209][$datosUsuarioActual['uss_idioma']];

// ✅ AHORA (sistema moderno)
echo __('estudiantes.titulo');
```

### Uso con Variables

```php
// Traducción con variables dinámicas
echo __('mensajes.bienvenida_usuario', ['nombre' => 'Juan Pérez']);

// En el JSON:
// "mensajes.bienvenida_usuario": "Bienvenido {nombre} al sistema"
```

### Uso con Contexto

```php
// Sin contexto
echo __('agregar_nuevo');

// Con contexto (más específico)
echo __('agregar_nuevo', [], 'estudiantes');  // busca: estudiantes.agregar_nuevo
```

### Uso en HTML

```php
<!-- Botones -->
<button class="btn btn-primary">
    <?=__('general.guardar');?>
</button>

<!-- Placeholders -->
<input type="text" placeholder="<?=__('estudiantes.buscar_placeholder');?>">

<!-- Títulos -->
<h1><?=__('cursos.titulo');?></h1>

<!-- Descripciones -->
<p><?=__('estudiantes.descripcion_pagina');?></p>
```

---

## 📝 Convenciones de Nombres de Claves

### Estructura Recomendada

```
{contexto}.{descripcion_especifica}
```

### Ejemplos

| Tipo | Clave | Traducción ES | Traducción EN |
|------|-------|---------------|---------------|
| **General** | `general.agregar_nuevo` | Agregar nuevo | Add new |
| **General** | `general.buscar` | Buscar | Search |
| **General** | `general.guardar` | Guardar | Save |
| **Módulo** | `estudiantes.titulo` | Estudiantes | Students |
| **Módulo** | `estudiantes.agregar_nuevo` | Agregar nuevo estudiante | Add new student |
| **Módulo** | `cursos.formato_boletin` | Formato boletín | Report card format |
| **DataTables** | `datatables.search` | Buscar: | Search: |
| **Mensajes** | `mensajes.exito` | Éxito | Success |

### Reglas de Nomenclatura

1. **Siempre en minúsculas**
2. **Usar guiones bajos** para separar palabras: `agregar_nuevo_estudiante`
3. **Usar punto** para separar contexto: `estudiantes.agregar_nuevo`
4. **Ser descriptivo**: `buscar_por_nombre` en lugar de `buscar1`
5. **Agrupar por contexto**: Todas las claves de estudiantes empiezan con `estudiantes.`

---

## 🔧 Agregar Nuevas Traducciones

### Método 1: Editar JSON Manualmente

1. Abre `config-general/traducciones/ES.json`
2. Agrega la nueva clave y traducción:

```json
{
  "mi_modulo.mi_texto": "Mi texto en español"
}
```

3. Abre `config-general/traducciones/EN.json`
4. Agrega la misma clave con traducción en inglés:

```json
{
  "mi_modulo.mi_texto": "My text in English"
}
```

### Método 2: Usar el Sistema de Detección Automática

1. Usa la función `__()` en tu código con la clave deseada
2. El sistema registrará automáticamente la clave en `pendientes.json`
3. Revisa `config-general/traducciones/pendientes.json`
4. Copia las claves y tradúcelas en ES.json y EN.json

---

## 🛠️ Script Extractor de Traducciones

### ¿Para qué sirve?

Escanea archivos PHP y encuentra textos hardcodeados en español que necesitan ser traducidos.

### Cómo Usarlo

```bash
php scripts/extraer-traducciones-directivo.php
```

### Salida del Script

```
📂 Escaneando 576 archivos PHP en directivo/...

═══════════════════════════════════════════════════════════════
                    REPORTE DE EXTRACCIÓN
═══════════════════════════════════════════════════════════════

📊 ESTADÍSTICAS:
   • Total de archivos escaneados: 576
   • Archivos con textos en español: 192
   • Total de textos encontrados: 650

═══════════════════════════════════════════════════════════════

📁 TOP 10 ARCHIVOS CON MÁS TEXTOS PARA TRADUCIR:

   1. estudiantes-agregar.php (24 textos)
      • "Crear matrículas"
        → Clave sugerida: crear_matrículas
      ...

💾 Reporte completo guardado en: config-general/traducciones/reporte-extraccion.json
```

---

## 📦 Archivos Modificados en la Implementación

### Archivos de Sistema Creados

1. ✅ `config-general/Traductor.php` - Clase principal
2. ✅ `config-general/traducciones/ES.json` - Traducciones español
3. ✅ `config-general/traducciones/EN.json` - Traducciones inglés
4. ✅ `scripts/extraer-traducciones-directivo.php` - Script extractor

### Archivos Modificados

1. ✅ `config-general/idiomas.php` - Integración del nuevo sistema

### Archivos del Módulo Directivo Migrados (10 archivos de prueba)

1. ✅ `main-app/directivo/cursos.php`
2. ✅ `main-app/directivo/estudiantes.php`
3. ✅ `main-app/directivo/asignaturas.php`
4. ✅ `main-app/directivo/areas.php`
5. ✅ `main-app/directivo/cargas.php`
6. ✅ `main-app/directivo/cargas-visual.php`
7. ✅ `main-app/directivo/cursos-agregar-modal.php`
8. ✅ `main-app/directivo/usuarios.php`
9. ✅ `main-app/directivo/inscripciones.php`
10. ✅ `main-app/directivo/grupos.php`

---

## 🎯 Ejemplos de Migración

### Ejemplo 1: Botón Simple

**Antes:**
```php
<a href="#" class="btn btn-primary">
    Agregar nuevo <i class="fa fa-plus"></i>
</a>
```

**Después:**
```php
<a href="#" class="btn btn-primary">
    <?=__('general.agregar_nuevo');?> <i class="fa fa-plus"></i>
</a>
```

### Ejemplo 2: Placeholder en Input

**Antes:**
```php
<input type="text" placeholder="Buscar asignatura...">
```

**Después:**
```php
<input type="text" placeholder="<?=__('asignaturas.buscar_placeholder');?>">
```

### Ejemplo 3: Tabla con Headers

**Antes:**
```php
<th>Formato boletín</th>
<th>Matrícula</th>
<th>Pensión</th>
```

**Después:**
```php
<th><?=__('cursos.formato_boletin');?></th>
<th><?=__('cursos.matricula');?></th>
<th><?=__('cursos.pension');?></th>
```

### Ejemplo 4: Descripción de Página

**Antes:**
```php
<p class="text-muted">
    Aquí puedes gestionar toda la información de los estudiantes matriculados...
</p>
```

**Después:**
```php
<p class="text-muted">
    <?=__('estudiantes.descripcion_pagina');?>
</p>
```

### Ejemplo 5: DataTables en JavaScript

**Antes:**
```javascript
"language": {
    "lengthMenu": "Mostrar _MENU_ registros por página",
    "search": "Buscar:",
    "paginate": {
        "next": "Siguiente",
        "previous": "Anterior"
    }
}
```

**Después:**
```php
"language": {
    "lengthMenu": "<?=__('datatables.length_menu');?>",
    "search": "<?=__('datatables.search');?>",
    "paginate": {
        "next": "<?=__('datatables.next');?>",
        "previous": "<?=__('datatables.previous');?>"
    }
}
```

---

## 🔍 Funciones Disponibles

### `__($clave, $variables = [], $contexto = '')`

Función principal de traducción.

**Parámetros:**
- `$clave` (string): Clave de traducción
- `$variables` (array): Variables para reemplazar en el texto
- `$contexto` (string): Contexto opcional

**Ejemplos:**
```php
echo __('general.guardar');
echo __('mensajes.bienvenida', ['nombre' => 'Juan']);
echo __('agregar_nuevo', [], 'estudiantes');
```

### `___($clave, $idioma, $variables = [])`

Traducir en un idioma específico sin cambiar el idioma del sistema.

**Ejemplo:**
```php
// Forzar inglés independientemente del idioma del usuario
echo ___('general.guardar', 'EN');
```

### `Traductor::existe($clave, $contexto = '')`

Verificar si existe una traducción.

**Ejemplo:**
```php
if (Traductor::existe('estudiantes.titulo')) {
    echo __('estudiantes.titulo');
}
```

---

## ⚙️ Configuración

### Modo Desarrollo vs Producción

En `config-general/idiomas.php`:

```php
// MODO DESARROLLO: Registra automáticamente textos sin traducir
define('MODO_DESARROLLO', true);

// MODO PRODUCCIÓN: No registra pendientes (mejor rendimiento)
define('MODO_DESARROLLO', false);
```

### Cambiar Idioma del Usuario

El sistema detecta automáticamente el idioma del usuario desde:

```php
$datosUsuarioActual['uss_idioma']  // 1 = ES, 2 = EN
```

---

## 📊 Reporte de Implementación

### Estadísticas

- ✅ **10 archivos migrados** al nuevo sistema
- ✅ **650 textos detectados** en 192 archivos (pendientes de migración)
- ✅ **100% compatible** con el sistema anterior
- ✅ **80+ traducciones** configuradas en ES y EN

### Cobertura Actual

| Módulo | Archivos Migrados | Textos Traducidos |
|--------|-------------------|-------------------|
| **Cursos** | 2 | ~15 |
| **Estudiantes** | 1 | ~8 |
| **Asignaturas** | 1 | ~5 |
| **Áreas** | 1 | ~4 |
| **Cargas** | 2 | ~20 |
| **Usuarios** | 1 | ~3 |
| **Inscripciones** | 1 | ~5 |
| **General** | - | ~20 |
| **DataTables** | - | 10 |

---

## 🚦 Próximos Pasos

### Fase 1: Expansión (Recomendado)
1. Migrar los archivos restantes de `directivo/` (192 archivos con textos)
2. Extender a otros módulos (`docente/`, `estudiante/`, `acudiente/`)

### Fase 2: Optimización
1. Implementar caché con Redis para traducciones
2. Crear comando CLI para traducir en lote con APIs

### Fase 3: Más Idiomas
1. Agregar `FR.json` (francés)
2. Agregar `PT.json` (portugués)
3. Integrar con DeepL API para traducción automática

---

## ❓ FAQ

### ¿Puedo usar el sistema antiguo y el nuevo al mismo tiempo?

✅ **Sí**. El sistema es totalmente compatible. Puedes usar `$frases[numero]` y `__('clave')` en el mismo archivo.

### ¿Qué pasa si olvido traducir una clave?

El sistema muestra un **fallback formateado**. Por ejemplo:
- Clave: `estudiantes.agregar_nuevo`
- Fallback: "Agregar Nuevo"

### ¿Dónde encuentro los textos sin traducir?

En `config-general/traducciones/pendientes.json` (se genera automáticamente en modo desarrollo).

### ¿Cómo agrego un nuevo idioma?

1. Crea `config-general/traducciones/FR.json` (ejemplo francés)
2. Copia la estructura de `ES.json`
3. Traduce los valores
4. Modifica `idiomas.php` para soportar el código de idioma

---

## 📞 Soporte

Para dudas o problemas con el sistema de traducción:

1. Revisa este documento
2. Consulta el archivo `config-general/traducciones/reporte-extraccion.json`
3. Ejecuta el script extractor para identificar textos pendientes

---

## 📝 Changelog

### Versión 1.0 (22 Oct 2024)
- ✅ Sistema base implementado
- ✅ Clase `Traductor.php` creada
- ✅ Archivos JSON para ES y EN
- ✅ Script extractor de traducciones
- ✅ 10 archivos de directivo migrados como prueba
- ✅ 80+ traducciones configuradas
- ✅ Compatible con sistema anterior

---

## 🎉 Conclusión

El nuevo sistema de traducción está **listo para usar** y **funcionando**. 

Los 10 archivos modificados en `main-app/directivo/` son una **prueba exitosa** de que el sistema funciona correctamente.

**Recomendación:** Continuar migrando gradualmente el resto de archivos del módulo directivo y luego expandir a otros módulos.

---

*Documentación generada por SINTIA Dev Team - Octubre 2024*


