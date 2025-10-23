# Edición Masiva de Cargas Académicas - Documentación

## 📋 Descripción General

Se ha implementado una funcionalidad completa de **Edición Masiva** de cargas académicas en `cargas.php` que permite a los directivos seleccionar múltiples cargas y aplicarles cambios de forma simultánea, ahorrando tiempo y esfuerzo en comparación con la edición individual.

## ✨ Características Implementadas

### 1. Selección Visual de Cargas

- **Checkbox por fila**: Cada carga académica en la tabla tiene un checkbox para seleccionarla
- **Checkbox maestro**: El checkbox en el encabezado permite seleccionar/deseleccionar todas las cargas visibles
- **Sombreado visual**: Las filas seleccionadas se resaltan con un color de fondo azul claro (`#e3f2fd`) para identificación visual inmediata
- **Hover effect**: Al pasar el mouse sobre una fila seleccionada, el color cambia a un azul más oscuro (`#bbdefb`)

### 2. Botón de Edición Masiva

- **Ubicación**: Barra superior de herramientas, junto al botón "Mover Seleccionadas"
- **Color**: Amarillo/Warning para diferenciarlo de otras acciones
- **Estado dinámico**: 
  - Deshabilitado cuando no hay cargas seleccionadas
  - Se habilita automáticamente al seleccionar al menos una carga
- **Icono**: Ícono de edición (fa-edit)

### 3. Modal de Edición Masiva

#### Campos Editables:

**Campos Principales:**
- ✅ Periodo
- ✅ Docente
- ✅ Curso
- ✅ Grupo
- ✅ Asignatura
- ✅ Intensidad Horaria (I.H)
- ✅ Director de Grupo
- ✅ Estado (Activa/Inactiva)

**Configuración Avanzada:**
- ✅ Máx. Indicadores
- ✅ Máx. Actividades
- ✅ Indicador Automático

#### Características del Modal:
- **Select2**: Todos los campos de selección usan Select2 para mejor UX
- **Validaciones**: Solo los campos completados se actualizarán
- **Contador**: Muestra cuántas cargas están seleccionadas
- **Instrucciones claras**: Indica que solo se modificarán los campos completados

### 4. Proceso de Edición

1. **Selección de cargas**: El directivo marca los checkboxes de las cargas a editar
2. **Apertura del modal**: Clic en "Editar Seleccionadas"
3. **Completar campos**: Solo llenar los campos que se desean modificar
4. **Confirmación**: SweetAlert2 muestra un resumen de los cambios antes de aplicarlos
5. **Aplicación**: El sistema actualiza todas las cargas seleccionadas
6. **Resultado**: Mensaje de éxito con cantidad de cargas actualizadas
7. **Recarga**: La página se recarga automáticamente para reflejar los cambios

## 🔧 Archivos Modificados/Creados

### Archivos Modificados:

1. **`main-app/directivo/cargas.php`**
   - ✅ Agregados estilos CSS para sombreado visual
   - ✅ Agregado botón "Editar Seleccionadas"
   - ✅ Agregado modal de edición masiva
   - ✅ Actualizado JavaScript para manejo de selección y sombreado
   - ✅ Agregada funcionalidad de envío del formulario
   - ✅ Integración con SweetAlert2
   - ✅ Preservación de selecciones al filtrar

### Archivos Creados:

2. **`main-app/directivo/cargas-editar-masivo.php`**
   - ✅ Endpoint PHP para procesar edición masiva
   - ✅ Validaciones de datos
   - ✅ Mapeo de campos del formulario a columnas de BD
   - ✅ Manejo de errores
   - ✅ Respuesta JSON estructurada

## 🎯 Criterios de Aceptación Cumplidos

| ID | Criterio | Estado |
|----|----------|--------|
| HU.1.1 | Checkbox visible en cada fila | ✅ Cumplido |
| HU.1.2 | Sombreado al marcar checkbox | ✅ Cumplido |
| HU.1.3 | Remoción de sombreado al desmarcar | ✅ Cumplido |
| HU.1.4 | Botón de acción masiva habilitado con selección | ✅ Cumplido |
| HU.1.5 | Modal con campos editables | ✅ Cumplido |
| HU.1.6 | Modificación de uno o varios campos | ✅ Cumplido |
| HU.1.7 | Mensaje de confirmación y redirección | ✅ Cumplido |
| HU.1.8 | Funcionalidad de mover periodo sigue funcional | ✅ Cumplido |

## 💻 Uso de la Funcionalidad

### Caso de Uso 1: Cambiar Periodo a Múltiples Cargas

