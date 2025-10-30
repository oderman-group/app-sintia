# 🔒 IMPLEMENTACIÓN DE RATE LIMITING - FASE 2.1

**Fecha**: Octubre 30, 2025  
**Estado**: ✅ COMPLETADO  
**Propósito**: Prevenir ataques de fuerza bruta contra el sistema de login

---

## 📋 RESUMEN

Se implementó un sistema robusto de Rate Limiting que controla y limita los intentos de login tanto por dirección IP como por nombre de usuario, previniendo efectivamente ataques de fuerza bruta.

---

## 🎯 OBJETIVOS CUMPLIDOS

- ✅ Bloqueo automático por IP después de múltiples intentos
- ✅ Bloqueo automático por usuario después de múltiples intentos
- ✅ Registro detallado de todos los intentos fallidos
- ✅ Limpieza automática de intentos al login exitoso
- ✅ Mensajes informativos con tiempo de espera
- ✅ Logs de seguridad con emojis para fácil identificación
- ✅ Dashboard de monitoreo en tiempo real
- ✅ Sistema de mantenimiento para limpiar datos antiguos
- ✅ Estadísticas de intentos fallidos

---

## 🏗️ ARQUITECTURA

### **Clase Principal**: `RateLimit`
**Ubicación**: `main-app/class/App/Seguridad/RateLimit.php`

#### Constantes de Configuración:
```php
MAX_INTENTOS_IP = 10;           // Máximo intentos por IP
MAX_INTENTOS_USUARIO = 5;       // Máximo intentos por usuario
TIEMPO_BLOQUEO_IP = 900;        // 15 minutos
TIEMPO_BLOQUEO_USUARIO = 1800;  // 30 minutos
VENTANA_TIEMPO = 3600;          // 1 hora para conteo
```

#### Métodos Públicos:
- `verificarBloqueoIP($ip)` - Verifica si una IP está bloqueada
- `verificarBloqueoUsuario($usuario)` - Verifica si un usuario está bloqueado
- `registrarIntentoFallido($usuario, $ip, $clave)` - Registra intento fallido
- `limpiarIntentos($ussId, $ip)` - Limpia intentos al login exitoso
- `formatearTiempoRestante($segundos)` - Formatea tiempo de espera
- `logBloqueo($tipo, $identificador, $ip, $tiempo)` - Log de bloqueos
- `obtenerEstadisticas($horas)` - Obtiene stats de intentos
- `limpiarIntentosAntiguos()` - Elimina registros >30 días

---

## 🔄 FLUJO DE AUTENTICACIÓN

### **1. Verificación Previa (ANTES de validar credenciales)**

```
Usuario ingresa credenciales
    ↓
Validar CSRF token
    ↓
Verificar bloqueo por IP ─────→ SI: Mostrar mensaje + tiempo espera
    ↓ NO
Verificar bloqueo por usuario ─→ SI: Mostrar mensaje + tiempo espera
    ↓ NO
Intentar autenticación
```

### **2. Manejo de Intentos Fallidos**

```
Intento de login fallido
    ↓
Registrar en usuarios_intentos_fallidos
    ↓
Incrementar uss_intentos_fallidos
    ↓
Verificar si alcanzó límite
    ↓
SI: Bloquear (15-30 min según tipo)
NO: Mostrar intentos restantes
```

### **3. Login Exitoso**

```
Login exitoso
    ↓
Resetear uss_intentos_fallidos = 0
    ↓
Limpiar registros Rate Limiting
    ↓
Log de login exitoso
    ↓
Establecer sesión
```

---

## 📊 TABLA UTILIZADA

**Tabla**: `BD_ADMIN.usuarios_intentos_fallidos`

**Campos**:
- `uif_id` - ID autoincremental
- `uif_usuarios` - uss_id del usuario (puede ser NULL si no existe)
- `uif_ip` - Dirección IP del intento
- `uif_clave` - Clave intentada (para análisis)
- `uif_institucion` - ID de institución
- `uif_year` - Año académico
- `uif_fecha` - Timestamp del intento

