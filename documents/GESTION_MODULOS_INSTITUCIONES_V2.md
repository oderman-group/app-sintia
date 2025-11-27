# Sistema Moderno de Gestión de Módulos por Institución V2

## 📋 Descripción

Sistema completamente renovado para la gestión de módulos institucionales con una interfaz moderna, intuitiva y funcionalidad en tiempo real.

## 🎯 Características Principales

### ✨ Interfaz Moderna
- **Diseño Gradient**: Uso de gradientes modernos y efectos visuales atractivos
- **Animaciones Fluidas**: Transiciones suaves y feedback visual en tiempo real
- **Responsive Design**: Adaptable a cualquier dispositivo (móvil, tablet, desktop)
- **Dark Mode Ready**: Preparado para implementación de modo oscuro

### ⚡ Funcionalidad en Tiempo Real
- **Guardado Automático**: Los cambios se guardan inmediatamente mediante AJAX
- **Cambio de Institución Dinámico**: Selecciona y cambia entre instituciones sin recargar
- **Búsqueda en Vivo**: Filtra módulos mientras escribes
- **Feedback Instantáneo**: Notificaciones toast para cada acción

### 🎨 Componentes UI/UX

#### 1. Selector de Institución
- Dropdown elegante con Select2
- Vista previa de estadísticas en tiempo real
- Indicador de estado (Activa/Inactiva)
- Información básica visible (ID, NIT, BD)

#### 2. Tarjetas de Módulos
- Diseño tipo card con gradiente superior
- Toggle switches animados
- Iconos representativos
- Efecto hover con elevación
- Estado visual claro (activo/inactivo)

#### 3. Buscador y Filtros
- Buscador con ícono y placeholder descriptivo
- Filtros rápidos: Todos, Activos, Inactivos
- Búsqueda por: nombre, ID, descripción

#### 4. Acciones Masivas
- Activar todos los módulos de una vez
- Desactivar todos los módulos
- Confirmación antes de acciones masivas
- Indicador de progreso durante el proceso

## 📁 Archivos del Sistema

### Frontend
- **`main-app/directivo/dev-instituciones-editar-v2.php`**: Página principal
- **`main-app/css/instituciones-modulos-v2.css`**: Estilos personalizados
- **`main-app/js/instituciones-modulos-v2.js`**: Funcionalidad JavaScript

### Backend (AJAX)
- **`main-app/directivo/ajax-instituciones-modulos-guardar.php`**: Guardar/remover módulos
- **`main-app/directivo/ajax-instituciones-obtener-datos.php`**: Obtener datos de institución

## 🚀 Uso

### Acceso
```
URL: main-app/directivo/dev-instituciones-editar-v2.php
Requiere: Permisos de desarrollador (Modulos::verificarPermisoDev())
```

### Flujo de Trabajo

1. **Seleccionar Institución**
   - Usa el dropdown superior para cambiar de institución
   - Los datos se cargan automáticamente

2. **Gestionar Módulos**
   - Activa/desactiva módulos con el toggle switch
   - Los cambios se guardan al instante
   - Recibe confirmación visual de cada acción

3. **Buscar y Filtrar**
   - Escribe en el buscador para filtrar módulos
   - Usa los filtros rápidos para ver solo activos o inactivos

4. **Acciones Masivas**
   - Usa los botones superiores para activar/desactivar todos
   - Confirma la acción en el diálogo

## 🎨 Paleta de Colores

```css
--color-primary: #667eea      /* Púrpura azulado */
--color-secondary: #764ba2    /* Púrpura */
--color-success: #38ef7d      /* Verde brillante */
--color-danger: #f45c43       /* Rojo coral */
--color-bg: #f5f7fa          /* Gris claro */
```

## 📱 Responsive Breakpoints

- **Desktop**: > 1200px (grid 3-4 columnas)
- **Tablet**: 768px - 1200px (grid 2-3 columnas)
- **Mobile**: < 768px (grid 1 columna)

