# 🎯 MEJORAS EN AUTO-REGISTRO DE INSTITUCIONES

## 📋 RESUMEN DE CAMBIOS

Se simplificó y mejoró el proceso de auto-registro desde fuera de la aplicación (`registro.php`).

**Fecha:** 29 de Octubre de 2025  
**Archivos modificados:** 2

---

## ✅ CAMBIOS IMPLEMENTADOS

### 1️⃣ **Asignación Automática de TODOS los Módulos**

**Antes:**
- Usuario seleccionaba módulos específicos uno por uno con checkboxes
- Proceso confuso y largo
- Si no seleccionaba, solo asignaba 5 módulos básicos

**Después:**
- ✅ **TODOS** los módulos activos se relacionan automáticamente
- ✅ Sin necesidad de seleccionar manualmente
- ✅ Proceso más rápido y simple
- ✅ Garantiza acceso completo desde el inicio

**Código (Backend):**
```php
// Consultar TODOS los módulos activos
$consultaModulos = mysqli_query($conexion, "SELECT mod_id FROM BD_ADMIN.modulos WHERE mod_estado = 1");

if ($consultaModulos && mysqli_num_rows($consultaModulos) > 0) {
    $valoresModulos = [];
    while ($modulo = mysqli_fetch_array($consultaModulos, MYSQLI_BOTH)) {
        $valoresModulos[] = "($idInsti, ".$modulo['mod_id'].")";
    }
    
    if (!empty($valoresModulos)) {
        $sqlModulos = "INSERT INTO BD_ADMIN.instituciones_modulos (ipmod_institucion, ipmod_modulo) 
                       VALUES " . implode(',', $valoresModulos);
        mysqli_query($conexion, $sqlModulos);
    }
}
```

---

### 2️⃣ **Pregunta General: "¿Para qué usarías más SINTIA?"**

**Antes:**
- Paso 2: Selección manual de módulos individuales
- Lista larga de checkboxes
- Confuso para nuevos usuarios

**Después:**
- Paso 2: Pregunta simple con 4 opciones visuales
- Solo seleccionar UNA opción
- Información se envía por correo (no se guarda en BD)

**Opciones Disponibles:**

| Opción | Icono | Descripción |
|--------|-------|-------------|
| **Gestión Académica** | 📚 | Calificaciones, boletines, reportes académicos y seguimiento de estudiantes |
| **Gestión Administrativa** | 🏢 | Matrículas, finanzas, documentos y procesos administrativos |
| **Comunicación** | 💬 | Comunicados, mensajería, notificaciones y conexión con la comunidad |
| **Gestión Integral** | 🎯 | Todas las áreas: académico, administrativo, financiero y comunicación |

**HTML Implementado:**
```html
<div class="row g-3">
    <div class="col-md-6">
        <div class="uso-card" data-uso="academico" onclick="selectUso('academico')">
            <div class="uso-icon">
                <i class="bi bi-book-fill"></i>
            </div>
            <h5 class="uso-title">Gestión Académica</h5>
            <p class="uso-description">
                Calificaciones, boletines, reportes académicos...
            </p>
        </div>
    </div>
    <!-- ... otras 3 opciones ... -->
</div>

<input type="hidden" id="usoSintia" name="usoSintia" value="">
```

**JavaScript:**
```javascript
function selectUso(uso) {
    $('.uso-card').removeClass('selected');
    $(`.uso-card[data-uso="${uso}"]`).addClass('selected');
    $('#usoSintia').val(uso);
}
```

**Backend:**
```php
// Capturar uso de SINTIA (solo para enviar por correo, no se guarda en BD)
$usoSintia = isset($_POST['usoSintia']) ? $_POST['usoSintia'] : 'no especificado';
$usoSintiaTexto = '';

switch($usoSintia) {
    case 'academico': $usoSintiaTexto = 'Gestión Académica'; break;
    case 'administrativo': $usoSintiaTexto = 'Gestión Administrativa'; break;
    case 'comunicacion': $usoSintiaTexto = 'Comunicación'; break;
    case 'integral': $usoSintiaTexto = 'Gestión Integral'; break;
    default: $usoSintiaTexto = 'No especificado';
}

// Se incluye en el correo de bienvenida
$data['uso_sintia'] = $usoSintiaTexto;
```