**Índices recomendados**:
```sql
CREATE INDEX idx_ip_fecha ON usuarios_intentos_fallidos(uif_ip, uif_fecha);
CREATE INDEX idx_usuario_fecha ON usuarios_intentos_fallidos(uif_usuarios, uif_fecha);
CREATE INDEX idx_fecha ON usuarios_intentos_fallidos(uif_fecha);
```

---

## 🔐 NIVELES DE PROTECCIÓN

### **Nivel 1: Por IP**
- **Límite**: 10 intentos en 1 hora
- **Bloqueo**: 15 minutos
- **Propósito**: Bloquear bots y ataques distribuidos

### **Nivel 2: Por Usuario**
- **Límite**: 5 intentos en 1 hora
- **Bloqueo**: 30 minutos
- **Propósito**: Proteger cuentas específicas

### **Nivel 3: Mensajes Progresivos**
- Intentos 1-2: Mensaje estándar
- Intentos 3-5: Muestra intentos restantes
- Intento 6+: Bloqueo con tiempo de espera

---

## 📝 LOGS GENERADOS

### **Intento Fallido**:
```
🔴 INTENTO DE LOGIN FALLIDO - Usuario: admin | IP: 192.168.1.100 | uss_id: USU123
```

### **Bloqueo Activado**:
```
🚨 BLOQUEO POR RATE LIMIT - Tipo: USUARIO | Usuario: admin | IP: 192.168.1.100 | Tiempo restante: 25 minutos
```

### **Login Exitoso**:
```
🟢 LOGIN EXITOSO - uss_id: USU123 | IP: 192.168.1.100
```

### **Mantenimiento**:
```
🧹 MANTENIMIENTO RATE LIMIT - Eliminados 1250 registros antiguos
```

---

## 📄 ARCHIVOS MODIFICADOS

### **Autenticación**:
- `main-app/controlador/autentico-async.php`
  - Agregado: `require_once RateLimit.php`
  - Agregado: Verificación de bloqueo por IP (líneas 60-70)
  - Agregado: Verificación de bloqueo por usuario (líneas 72-82)
  - Modificado: Registro de intentos fallidos con RateLimit
  - Agregado: Limpieza de intentos en login exitoso
  - Agregado: Mensajes con intentos restantes

### **Archivos Nuevos**:
1. `main-app/class/App/Seguridad/RateLimit.php` - Clase principal
2. `main-app/directivo/dev-seguridad-dashboard.php` - Dashboard de monitoreo
3. `main-app/directivo/ajax-rate-limit-mantenimiento.php` - Endpoint mantenimiento

---

## 📊 DASHBOARD DE SEGURIDAD

**URL**: `directivo/dev-seguridad-dashboard.php`

**Características**:
- 📊 Estadísticas de intentos fallidos (24h y 7 días)
- 🌐 Conteo de IPs únicas atacantes
- 🔝 Top 10 IPs con más intentos
- 📋 Configuración actual del sistema
- 🔧 Botón de mantenimiento manual
- 🎨 Diseño moderno con niveles de amenaza colorizados

**Niveles de Amenaza**:
- 🟢 **Bajo**: < 5 intentos
- 🔵 **Medio**: 5-9 intentos
- 🟡 **Alto**: 10-19 intentos
- 🔴 **Crítico**: ≥ 20 intentos

---

## 🛡️ MENSAJES AL USUARIO

### **Bloqueo por IP**:
```
🚫 Demasiados intentos fallidos desde tu red. 
Por favor espera 12 minutos antes de intentar nuevamente.
```

### **Bloqueo por Usuario**:
```
🚫 Demasiados intentos fallidos para este usuario. 
Por favor espera 25 minutos antes de intentar nuevamente.
```

