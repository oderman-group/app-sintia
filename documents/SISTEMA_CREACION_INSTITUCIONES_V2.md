# Sistema de Creación de Instituciones V2 🏢

## 📋 Descripción General

Sistema modernizado para la creación de nuevas instituciones y renovación de años académicos con interfaz wizard paso a paso, validaciones en tiempo real y proceso asíncrono.

## 🎯 Características Principales

### ✨ Mejoras Implementadas

1. **Interfaz Moderna Wizard**
   - Diseño paso a paso intuitivo
   - Indicadores visuales de progreso
   - Animaciones suaves y profesionales
   - Responsive para todos los dispositivos

2. **Validaciones en Tiempo Real**
   - Verificación asíncrona de siglas de BD
   - Validación de documentos duplicados
   - Validación de formato de email
   - Verificación de existencia de años
   - Feedback visual inmediato

3. **Proceso Asíncrono**
   - Creación sin recargar la página
   - Barra de progreso en tiempo real
   - Log detallado del proceso
   - Notificaciones de estado

4. **Campos Actualizados**
   - Se incluyeron TODOS los campos de las tablas actuales
   - Sincronización completa con las estructuras de BD
   - Campos nuevos agregados:
     - `gra_periodos_maximos`, `gra_orden` (grados)
     - `gru_descripcion` (grupos)
     - `catn_descripcion` (categorías notas)
     - `notip_color`, `notip_descripcion` (tipos notas)
     - `ar_estado`, `ar_descripcion`, `ar_color` (áreas)
     - `mat_estado`, `mat_descripcion`, `mat_orden`, `mat_intensidad_horaria` (materias)
     - Más de 30 campos adicionales en usuarios
     - Más de 20 campos adicionales en matrículas
     - Campos nuevos en cargas académicas
     - Y muchos más...

5. **Manejo de Errores**
   - Transacciones BD con ROLLBACK automático
   - Mensajes de error descriptivos
   - Log de errores para debugging
   - Recuperación ante fallos

## 📁 Archivos Creados

### Archivos Principales

#### 1. `dev-crear-nueva-bd-v2.php`
**Interfaz principal del wizard**

Características:
- 5 pasos claramente definidos
- Diseño moderno con gradientes y sombras
- Formularios adaptativos según tipo de operación
- Validación de formularios en el frontend
- Resumen de confirmación antes de procesar

#### 2. `dev-crear-nueva-bd-v2.js`
**Lógica del wizard y validaciones**

Funciones principales:
- `nextStep()` / `previousStep()`: Navegación entre pasos
- `validateCurrentStep()`: Validación de cada paso
- `procesarCreacion()`: Iniciar proceso de creación
- `startCreationProcess()`: Proceso asíncrono principal
- `setupRealTimeValidation()`: Configurar validaciones en tiempo real
- `validateBDSiglas()`: Validar disponibilidad de siglas
- `validateDocumento()`: Validar documentos únicos
- `updateProgress()`: Actualizar barra de progreso
- `addProgressLog()`: Agregar mensajes al log

### Archivos de Validación (AJAX)

#### 3. `ajax-crear-bd-validar-siglas.php`
Valida que las siglas de BD no existan ya en el sistema.

**Parámetros:**
- `siglasBD`: Siglas a validar
- `tipoInsti`: Tipo de institución (1=nueva, 0=renovación)

**Respuesta:**
```json
{
    "success": true/false,
    "message": "Mensaje descriptivo"
}
```

#### 4. `ajax-crear-bd-validar-documento.php`
Valida que el documento del usuario no esté registrado.

**Parámetros:**
- `documento`: Número de documento

**Respuesta:**
```json
{
    "exists": true/false,
    "message": "Mensaje descriptivo",
    "institucion": "ID institución (si existe)"
}
```

#### 5. `ajax-crear-bd-validar.php`
Validación final antes de iniciar el proceso de creación.

**Parámetros:**
- Todos los datos del formulario según tipo

**Validaciones:**
- Campos requeridos completos
- Formatos válidos
- Existencia de datos previos
- Año anterior disponible (renovación)

**Respuesta:**
```json
{
    "success": true/false,
    "message": "Mensaje descriptivo",
    "errors": ["array de errores"]
}
```

