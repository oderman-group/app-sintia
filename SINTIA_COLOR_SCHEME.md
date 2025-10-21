# Sintia - Paleta de Colores 60-30-10

## Descripción
Se ha implementado la regla de diseño 60-30-10 para mejorar la legibilidad y experiencia visual de los usuarios de Sintia.

## Paleta de Colores

### Colores Principales
- **60% Blanco (#ffffff)** - Fondos principales de la aplicación
- **30% Cian/Turquesa (#41c4c4)** - Color secundario para elementos interactivos
- **10% Púrpura (#6017dc)** - Color de acento para elementos destacados

### Variables CSS Disponibles
```css
:root {
  --sintia-primary-bg: #ffffff;        /* 60% - Fondos principales */
  --sintia-secondary: #41c4c4;         /* 30% - Color secundario */
  --sintia-accent: #6017dc;           /* 10% - Color de acento */
  
  --sintia-text-primary: #333333;
  --sintia-text-secondary: #666666;
  --sintia-text-muted: #999999;
  
  --sintia-bg-light: #f8f9fa;
  --sintia-bg-hover: #f4f6f9;
  --sintia-bg-active: #e8f4f8;
  
  --sintia-border: #e9ecef;
  --sintia-border-light: #f1f3f4;
}
```

## Archivos Modificados

### 1. `config-general/assets/css/theme/light/theme-color.css`
- Actualizado el color del sidebar de #4680ff a #41c4c4
- Actualizado el color del header activo a #6017dc
- Mantenido el fondo blanco para el 60% del espacio

### 2. `config-general/assets/css/chat.css`
- Actualizado el scrollbar del chat a #41c4c4

### 3. `config-general/assets/css/material_style.css`
- Actualizado el menú principal a #41c4c4

### 4. `config-general/assets/css/sintia-color-scheme.css` (NUEVO)
- Archivo CSS personalizado con todas las variables y clases utilitarias
- Implementa la regla 60-30-10 de manera sistemática

## Clases Utilitarias Disponibles

### Colores de Texto
- `.text-primary-sintia` - Color secundario (#41c4c4)
- `.text-accent-sintia` - Color de acento (#6017dc)

### Colores de Fondo
- `.bg-primary-sintia` - Fondo secundario (#41c4c4)
- `.bg-accent-sintia` - Fondo de acento (#6017dc)

### Bordes
- `.border-primary-sintia` - Borde secundario (#41c4c4)
- `.border-accent-sintia` - Borde de acento (#6017dc)

### Iconos
- `.icon-primary` - Iconos en color secundario
- `.icon-accent` - Iconos en color de acento

## Implementación

Para usar la nueva paleta de colores en tu aplicación:

1. **Incluir el archivo CSS principal:**
```html
<link rel="stylesheet" href="config-general/assets/css/sintia-color-scheme.css">
```

2. **Usar las variables CSS:**
```css
.mi-elemento {
  background-color: var(--sintia-primary-bg);
  color: var(--sintia-secondary);
  border: 1px solid var(--sintia-border);
}
```

3. **Usar las clases utilitarias:**
```html
<div class="bg-primary-sintia text-white">
  <i class="icon-accent"></i>
  <span class="text-accent-sintia">Elemento destacado</span>
</div>
```

## Beneficios

1. **Mejor Legibilidad:** El contraste entre colores mejora la lectura
2. **Consistencia Visual:** Todos los elementos siguen la misma paleta
3. **Experiencia de Usuario:** Colores más suaves y profesionales
4. **Accesibilidad:** Mejor contraste para usuarios con dificultades visuales

## Mantenimiento

Para mantener la consistencia:
- Siempre usar las variables CSS definidas
- Evitar colores hardcodeados
- Seguir la regla 60-30-10 en nuevos elementos
- Probar el contraste antes de implementar cambios

## Compatibilidad

La nueva paleta es compatible con:
- Todos los navegadores modernos
- Modo claro y oscuro (preparado para futuras implementaciones)
- Dispositivos móviles y desktop
- Sistemas de accesibilidad
