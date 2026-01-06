# Sistema de Auditoría Financiera

## Descripción

El sistema de auditoría financiera registra todos los cambios realizados en las tablas críticas del módulo financiero, tanto desde la aplicación como directamente en la base de datos. Esto proporciona un registro completo e inmutable de todas las modificaciones para análisis forense, cumplimiento y trazabilidad.

## Arquitectura

El sistema utiliza una combinación de:

1. **Triggers a nivel de base de datos**: Capturan automáticamente todos los cambios (INSERT, UPDATE, DELETE)
2. **Clase PHP `AuditoriaFinanciera`**: Complementa los triggers agregando contexto de la aplicación (usuario, IP, etc.)

### Tabla de Auditoría

**Nombre**: `auditoria_financiera`  
**Base de datos**: `BD_FINANCIERA`

#### Estructura

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | BIGINT UNSIGNED | ID autoincremental (PRIMARY KEY) |
| `tabla_afectada` | VARCHAR(100) | Nombre de la tabla que cambió |
| `registro_id` | VARCHAR(50) | ID del registro que cambió |
| `accion` | ENUM('INSERT', 'UPDATE', 'DELETE') | Tipo de acción |
| `valor_anterior` | JSON | Datos antes del cambio (completo) |
| `valor_nuevo` | JSON | Datos después del cambio (completo) |
| `cambios_detectados` | JSON | Solo campos modificados (para UPDATE) |
| `usuario_db` | VARCHAR(100) | Usuario de MySQL (USER()) |
| `usuario_app` | VARCHAR(50) | Usuario de SINTIA (si aplica) |
| `contexto` | ENUM('APP', 'BD_DIRECTA') | Origen del cambio |
| `ip_address` | VARCHAR(45) | IP del usuario (si aplica) |
| `institucion` | INT | ID de la institución |
| `year` | INT | Año académico |
| `fecha` | TIMESTAMP | Fecha y hora del cambio |

#### Índices

- `idx_tabla_registro`: (tabla_afectada, registro_id, fecha)
- `idx_usuario_db_fecha`: (usuario_db, fecha)
- `idx_usuario_app_fecha`: (usuario_app, fecha)
- `idx_institucion_year`: (institucion, year, fecha)
- `idx_accion_fecha`: (accion, fecha)
- `idx_contexto_fecha`: (contexto, fecha)
- `idx_fecha`: (fecha)

## Tablas Auditadas

### Prioridad ALTA

1. **finanzas_cuentas**: Facturas y movimientos financieros
2. **transaction_items**: Items de cada transacción
3. **payments_invoiced**: Abonos y pagos
4. **recurring_invoices**: Facturas recurrentes

### Prioridad MEDIA

5. **items**: Maestro de items
6. **finanzas_cuentas_bancarias**: Cuentas bancarias

### Campos Auditados por Tabla

#### finanzas_cuentas
- `fcu_id`, `fcu_fecha`, `fcu_detalle`, `fcu_valor`, `fcu_tipo`
- `fcu_anulado`, `fcu_status`, `fcu_consecutivo`, `fcu_usuario`
- `fcu_cerrado`, `fcu_fecha_cerrado`, `fcu_cerrado_usuario`
- `fcu_observaciones`
- `institucion`, `year`

#### transaction_items
- `id_autoincremental`, `id_transaction`, `id_item`, `item_name`
- `price`, `cantity`, `subtotal`, `discount`, `tax`
- `item_type`, `application_time`, `type_transaction`
- `factura_recurrente_id`, `description`
- `institucion`, `year`

#### payments_invoiced
- `id`, `invoiced`, `payment`, `payment_tipo`, `payment_method`, `type_payments`
- `payment_cuenta_bancaria_id`, `observation`, `note`, `fecha_documento`, `attachment`
- `is_deleted`
- `institucion`, `year`

#### recurring_invoices
- `id`, `user`, `date_start`, `date_finish`, `frequency`
- `days_in_month`, `detail`, `additional_value`, `invoice_type`
- `observation`, `is_deleted`, `responsible_user`
- `institucion`, `year`

#### items
- `item_id`, `name`, `price`, `description`
- `item_type`, `application_time`, `status`
- `institucion`, `year`

#### finanzas_cuentas_bancarias
- `cba_id`, `cba_nombre`, `cba_tipo`, `cba_metodo_pago_asociado`
- `institucion`, `year`

## Consultas Útiles

### Ver todos los cambios de una factura específica

```sql
SELECT 
    af.*,
    JSON_PRETTY(af.valor_anterior) as valor_anterior_formato,
    JSON_PRETTY(af.valor_nuevo) as valor_nuevo_formato,
    JSON_PRETTY(af.cambios_detectados) as cambios_formato
FROM auditoria_financiera af
WHERE af.tabla_afectada = 'finanzas_cuentas'
  AND af.registro_id = '123'
ORDER BY af.fecha DESC;
```

### Ver cambios realizados por un usuario de la aplicación

```sql
SELECT 
    af.*,
    JSON_PRETTY(af.cambios_detectados) as cambios_formato
FROM auditoria_financiera af
WHERE af.usuario_app = 'user123'
ORDER BY af.fecha DESC
LIMIT 100;
```

### Ver cambios realizados directamente en BD (contexto = 'BD_DIRECTA')

```sql
SELECT 
    af.*,
    af.usuario_db,
    JSON_PRETTY(af.cambios_detectados) as cambios_formato
FROM auditoria_financiera af
WHERE af.contexto = 'BD_DIRECTA'
ORDER BY af.fecha DESC
LIMIT 100;
```

### Ver cambios en un rango de fechas