### **Intentos Restantes**:
```
Contraseña incorrecta. Te quedan 3 intentos.
```

---

## 🔧 MANTENIMIENTO

### **Automático**:
- Los intentos se cuentan en ventana de 1 hora
- Después de 15-30 minutos, el bloqueo se levanta automáticamente
- Login exitoso limpia todos los intentos del usuario

### **Manual**:
- Acceder a: `dev-seguridad-dashboard.php`
- Click en "Limpiar Registros Antiguos"
- Elimina registros > 30 días
- Mantiene la BD optimizada

### **Via Cronjob** (recomendado):
```bash
# Ejecutar cada semana
0 2 * * 0 curl https://tudominio.com/main-app/directivo/ajax-rate-limit-mantenimiento.php
```

---

## 🧪 TESTING

### **Test 1: Bloqueo por IP**
1. Intentar login fallido 10 veces desde la misma IP
2. Verificar bloqueo de 15 minutos
3. Verificar mensaje con tiempo restante
4. Verificar log en `errores_local.log`

### **Test 2: Bloqueo por Usuario**
1. Intentar login con usuario incorrecto 5 veces
2. Verificar bloqueo de 30 minutos
3. Verificar mensaje con tiempo restante

### **Test 3: Login Exitoso**
1. Login exitoso después de intentos fallidos
2. Verificar que `uss_intentos_fallidos` = 0
3. Verificar log de login exitoso
4. Verificar que puede volver a intentar inmediatamente

### **Test 4: Mensajes Progresivos**
1. Intento 1: "Contraseña incorrecta"
2. Intento 3: "Te quedan 2 intentos"
3. Intento 5: "Te quedan 0 intentos" (último antes de bloqueo)
4. Intento 6: "Demasiados intentos..."

---

## 📈 ESTADÍSTICAS DISPONIBLES

El dashboard muestra:
- Total de intentos fallidos (24h y 7 días)
- IPs únicas que intentaron acceder
- Top 10 IPs atacantes
- Nivel de amenaza por IP

---

## 🔒 CONSIDERACIONES DE SEGURIDAD

### **Ventajas**:
- ✅ Previene ataques de fuerza bruta efectivamente
- ✅ No requiere CAPTCHA (mejor UX)
- ✅ Logs detallados para análisis forense
- ✅ Bloqueo temporal (no permanente)
- ✅ Distinción entre IP y usuario
- ✅ No afecta usuarios legítimos

### **Limitaciones**:
- ⚠️ IPs dinámicas pueden causar falsos positivos
- ⚠️ Proxies/VPNs pueden compartir IP
- ⚠️ Usuario puede intentar desde otra IP

### **Mejoras Futuras Posibles**:
- 🔮 Integración con Cloudflare/WAF
- 🔮 Sistema de whitelist para IPs confiables
- 🔮 Notificaciones por email al superar límites
- 🔮 Bloqueo permanente después de X bloqueos temporales
- 🔮 CAPTCHA después del 3er intento

---

## 🚀 PRÓXIMOS PASOS

**Completado**: Rate Limiting ✅

**Pendiente en Fase 2**:
- Security Logging (extendido)
- Input Sanitization (reforzado)
- SQL Injection (completar migración PDO)
- MIME Validation (mejorar uploads)

---

## 📞 SOPORTE

Si necesitas ajustar los límites o tiempos:
1. Editar constantes en `RateLimit.php`
2. Los cambios se aplican inmediatamente
3. No requiere migración de datos

**Valores actuales** (modificables):
- MAX_INTENTOS_IP: 10
- MAX_INTENTOS_USUARIO: 5
- TIEMPO_BLOQUEO_IP: 900 segundos (15 min)
- TIEMPO_BLOQUEO_USUARIO: 1800 segundos (30 min)
- VENTANA_TIEMPO: 3600 segundos (1 hora)

---

**Implementado por**: SINTIA Development Team  
**Versión**: 2.0 - Security Enhanced