## ⚙️ Tecnologías Utilizadas

- **Frontend Framework**: Bootstrap 4
- **CSS**: CSS3 con Variables y Grid Layout
- **JavaScript**: jQuery + AJAX
- **Select2**: Para selectores mejorados
- **Font Awesome 6**: Para iconografía

## 🔒 Seguridad

- Validación de permisos en cada request
- Protección contra SQL Injection
- Validación de datos en servidor
- Historial de acciones registrado

## 📊 Funcionalidades AJAX

### Guardar Módulo
```javascript
POST: ajax-instituciones-modulos-guardar.php
Datos: {
    institucion_id: int,
    modulo_id: int,
    accion: 'agregar' | 'remover'
}
```

### Obtener Datos de Institución
```javascript
POST: ajax-instituciones-obtener-datos.php
Datos: {
    institucion_id: int
}
```

## 🎯 Mejoras Implementadas

### UX
- ✅ Feedback visual inmediato
- ✅ Animaciones suaves y naturales
- ✅ Loading states claros
- ✅ Notificaciones toast informativas
- ✅ Confirmaciones para acciones críticas

### UI
- ✅ Diseño limpio y espaciado
- ✅ Jerarquía visual clara
- ✅ Uso estratégico de colores
- ✅ Iconografía consistente
- ✅ Sombras y profundidad adecuadas

### Performance
- ✅ Carga asíncrona de datos
- ✅ Actualizaciones parciales del DOM
- ✅ CSS optimizado con variables
- ✅ Animaciones con GPU acceleration

### Accesibilidad
- ✅ Contraste de colores adecuado
- ✅ Focus states visibles
- ✅ Tooltips descriptivos
- ✅ Navegación por teclado

## 🐛 Manejo de Errores

- Errores de red: Notificación al usuario + revert visual
- Errores de servidor: Mensaje descriptivo
- Validaciones: Feedback antes de enviar
- Logging: Errores registrados en historial

## 🔄 Comparación con Versión Anterior

| Característica | V1 (Anterior) | V2 (Nueva) |
|----------------|---------------|------------|
| Diseño | Tabla básica | Cards modernas |
| Guardado | Submit form | AJAX tiempo real |
| Cambio institución | Reload página | Dinámico sin reload |
| Búsqueda | No disponible | En vivo |
| Acciones masivas | No disponible | Sí (activar/desactivar todos) |
| Responsive | Básico | Totalmente adaptable |
| Animaciones | No | Sí, suaves y modernas |
| Feedback | Básico | Toast notifications |

## 📝 Notas de Desarrollo

- El sistema usa la constante `INSTITUCION_ACTUAL` para mantener el estado
- Los módulos se marcan visualmente antes de confirmar en servidor
- Si falla el guardado, se revierte el cambio visual
- Compatible con el sistema de permisos existente
- Mantiene compatibilidad con el módulo de inscripciones

## 🎓 Mejores Prácticas Aplicadas

1. **Separation of Concerns**: HTML, CSS y JS separados
2. **Progressive Enhancement**: Funciona sin JavaScript básico
3. **Mobile First**: Diseño pensado desde móvil
4. **DRY Principle**: Código reutilizable y modular
5. **Error Handling**: Manejo robusto de errores
6. **User Feedback**: Comunicación clara de acciones

## 🚧 Futuras Mejoras Posibles

- [ ] Drag & drop para ordenar módulos
- [ ] Vista de módulos por categoría
- [ ] Exportar/importar configuraciones
- [ ] Historial de cambios por institución
- [ ] Modo oscuro (dark mode)
- [ ] Shortcuts de teclado
- [ ] Tutorial interactivo (onboarding)
- [ ] Comparar módulos entre instituciones

## 📞 Soporte

Para problemas o sugerencias, contactar al equipo de desarrollo.

---

**Versión**: 2.0  
**Fecha**: Octubre 2025  
**Autor**: Sistema SINTIA  
**Estado**: ✅ Producción Ready