---

### 3️⃣ **Tema Blanco por Defecto para Usuarios**

**Antes:**
- Usuarios se creaban sin tema específico (NULL)
- Tema naranja por defecto en algunos casos

**Después:**
- ✅ Tema blanco profesional por defecto para TODOS los usuarios
- ✅ 3 campos configurados automáticamente:

| Campo | Valor |
|-------|-------|
| `uss_tema_sidebar` | `white-sidebar-color` |
| `uss_tema_header` | `header-white` |
| `uss_tema_logo` | `logo-white` |

**Código:**
```php
INSERT INTO BD_GENERAL.usuarios(
    ..., uss_tema_sidebar, uss_tema_header, uss_tema_logo, institucion, year
) VALUES 
('1', ..., 'white-sidebar-color', 'header-white', 'logo-white', '".$idInsti."', '".$year."'),
('2', ..., 'white-sidebar-color', 'header-white', 'logo-white', '".$idInsti."', '".$year."'),
('3', ..., 'white-sidebar-color', 'header-white', 'logo-white', '".$idInsti."', '".$year."'),
('4', ..., 'white-sidebar-color', 'header-white', 'logo-white', '".$idInsti."', '".$year."'),
('5', ..., 'white-sidebar-color', 'header-white', 'logo-white', '".$idInsti."', '".$year."');
```

**Beneficios:**
- ✅ UI más profesional desde el primer ingreso
- ✅ Experiencia consistente para todos los usuarios
- ✅ Tema moderno y limpio

---

## 🎨 ESTILOS CSS NUEVOS

```css
.uso-card {
    border: 3px solid #e5e7eb;
    border-radius: 16px;
    padding: 2rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
}

.uso-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(102, 126, 234, 0.2);
    border-color: #667eea;
}

.uso-card.selected {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    box-shadow: 0 12px 30px rgba(102, 126, 234, 0.4);
}

.uso-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    margin-bottom: 1.5rem;
}

.uso-card.selected .uso-icon {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}
```

---

## 📧 INFORMACIÓN EN CORREO DE BIENVENIDA

**Variables disponibles en la plantilla:**

```php
$data['institucion_id']     // ID de la institución
$data['institucion_agno']   // Año académico  
$data['institucion_nombre'] // Nombre de la institución ✨
$data['usuario_id']         // ID del usuario
$data['usuario_email']      // Email del destinatario
$data['usuario_nombre']     // Nombre completo
$data['usuario_usuario']    // Usuario de acceso
$data['usuario_clave']      // Contraseña temporal
$data['uso_sintia']         // ✨ NUEVO: "Gestión Académica", "Comunicación", etc.
$data['url_acceso']         // ✨ URL directa para acceder
```

**Ejemplo de uso en la plantilla:**
```php
<p>Indicaste que usarías SINTIA principalmente para: 
   <strong><?= $data['uso_sintia'] ?></strong>
</p>
```

---

## 📊 COMPARATIVA ANTES/DESPUÉS

### **Paso 2 - Antes:**
```
┌──────────────────────────────────────┐
│ ¿Qué módulos te interesan?           │
│                                      │
│ ☐ Académico                          │
│ ☐ Disciplina                         │
│ ☐ Financiero                         │
│ ☐ Comunicación                       │
│ ☐ Cronograma                         │
│ ☐ Marketplace                        │
│ ... (hasta 15-20 módulos)           │
│                                      │
│ Has seleccionado: 0 módulo(s)       │
└──────────────────────────────────────┘
```

