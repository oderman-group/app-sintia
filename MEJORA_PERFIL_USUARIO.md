# Mejora del Perfil de Usuario - Sistema Sintia

## Descripción General
Se ha rediseñado completamente la página de perfil de usuario con un enfoque moderno, organizado y funcional. El nuevo diseño incluye un sistema de pestañas para organizar mejor la información y una funcionalidad de recorte automático de fotos.

## Fecha de Implementación
22 de Octubre, 2025

## Última Actualización
22 de Octubre, 2025 - Se simplificó el formulario para que solo los datos básicos sean obligatorios

## Filosofía del Diseño

El nuevo perfil se basa en el principio de **"Datos Básicos Obligatorios, Información Complementaria Opcional"**:

- ✅ **Datos Obligatorios**: Solo nombres y apellidos (campos críticos)
- 📋 **Datos Opcionales**: Todo lo demás (profesión, dirección, estado civil, etc.)
- 🎯 **Objetivo**: Facilitar la experiencia del usuario sin obligarlo a llenar datos que puede completar después

## Cambios Realizados

### 1. Nuevo Diseño con Sistema de Pestañas

#### Archivo Principal Creado:
- `main-app/compartido/perfil-contenido-v2.php` - Nueva interfaz de perfil

#### Organización de la Información:

**Pestaña 1: Información Básica**
- Foto de perfil (con carga y recorte automático)
- Nombres y apellidos
- Documento de identidad
- Correo electrónico
- Teléfono y celular
- Firma digital

**Pestaña 2: Información Personal**
- Género
- Fecha de nacimiento
- Lugar de nacimiento
- Opción de mostrar edad
- Estado civil
- Religión
- Número de hijos

**Pestaña 3: Información Profesional** (Solo para no estudiantes)
- Nivel académico
- Área de desempeño
- Estado laboral
- Información del negocio (si aplica)
  - Tipo de negocio
  - Sitio web

**Pestaña 4: Información Residencial** (Solo para no estudiantes)
- Dirección de residencia
- Estrato
- Tipo de vivienda
- Medio de transporte usual

**Pestaña 5: Preferencias**
- Notificaciones por correo
- Última actualización del perfil

### 2. Sistema de Carga y Recorte de Fotos

#### Características:
- **Carga Simplificada**: El usuario puede hacer clic en su foto de perfil para cambiarla
- **Vista Previa**: Se muestra la imagen antes de recortar
- **Recorte Automático**: Utiliza la librería Croppie para recortar la imagen
- **Formato Cuadrado**: Todas las fotos se guardan en formato cuadrado perfecto
- **Rotación**: Permite rotar la imagen antes de recortar
- **Guardado Instantáneo**: La foto recortada se guarda en formato JPEG optimizado

#### Flujo de Trabajo:
1. Usuario selecciona una imagen (cualquier formato: PNG, JPG, JPEG)
2. Se abre un modal con la herramienta de recorte
3. Usuario ajusta el recorte y puede rotar la imagen
4. Al hacer clic en "Recortar y Guardar", se actualiza la vista previa
5. Al guardar el formulario, la imagen se guarda en el servidor

### 3. Diseño Visual Mejorado

