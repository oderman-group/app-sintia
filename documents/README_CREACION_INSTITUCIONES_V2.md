# 🚀 Sistema de Creación de Instituciones V2 - Guía Rápida

## ✨ ¿Qué se ha mejorado?

### Antes ❌
- Formulario antiguo con 2-3 pasos poco claros
- Sin validaciones en tiempo real
- Proceso síncrono (recarga completa)
- Campos faltantes en algunas tablas
- Sin feedback visual del progreso
- UI desactualizada

### Ahora ✅
- **Wizard moderno de 5 pasos** con indicadores visuales
- **Validaciones en tiempo real** asíncronas
- **Proceso asíncrono** con barra de progreso
- **TODOS los campos actualizados** según BDs actuales
- **Feedback en tiempo real** con logs
- **UI/UX profesional** y moderna

---

## 📂 Archivos Nuevos

### Interfaz Principal
- `main-app/directivo/dev-crear-nueva-bd-v2.php` - Wizard principal
- `main-app/directivo/dev-crear-nueva-bd-v2.js` - Lógica del wizard

### Endpoints AJAX
- `main-app/directivo/ajax-crear-bd-validar-siglas.php` - Validar siglas BD
- `main-app/directivo/ajax-crear-bd-validar-documento.php` - Validar documentos
- `main-app/directivo/ajax-crear-bd-validar.php` - Validación final
- `main-app/directivo/ajax-crear-bd-procesar.php` - Procesamiento principal

### Documentación
- `documents/SISTEMA_CREACION_INSTITUCIONES_V2.md` - Documentación completa
- `documents/README_CREACION_INSTITUCIONES_V2.md` - Este archivo

---

## 🎯 Cómo Usar

### Opción 1: Nueva Institución

1. Acceder a `dev-crear-nueva-bd-v2.php`
2. Seleccionar **"Nueva Institución"**
3. Completar datos de la institución:
   - Nombre completo
   - Siglas
   - Siglas para BD (solo minúsculas y números)
   - Año a crear
4. Completar datos del contacto principal:
   - Documento
   - Nombres y apellidos
   - Email (recibirá credenciales)
   - Celular (opcional)
5. Revisar confirmación
6. Hacer clic en **"Crear y Finalizar"**
7. Esperar el proceso (se mostrará progreso en tiempo real)

### Opción 2: Renovar Año

1. Acceder a `dev-crear-nueva-bd-v2.php`
2. Seleccionar **"Renovar Año"**
3. Seleccionar institución existente
4. Especificar año a crear (automáticamente sugiere año siguiente)
5. Revisar confirmación (muestra qué se copiará)
6. Hacer clic en **"Crear y Finalizar"**
7. Esperar el proceso

---

## 🔍 Validaciones en Tiempo Real

### Siglas de BD
- Se valida que no existan ya
- Solo permite letras minúsculas, números y guión bajo
- Muestra preview del nombre completo de BD

### Documento
- Verifica que no esté registrado como directivo
- Feedback inmediato

### Email
- Valida formato correcto
- Feedback visual

### Año
- Para renovación, verifica que no exista
- Verifica que exista el año anterior
- Calcula automáticamente año siguiente

---

## 📊 Campos Actualizados

### Tablas con Campos Nuevos Agregados:

#### `academico_grados`
- `gra_periodos_maximos`
- `gra_orden`
- Y todos los campos existentes

#### `academico_grupos`
- `gra_descripcion`

#### `academico_categorias_notas`
- `catn_descripcion`

#### `academico_notas_tipos`
- `notip_color`
- `notip_descripcion`

#### `academico_areas`
- `ar_estado`
- `ar_descripcion`
- `ar_color`

#### `academico_materias`
- `mat_estado`
- `mat_descripcion`
- `mat_orden`
- `mat_intensidad_horaria`

#### `usuarios` (BD General)
- `uss_lugar_expedicion`
- `uss_direccion`
- `uss_estado_civil`
- `uss_profesion`
- `uss_estado_laboral`
- `uss_nivel_academico`
- `uss_religion`
- `uss_tiene_hijos`
- `uss_numero_hijos`
- `uss_lugar_nacimiento`
- `uss_empresa_labor`
- `uss_firma`
- `uss_tipo_negocio`
- `uss_estrato`
- `uss_tipo_vivienda`
- `uss_medio_transporte`
- `uss_notificacion`
- `uss_solicitar_datos`
- `uss_institucion_municipio`
- `uss_parentezco`
- `uss_cambio_notificacion`
- Y más...

#### `academico_matriculas`
- `mat_acudiente2`
- `mat_inclusion`
- `mat_extranjero`
- `mat_ciudad_nacimiento`
- `mat_nacionalidad`
- `mat_fecha_ingreso`
- `mat_modalidad_estudio`
- `mat_colegio_procedente`
- `mat_repitente_agno`
- `mat_discapacidad_categoria`
- `mat_celular2`
- `mat_tipo_sangre`
- `mat_eps`
- `mat_rh`
- Y más...