```sql
SELECT 
    af.tabla_afectada,
    af.accion,
    COUNT(*) as total_cambios,
    COUNT(DISTINCT af.registro_id) as registros_afectados
FROM auditoria_financiera af
WHERE af.fecha BETWEEN '2024-01-01' AND '2024-01-31'
GROUP BY af.tabla_afectada, af.accion
ORDER BY total_cambios DESC;
```

### Detectar DELETE físicos (cambios críticos)

```sql
SELECT 
    af.*,
    JSON_PRETTY(af.valor_anterior) as datos_eliminados
FROM auditoria_financiera af
WHERE af.accion = 'DELETE'
ORDER BY af.fecha DESC
LIMIT 50;
```

### Ver cambios sospechosos (cambios masivos)

```sql
SELECT 
    af.fecha,
    af.usuario_app,
    af.usuario_db,
    af.tabla_afectada,
    COUNT(*) as cambios_en_periodo
FROM auditoria_financiera af
WHERE af.fecha >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
GROUP BY af.fecha, af.usuario_app, af.usuario_db, af.tabla_afectada
HAVING cambios_en_periodo > 100
ORDER BY cambios_en_periodo DESC;
```

### Ver historial completo de un registro específico

```sql
SELECT 
    af.*,
    CASE 
        WHEN af.accion = 'INSERT' THEN 'Creación'
        WHEN af.accion = 'UPDATE' THEN 'Actualización'
        WHEN af.accion = 'DELETE' THEN 'Eliminación'
    END as accion_espanol,
    JSON_PRETTY(af.cambios_detectados) as cambios_formato
FROM auditoria_financiera af
WHERE af.tabla_afectada = 'transaction_items'
  AND af.registro_id = '456'
ORDER BY af.fecha ASC;
```

### Análisis de cambios por tabla y acción

```sql
SELECT 
    af.tabla_afectada,
    af.accion,
    COUNT(*) as total,
    COUNT(DISTINCT af.registro_id) as registros_unicos,
    MIN(af.fecha) as primera_fecha,
    MAX(af.fecha) as ultima_fecha
FROM auditoria_financiera af
WHERE af.fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY af.tabla_afectada, af.accion
ORDER BY total DESC;
```

## Políticas de Retención

### Recomendaciones

- **Logs críticos (DELETE físicos)**: Mantener indefinidamente
- **Logs normales**: Considerar archivar después de 2 años
- **Monitoreo de tamaño**: Revisar mensualmente el tamaño de la tabla
- **Particionamiento**: Considerar particionamiento por fecha si el volumen crece significativamente (> 10GB)

### Estimación de Espacio

- **Tamaño promedio por registro**: ~2-5 KB (dependiendo del tamaño del JSON)
- **1000 cambios/día**: ~60-150 MB/mes
- **10,000 cambios/día**: ~600 MB - 1.5 GB/mes

## Seguridad

### Permisos

El usuario de la aplicación (`app_user`) debe tener:
- ✅ **INSERT** sobre `auditoria_financiera`
- ❌ **NO UPDATE** sobre `auditoria_financiera`
- ❌ **NO DELETE** sobre `auditoria_financiera`

Esto garantiza la inmutabilidad de los logs.

### Inmutabilidad

Los logs son **inmutables** una vez creados. Solo el usuario `root` o un DBA con permisos especiales puede modificar o eliminar registros de auditoría (si es absolutamente necesario para mantenimiento).

## Implementación

### Archivos SQL

1. `sql/crear_auditoria_financiera.sql` - Crear tabla y estructura
2. `sql/funciones_auditoria.sql` - Función helper para triggers
3. `sql/triggers_auditoria_finanzas_cuentas.sql` - Triggers para facturas
4. `sql/triggers_auditoria_transaction_items.sql` - Triggers para items de transacción
5. `sql/triggers_auditoria_payments_invoiced.sql` - Triggers para abonos
6. `sql/triggers_auditoria_recurring_invoices.sql` - Triggers para facturas recurrentes
7. `sql/triggers_auditoria_items.sql` - Triggers para items (prioridad media)
8. `sql/triggers_auditoria_cuentas_bancarias.sql` - Triggers para cuentas bancarias (prioridad media)

### Archivos PHP

- `main-app/class/App/Seguridad/AuditoriaFinanciera.php` - Clase helper para registrar desde PHP

### Archivos Integrados

- `main-app/directivo/movimientos-actualizar.php`
- `main-app/directivo/movimientos-anular.php`
- `main-app/directivo/factura-recurrente-actualizar.php`
- `main-app/class/Movimientos.php` (método `anularAbono()`)

## Notas Importantes

1. **Soft Delete**: El sistema maneja los diferentes patrones de soft delete existentes:
   - `fcu_anulado` (finanzas_cuentas)
   - `is_deleted` (payments_invoiced, recurring_invoices)
   - `status` (items)

2. **Rendimiento**: Los triggers se ejecutan automáticamente y no deben afectar significativamente el rendimiento. Si se detectan problemas, considerar:
   - Auditar solo campos críticos (ya implementado)
   - Optimizar índices
   - Particionamiento por fecha

3. **Compatibilidad**: El sistema es completamente compatible con el código existente y no modifica estructuras de tablas existentes.

4. **Testing**: Se recomienda probar:
   - Triggers con INSERT/UPDATE/DELETE desde aplicación
   - Triggers con cambios directos en BD (DBeaver/consola)
   - Verificar que los triggers no afecten el rendimiento de operaciones normales
   - Verificar que los logs se registren correctamente en ambos contextos (APP vs BD_DIRECTA)