#### Paleta de Colores Sintia:
- **Primario (60%)**: Blanco (#ffffff) - Fondos principales
- **Secundario (30%)**: Cian/Turquesa (#41c4c4) - Elementos interactivos
- **Acento (10%)**: Púrpura (#6017dc) - Elementos destacados

#### Mejoras de UX:
- **Header Degradado**: Fondo con gradiente de colores Sintia
- **Avatar Circular**: Foto de perfil circular con efecto hover
- **Pestañas Personalizadas**: Con indicador visual de pestaña activa
- **Formularios Organizados**: Mejor espaciado y agrupación lógica
- **Botones Modernos**: Con efectos hover y gradientes
- **Alertas Informativas**: Diseño elegante con bordes de colores
- **Campos Requeridos**: Marcados visualmente con asterisco rojo
- **Switch Toggles**: Interruptores modernos para opciones booleanas

### 4. Archivos Modificados

#### Archivos de Vista:
- `main-app/directivo/perfil.php` - Actualizado para usar perfil-contenido-v2.php
- `main-app/docente/perfil.php` - Actualizado para usar perfil-contenido-v2.php
- `main-app/estudiante/perfil.php` - Actualizado para usar perfil-contenido-v2.php
- `main-app/acudiente/perfil.php` - Actualizado para usar perfil-contenido-v2.php

#### Archivos de Backend:
- `main-app/compartido/perfil-actualizar.php` - Actualizado para manejar fotos recortadas desde el frontend

### 5. Funcionalidades Técnicas

#### Manejo de Imágenes:
```php
// Nuevo sistema de guardado de fotos recortadas
if (!empty($_POST['fotoRecortada'])) {
    $imgBase64 = $_POST['fotoRecortada'];
    $img = str_replace('data:image/jpeg;base64,', '', $imgBase64);
    $img = str_replace(' ', '+', $img);
    $imgData = base64_decode($img);
    // Guardado de la imagen...
}
```

#### JavaScript para Recorte:
- Inicialización de Croppie con viewport cuadrado (400x400)
- Funciones de rotación (izquierda y derecha)
- Conversión a base64 para envío al servidor
- Actualización de vista previa en tiempo real

#### Validaciones:
- Peso máximo de archivo: 1 MB
- Formatos permitidos: PNG, JPG, JPEG
- Recorte obligatorio en formato cuadrado

#### Campos Obligatorios vs Opcionales:

**Solo Obligatorios:**
- Primer nombre (uss_nombre) - `required`
- Primer apellido (uss_apellido1) - `required`

**Todos Opcionales:**
- Segundo nombre, segundo apellido
- Documento, email, teléfono, celular
- Género, fecha de nacimiento, lugar de nacimiento
- Estado civil, religión, número de hijos
- Nivel académico, área de desempeño, estado laboral
- Dirección, estrato, tipo de vivienda, medio de transporte
- Información del negocio
- Fotos y firma digital

### 6. Mejoras de Accesibilidad

- Labels descriptivos en todos los campos
- Campos requeridos claramente marcados
- Mensajes informativos sobre formatos y requisitos
- Indicadores visuales de estado (activo, hover)
- Textos de ayuda en campos complejos

### 7. Compatibilidad

#### Navegadores Soportados:
- Chrome (última versión)
- Firefox (última versión)
- Safari (última versión)
- Edge (última versión)

#### Dispositivos:
- Desktop (optimizado)
- Tablet (responsive)
- Mobile (responsive)

### 8. Dependencias

#### Librerías Utilizadas:
- **Croppie**: Para el recorte de imágenes
  - Ubicación: `librerias/croppie/`
  - Archivos: `croppie.js`, `croppie.css`
- **jQuery**: Para manipulación del DOM
- **Bootstrap**: Para estructura y componentes
- **Select2**: Para selectores mejorados
- **jQuery Toast**: Para notificaciones

## Ventajas del Nuevo Diseño

1. **Organización Mejorada**: La información está separada en categorías lógicas
2. **Menos Sobrecarga Visual**: Solo se muestra la información de una pestaña a la vez
3. **Mejor UX**: Proceso de carga de foto más intuitivo y profesional
4. **Diseño Moderno**: Uso de colores corporativos y elementos visualares atractivos
5. **Responsive**: Se adapta a diferentes tamaños de pantalla
6. **Fotos Uniformes**: Todas las fotos de perfil son cuadradas y consistentes
7. **Proceso Simplificado**: Ya no requiere navegación a páginas adicionales para recortar
8. **Sin Presión**: Solo pide lo esencial, el resto es opcional y se puede llenar cuando el usuario quiera
9. **Validación Inteligente**: Solo valida campos realmente obligatorios
10. **Flexibilidad**: Los usuarios pueden guardar con solo completar nombres y apellidos

## Mantenimiento Futuro

### Para Agregar Nuevos Campos:
1. Identificar la pestaña apropiada
2. Agregar el campo en `perfil-contenido-v2.php`
3. Actualizar `perfil-actualizar.php` para manejar el nuevo campo

### Para Modificar Estilos:
Los estilos están definidos en la sección `<style>` del archivo `perfil-contenido-v2.php`. Se recomienda:
- Mantener las variables CSS de colores Sintia
- Seguir la nomenclatura de clases existente
- Probar cambios en diferentes resoluciones

### Para Agregar Nuevas Pestañas:
1. Agregar elemento `<li>` en la sección de pestañas
2. Crear contenido de la pestaña en `<div class="tab-pane">`
3. Asegurar consistencia visual con pestañas existentes

## Notas Técnicas

### Seguridad:
- Validación de peso de archivo antes de subir
- Sanitización de datos con `mysqli_real_escape_string`
- Validación de formatos de imagen
- Conversión segura de base64

### Performance:
- Imágenes guardadas en formato JPEG optimizado
- Caché de imágenes con parámetro de versión (`?v=timestamp`)
- Carga diferida de pestañas (solo se procesa la activa)

### Manejo de Errores:
- Validación de campos requeridos antes de enviar
- Mensajes de error claros y específicos
- Notificaciones toast para confirmaciones

## Archivo Original

El archivo original `perfil-contenido.php` se mantiene intacto para compatibilidad y referencia. Si se necesita volver al diseño anterior, simplemente cambiar las referencias de `perfil-contenido-v2.php` a `perfil-contenido.php` en los archivos de perfil.

## Conclusión

Esta mejora representa un avance significativo en la experiencia de usuario del sistema Sintia, con un diseño más moderno, organizado y funcional que facilita la gestión de información personal de todos los usuarios de la plataforma.