#### `academico_cargas`
- `car_estado`
- `car_fecha_automatica`
- `car_evidencia`
- `car_inicio`
- `car_fin`
- `car_primer_acceso_docente`
- `car_ultimo_acceso_docente`
- `car_fecha_generar_informe_auto`

#### `configuracion`
- `conf_firma_inasistencia_planilla_notas_doc`
- `conf_puede_cambiar_grado_y_grupo`
- Y TODOS los campos de configuración

---

## ⚡ Características Técnicas

### Frontend
- **Framework CSS**: Bootstrap + Custom Styles
- **JavaScript**: Vanilla JS + jQuery
- **AJAX**: Peticiones asíncronas
- **Validaciones**: Debounce de 500ms
- **Animaciones**: CSS3 transitions

### Backend
- **Transacciones**: BEGIN...COMMIT con ROLLBACK
- **Validaciones**: Múltiples niveles
- **Seguridad**: Permisos DEV requeridos
- **Emails**: PHPMailer / Mailpit
- **Logs**: Error reporting automático

### Base de Datos
- **Inserts optimizados**: INSERT...SELECT
- **Índices respetados**: Performance mantenida
- **Transacciones ACID**: Integridad garantizada

---

## 🎨 Paleta de Colores

```css
/* Principal */
--primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

/* Estados */
--success: #28a745;
--error: #dc3545;
--warning: #ffc107;
--info: #17a2b8;
--neutral: #6c757d;

/* Fondos */
--bg-light: #f8f9fa;
--bg-white: #ffffff;
--border: #e9ecef;
```

---

## 🐛 Troubleshooting

### Error: "Siglas no disponibles"
**Solución**: Cambiar las siglas de BD, ya existe una institución con esas siglas

### Error: "Ya existe el año X"
**Solución**: El año ya fue creado, verificar en la lista de años de la institución

### Error: "No existe el año anterior"
**Solución**: Para renovar, debe existir el año anterior (ej: para crear 2025, debe existir 2024)

### Error: "Documento ya registrado"
**Solución**: Usar otro documento o modificar el usuario existente

### No se envió el email
**Verificar**:
- Configuración de email en sensitive.php
- En LOCAL/TEST usa Mailpit (puerto 1025)
- En PROD usa configuración SMTP real

---

## 📝 Notas Importantes

### Para Desarrolladores

1. **Siempre usar constantes** para nombres de BD:
   ```php
   BD_ADMIN, BD_ACADEMICA, BD_GENERAL, BD_ADMISIONES
   ```

2. **Respetar transacciones**:
   ```php
   mysqli_query($conexion, "BEGIN");
   // operaciones...
   mysqli_query($conexion, "COMMIT");
   ```

3. **Capturar errores**:
   ```php
   try {
       // código
   } catch(Exception $e) {
       mysqli_query($conexion, "ROLLBACK");
       include("../compartido/error-catch-to-report.php");
   }
   ```

### Para Testing

1. **Probar en LOCAL primero**
2. **Verificar en TEST**
3. **Validar en PROD**

### Ambientes

- **LOCAL**: `localhost` → `mobiliar_*`
- **TEST**: `developer.plataformasintia.com` → `mobiliar_*`
- **PROD**: `main.plataformasintia.com` → `mobiliar_*`

---

## ✅ Checklist de Verificación

Después de crear/renovar, verificar:

- [ ] Institución creada en tabla `instituciones`
- [ ] Configuración creada en tabla `configuracion`
- [ ] Información general creada
- [ ] Cursos/grados creados
- [ ] Grupos creados
- [ ] Áreas y materias creadas
- [ ] Usuarios creados (Nueva: 5 usuarios base)
- [ ] Matrículas copiadas (Renovación)
- [ ] Cargas copiadas (Renovación)
- [ ] Años actualizados en institución
- [ ] Email enviado (si aplica)

---

## 🚀 Próximos Pasos

Una vez creada la institución:

1. **Acceder** con las credenciales enviadas por email
2. **Configurar** información de la institución
3. **Ajustar** configuraciones académicas
4. **Crear** usuarios reales
5. **Matricular** estudiantes
6. **Asignar** cargas a docentes

---

## 📞 Soporte

**Logs disponibles**:
- Console del navegador (F12)
- `error-catch-to-report.php`
- Logs de MySQL

**Contacto**:
- Email: soporte@plataformasintia.com
- Sistema de tickets interno

---

## 📚 Documentación Completa

Ver archivo: `documents/SISTEMA_CREACION_INSTITUCIONES_V2.md`

---

**Versión**: 2.0.0  
**Fecha**: Octubre 23, 2025  
**Estado**: ✅ Listo para Testing

---

¡Disfruta del nuevo sistema! 🎉