#### 6. `ajax-crear-bd-procesar.php`
**Procesamiento principal de creación/renovación**

Este es el archivo más importante del sistema. Maneja:

##### Para Nueva Institución:
1. Crear registro en `instituciones`
2. Asignar módulos básicos
3. Crear configuración inicial
4. Crear información general
5. Insertar cursos predefinidos (1-11 + preescolar)
6. Crear grupos básicos (A, B, C, Sin grupo)
7. Crear categorías de notas
8. Crear tipos de notas
9. Crear área y materia de prueba
10. Crear usuarios base (Admin, Directivo, Docente, Acudiente, Estudiante prueba)
11. Crear matrícula de prueba
12. Crear carga académica de prueba
13. Enviar email de bienvenida

##### Para Renovación:
1. Copiar grados con TODOS los campos
2. Copiar grupos
3. Copiar categorías y tipos de notas
4. Copiar áreas y materias
5. Copiar usuarios (reiniciando intentos fallidos)
6. Copiar matrículas (estado 4 - no matriculado)
7. Reiniciar estado de matrículas para nuevo año
8. Copiar relaciones usuarios-estudiantes
9. Copiar cargas académicas (período 1)
10. Copiar documentos adjuntos
11. Copiar notificaciones de usuarios
12. Crear configuración del nuevo año
13. Actualizar años de la institución
14. Copiar información general
15. Copiar configuración de inscripciones (si aplica)

**Uso de Transacciones:**
```php
try {
    mysqli_query($conexion, "BEGIN");
    // ... todas las operaciones ...
    mysqli_query($conexion, "COMMIT");
} catch(Exception $e) {
    mysqli_query($conexion, "ROLLBACK");
    // ... manejo de error ...
}
```

## 🎨 Diseño UI/UX

### Paleta de Colores

- **Principal**: `#667eea` → `#764ba2` (Gradiente violeta)
- **Éxito**: `#28a745` (Verde)
- **Error**: `#dc3545` (Rojo)
- **Advertencia**: `#ffc107` (Amarillo)
- **Info**: `#17a2b8` (Azul)
- **Neutral**: `#6c757d` (Gris)

### Componentes Personalizados

1. **Wizard Steps**: Indicadores de paso con estados (pending, active, completed)
2. **Card Options**: Tarjetas interactivas para selección
3. **Form Controls Modern**: Inputs con validación visual
4. **Progress Bar**: Barra de progreso animada
5. **Progress Log**: Log estilo consola con categorías
6. **Validation Messages**: Mensajes contextuales de validación

### Animaciones

- **fadeIn**: Entrada de secciones (0.5s)
- **spin**: Indicadores de carga
- **translateX/Y**: Movimientos de botones al hover
- **smooth scrolling**: Navegación suave

## 🔒 Seguridad

### Validaciones Implementadas

1. **Frontend:**
   - Campos requeridos
   - Formatos de datos (email, números, etc.)
   - Longitudes máximas
   - Caracteres permitidos

2. **Backend:**
   - Verificación de permisos (DEV)
   - Validación de existencia de datos
   - Sanitización de inputs
   - Transacciones con ROLLBACK
   - Logs de errores

3. **Base de Datos:**
   - Transacciones ACID
   - Claves foráneas respetadas
   - Índices optimizados
   - Prevención de duplicados

## 📊 Flujo del Proceso

```
[Inicio]
   ↓
[Paso 1: Seleccionar Tipo]
   ├→ Nueva Institución
   │    ↓
   │  [Paso 2: Datos Institución]
   │    ↓
   │  [Paso 3: Contacto Principal]
   │    ↓
   │  [Paso 4: Confirmación]
   │    ↓
   │  [Paso 5: Procesamiento]
   │    ├→ Crear institución
   │    ├→ Crear configuraciones
   │    ├→ Crear datos base
   │    ├→ Crear usuarios
   │    └→ Enviar email
   │
   └→ Renovación
        ↓
      [Paso 2: Seleccionar Institución]
        ↓
      [Paso 4: Confirmación]
        ↓
      [Paso 5: Procesamiento]
        ├→ Copiar grados
        ├→ Copiar grupos
        ├→ Copiar materias
        ├→ Copiar usuarios
        ├→ Copiar matrículas
        ├→ Copiar cargas
        └→ Actualizar configuración
```