### **Paso 2 - Después:**
```
┌──────────────────────────────────────┐
│ ¿Para qué usarías más SINTIA?        │
│                                      │
│ ┌────────┐  ┌────────┐              │
│ │   📚   │  │   🏢   │              │
│ │Académico│ │ Admin  │              │
│ └────────┘  └────────┘              │
│ ┌────────┐  ┌────────┐              │
│ │   💬   │  │   🎯   │              │
│ │Comunic.│  │Integral│              │
│ └────────┘  └────────┘              │
└──────────────────────────────────────┘
```

**Mejoras:**
- ✅ Más simple y rápido
- ✅ Visualmente atractivo
- ✅ Fácil de entender
- ✅ Sin confusión
- ✅ Todos los módulos disponibles igual

---

## 🔧 ARCHIVOS MODIFICADOS

### 1. **`main-app/registro.php`**

**Cambios:**
- ❌ Eliminado: Sección de checkboxes de módulos
- ✅ Agregado: 4 cards de "uso de SINTIA"
- ✅ Agregado: Input hidden `usoSintia`
- ✅ Agregado: Función `selectUso()`
- ✅ Modificado: Validación del paso 2
- ✅ Eliminado: Función `actualizarContadorModulos()`
- ✅ Modificado: FormData envía `usoSintia` en lugar de `modulos[]`
- ✅ Agregado: Estilos CSS para `.uso-card`

**Líneas aproximadas:**
- Eliminadas: ~50
- Agregadas: ~120
- Modificadas: ~15

### 2. **`main-app/registro-guardar.php`**

**Cambios:**
- ❌ Eliminado: Procesamiento de módulos seleccionados por POST
- ✅ Agregado: Query dinámica para TODOS los módulos activos
- ✅ Agregado: Captura de `usoSintia` con switch para texto
- ✅ Modificado: INSERT de usuarios incluye `uss_tema_sidebar`, `uss_tema_header`, `uss_tema_logo`
- ✅ Modificado: Array `$data` del correo incluye `uso_sintia` y `url_acceso`
- ✅ Eliminado: Variable `$modulosSeleccionadosText` que causaba error

**Líneas aproximadas:**
- Eliminadas: ~20
- Agregadas: ~40
- Modificadas: ~5

---

## 🎯 FLUJO DE USUARIO MEJORADO

### **Paso 1:** Datos Personales
- Nombre, apellidos, email, celular (sin cambios)

### **Paso 2:** Información de Institución
- Nombre de institución, siglas, ciudad, cargo
- **NUEVO:** Seleccionar uso principal de SINTIA (4 opciones visuales)

### **Paso 3:** Verificación de Email
- Código de 6 dígitos (sin cambios)

### **Resultado:**
- Institución creada
- **Todos los módulos activos** relacionados automáticamente
- Usuario creado con tema blanco
- Correo de bienvenida enviado con:
  - Credenciales
  - Uso seleccionado de SINTIA
  - URL directa de acceso

---

## 💡 VALIDACIONES

### **Frontend (registro.php):**
```javascript
// Validar que seleccionó un uso
const usoSeleccionado = $('#usoSintia').val();
if (!usoSeleccionado) {
    alert('Por favor selecciona para qué usarías más SINTIA.');
    valido = false;
}
```

### **Backend (registro-guardar.php):**
```php
// Capturar con fallback
$usoSintia = isset($_POST['usoSintia']) ? $_POST['usoSintia'] : 'no especificado';

// Convertir a texto legible
switch($usoSintia) {
    case 'academico': $usoSintiaTexto = 'Gestión Académica'; break;
    // ...
}
```

---

## 📧 PLANTILLA DE EMAIL

**Variables adicionales disponibles:**

```php
$data['uso_sintia']    // ✨ NUEVO: Uso principal seleccionado
$data['url_acceso']    // ✨ NUEVO: URL directa para acceder
$data['institucion_nombre'] // ✨ NUEVO: Nombre de la institución
```

**Ejemplo de uso en plantilla:**