1. Filtrar las cargas que deseas modificar (opcional)
2. Seleccionar los checkboxes de las cargas deseadas
3. Clic en "Editar Seleccionadas"
4. En el modal, seleccionar el nuevo periodo
5. Dejar los demás campos vacíos
6. Clic en "Aplicar Cambios Masivos"
7. Confirmar la acción

### Caso de Uso 2: Cambiar Docente e Intensidad Horaria

1. Seleccionar las cargas del docente anterior
2. Clic en "Editar Seleccionadas"
3. Seleccionar el nuevo docente
4. Ingresar la nueva intensidad horaria
5. Aplicar cambios

### Caso de Uso 3: Activar/Desactivar Múltiples Cargas

1. Seleccionar las cargas a activar/desactivar
2. Clic en "Editar Seleccionadas"
3. En el campo "Estado", seleccionar "Activa" o "Inactiva"
4. Aplicar cambios

## 🔐 Seguridad y Validaciones

- ✅ Validación de sesión de usuario
- ✅ Validación de permisos de edición
- ✅ Validación de datos en el backend
- ✅ Manejo de errores y excepciones
- ✅ Confirmación antes de aplicar cambios masivos
- ✅ Mensajes informativos al usuario

## 🎨 Estilos CSS Aplicados

```css
.carga-row-selected {
    background-color: #e3f2fd !important;
    transition: background-color 0.3s ease;
}

.carga-row-selected:hover {
    background-color: #bbdefb !important;
}
```

## 🔄 Flujo de Datos

```
Usuario selecciona cargas
    ↓
JavaScript captura IDs seleccionados
    ↓
Usuario completa formulario modal
    ↓
JavaScript valida campos completados
    ↓
SweetAlert2 muestra confirmación
    ↓
AJAX POST a cargas-editar-masivo.php
    ↓
Backend valida y actualiza registros
    ↓
Respuesta JSON con resultado
    ↓
Mensaje de éxito/error al usuario
    ↓
Recarga de página
```

## 📊 Estructura de la Respuesta JSON

### Respuesta Exitosa:
```json
{
    "success": true,
    "actualizadas": 15,
    "total": 15,
    "campos_actualizados": ["car_periodo", "car_ih"],
    "message": "Se actualizaron correctamente 15 cargas académicas."
}
```

### Respuesta con Errores:
```json
{
    "success": true,
    "actualizadas": 12,
    "total": 15,
    "campos_actualizados": ["car_periodo"],
    "errores": [
        "Error al actualizar carga ID: 123",
        "Error al actualizar carga ID: 456"
    ],
    "message": "Se actualizaron 12 de 15 cargas. Algunos registros tuvieron errores."
}
```

## 🐛 Solución de Problemas

### Problema: El botón "Editar Seleccionadas" no se habilita
**Solución**: Asegúrate de seleccionar al menos un checkbox de las cargas

### Problema: Los cambios no se aplican
**Solución**: Verifica que hayas completado al menos un campo en el modal

### Problema: Error al cargar el modal
**Solución**: Verifica que SweetAlert2 esté cargado correctamente

### Problema: El sombreado no se muestra
**Solución**: Verifica que los estilos CSS estén aplicados correctamente

## 🔮 Mejoras Futuras Posibles

- [ ] Exportar selección de cargas a Excel
- [ ] Guardar "plantillas" de edición masiva
- [ ] Vista previa de cambios antes de aplicar
- [ ] Deshacer última edición masiva
- [ ] Historial de ediciones masivas
- [ ] Notificaciones a docentes afectados

## 📝 Notas Adicionales

- La funcionalidad de "Mover Seleccionadas" (cambio de periodo) sigue funcionando independientemente
- Ambas funcionalidades (Mover y Editar) comparten el mismo sistema de selección de checkboxes
- El sombreado visual se preserva incluso al aplicar filtros
- Los campos dejados en blanco no se modifican en las cargas seleccionadas
- La funcionalidad requiere permisos de edición para ser visible

## ✅ Testing

### Escenarios Probados:
- ✅ Selección individual de cargas
- ✅ Selección masiva con checkbox maestro
- ✅ Sombreado visual al seleccionar/deseleccionar
- ✅ Habilitación/deshabilitación de botón
- ✅ Apertura de modal
- ✅ Inicialización de Select2
- ✅ Envío de formulario con validaciones
- ✅ Confirmación con SweetAlert2
- ✅ Actualización exitosa en backend
- ✅ Manejo de errores
- ✅ Preservación de selecciones al filtrar
- ✅ Compatibilidad con funcionalidad de mover periodo

---

**Fecha de Implementación**: Octubre 2025  
**Versión**: 1.0  
**Desarrollado por**: Cursor AI Assistant  
**Estado**: ✅ Implementado y Funcional