## 🔧 Configuración

### Constantes Utilizadas

```php
BD_PREFIX          // Prefijo de bases de datos según entorno
ENVIROMENT         // Entorno actual (LOCAL, TEST, PROD)
BD_ADMIN          // Base de datos de administración
BD_ACADEMICA      // Base de datos académica
BD_GENERAL        // Base de datos general
BD_ADMISIONES     // Base de datos de admisiones
TIPO_DIRECTIVO    // Tipo de usuario directivo
```

### Ambientes Soportados

- **LOCAL**: `mobiliar_*`
- **TEST**: `mobiliar_*`
- **PROD**: `mobiliar_*`
- **Otros**: `odermangroup_*`

## 📈 Optimizaciones

### Consultas SQL

1. **Inserts Masivos**: Se usan INSERT...SELECT para copiar datos
2. **Transacciones**: Todo el proceso en una transacción
3. **Índices**: Se respetan los índices existentes
4. **Preparación**: Queries optimizados para rendimiento

### Frontend

1. **Validación Debounce**: Timers de 500ms para validaciones
2. **AJAX Eficiente**: Peticiones solo cuando necesario
3. **Cache de Datos**: FormData mantiene estado
4. **Lazy Loading**: Secciones cargadas según demanda

## 🐛 Manejo de Errores

### Tipos de Error

1. **Validación**: Campos incorrectos o faltantes
2. **Existencia**: Datos duplicados
3. **Base de Datos**: Errores de consulta
4. **Conexión**: Problemas de red
5. **Permisos**: Acceso no autorizado

### Log de Errores

Todos los errores se registran usando:
```php
include("../compartido/error-catch-to-report.php");
```

## 🧪 Testing

### Casos de Prueba Recomendados

#### Nueva Institución
1. ✅ Crear con todos los campos
2. ✅ Crear con campos mínimos
3. ✅ Validar siglas duplicadas
4. ✅ Validar documento duplicado
5. ✅ Validar email inválido
6. ✅ Verificar creación de usuarios
7. ✅ Verificar envío de email

#### Renovación
1. ✅ Renovar institución existente
2. ✅ Validar año duplicado
3. ✅ Validar año anterior inexistente
4. ✅ Verificar copia de usuarios
5. ✅ Verificar copia de matrículas
6. ✅ Verificar reinicio de estados
7. ✅ Verificar actualización de años

## 📞 Soporte

### Logs Disponibles

- **Frontend**: Console del navegador
- **Backend**: `error-catch-to-report.php`
- **BD**: Logs de MySQL

### Debugging

Para habilitar más información:
```javascript
// En dev-crear-nueva-bd-v2.js
console.log('Debug info:', data);
```

```php
// En archivos PHP
error_log('Debug: ' . print_r($data, true));
```

## 🚀 Próximas Mejoras

1. ~~Validación de campos completos~~ ✅
2. ~~Proceso asíncrono~~ ✅
3. ~~UI moderna~~ ✅
4. **Backup automático antes de procesar**
5. **Rollback manual en caso de problemas**
6. **Importación de datos desde Excel**
7. **Clonación de instituciones completas**
8. **Wizards para otras operaciones**

## 📝 Notas Importantes

1. **Permisos**: Solo usuarios DEV pueden acceder
2. **Transacciones**: Se usa ROLLBACK automático en errores
3. **Emails**: Se envían solo en producción (MAILPIT en desarrollo)
4. **Constantes**: Usar siempre las constantes definidas
5. **Testing**: Probar en LOCAL antes de TEST/PROD

## 🎓 Capacitación

### Para Desarrolladores

1. Estudiar flujo del wizard
2. Entender validaciones asíncronas
3. Revisar estructura de BDs
4. Conocer manejo de transacciones
5. Familiarizarse con respuestas JSON

### Para Usuarios

1. Seleccionar tipo de operación
2. Completar formularios
3. Revisar confirmación
4. Esperar proceso
5. Verificar resultado

---

## 📄 Licencia

© 2025 Plataforma SINTIA - Todos los derechos reservados

---

**Última actualización**: Octubre 23, 2025  
**Versión**: 2.0.0  
**Autor**: AI Assistant