```html
<p>
    <strong>Uso principal de SINTIA:</strong>  
    <?= $data['uso_sintia'] ?>
</p>

<p>
    <a href="<?= $data['url_acceso'] ?>" style="...">
        Acceder a mi cuenta
    </a>
</p>
```

---

## 🎨 EXPERIENCIA DE USUARIO

### **Ventajas del nuevo diseño:**

1. ✅ **Más rápido:** De ~15 checkboxes a 1 selección simple
2. ✅ **Más claro:** Cards grandes con iconos e descripciones
3. ✅ **Menos confusión:** No necesita saber qué es cada módulo
4. ✅ **Información útil:** El equipo SINTIA sabe qué necesita el usuario
5. ✅ **Mismo acceso:** Obtiene todos los módulos de todas formas

### **Visual Feedback:**

- **Hover:** Card se eleva y cambia borde a púrpura
- **Seleccionado:** 
  - Fondo degradado púrpura claro
  - Icono cambia a verde
  - Animación de pulso en el icono
  - Sombra más pronunciada

---

## 🔐 SEGURIDAD

- ✅ Campo `usoSintia` NO se guarda en BD (solo informativo)
- ✅ Validación en frontend y backend
- ✅ Switch con valores controlados (no acepta cualquier valor)
- ✅ Tema blanco aplicado a todos los usuarios (consistencia)

---

## 🔄 COMPATIBILIDAD

### **Retrocompatibilidad:**
- ✅ Instituciones creadas antes siguen funcionando
- ✅ No afecta módulos ya asignados
- ✅ Solo aplica a **nuevos registros**

### **Correo de bienvenida:**
- ✅ Template puede usar nuevas variables
- ✅ Si no existen, no rompe (son opcionales)
- ✅ Compatible con template antiguo

---

## 📝 VALIDACIONES IMPLEMENTADAS

### **Paso 2:**
```javascript
// Todos los campos institucionales + uso de SINTIA
if (!$('#nombreIns').val().trim()) valido = false;
if (!$('#siglasInst').val().trim()) valido = false;
if (!$('#ciudad').val().trim()) valido = false;
if (!$('#cargo').val().trim()) valido = false;
if (!$('#usoSintia').val()) {
    alert('Por favor selecciona para qué usarías más SINTIA.');
    valido = false;
}
```

---

## 🎯 BENEFICIOS

| Aspecto | Antes | Después |
|---------|-------|---------|
| **Tiempo en Paso 2** | ~2-3 min | ~30 seg |
| **Clicks requeridos** | 5-15 | 1 |
| **Confusión** | Alta | Baja |
| **Módulos disponibles** | Parciales | Todos |
| **Información para equipo** | Lista de IDs | Uso claro |
| **UI/UX** | Checkboxes | Cards modernas |

---

## 🧪 TESTING RECOMENDADO

1. **Ir a:** `/app-sintia/main-app/registro.php`
2. **Paso 1:** Completar datos personales
3. **Paso 2:** 
   - Completar datos de institución
   - Seleccionar una opción de uso (ej: "Gestión Académica")
   - Verificar que la card se marca visualmente
4. **Paso 3:** Verificar código
5. **Verificar en BD:**
   - ✅ Todos los módulos activos relacionados
   - ✅ Tema blanco en los 5 usuarios
6. **Verificar correo recibido:**
   - ✅ Incluye "Uso principal: Gestión Académica"
   - ✅ Incluye URL de acceso directo

---

## 📁 RESUMEN TÉCNICO

| Item | Cantidad |
|------|----------|
| **Archivos modificados** | 2 |
| **Líneas agregadas** | ~160 |
| **Líneas eliminadas** | ~70 |
| **Campos BD nuevos** | 3 (tema) |
| **Nuevas funciones JS** | 1 (`selectUso`) |
| **Errores de linter** | 0 ✅ |

---

**Estado:** ✅ COMPLETADO  
**Compatibilidad:** ✅ Backwards compatible  
**Testing:** Pendiente de usuario

