<?php
include("session.php");

$idPaginaInterna = 'DV0005';

include("../compartido/historial-acciones-guardar.php");

Modulos::verificarPermisoDev();

require_once(ROOT_PATH."/main-app/class/App/Seguridad/RateLimit.php");

// Obtener estadísticas
$stats24h = RateLimit::obtenerEstadisticas(24);
$stats7d = RateLimit::obtenerEstadisticas(24 * 7);

include("../compartido/head.php");
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.security-dashboard {
    padding: 20px;
}

.page-header-security {
    background: var(--danger-gradient);
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 30px;
    color: white;
    box-shadow: 0 10px 30px rgba(239, 68, 68, 0.3);
}

.page-header-security h2 {
    margin: 0 0 10px 0;
    font-size: 28px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-header-security p {
    margin: 0;
    opacity: 0.9;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card-security {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    border-left: 4px solid;
    transition: all 0.3s ease;
}

.stat-card-security:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.stat-card-security.danger { border-color: #ef4444; }
.stat-card-security.warning { border-color: #f59e0b; }
.stat-card-security.info { border-color: #3b82f6; }

.stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
}

.stat-title {
    font-size: 14px;
    color: #6b7280;
    font-weight: 600;
    text-transform: uppercase;
}

.stat-icon-security {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
}

.stat-icon-security.danger { background: var(--danger-gradient); }
.stat-icon-security.warning { background: var(--warning-gradient); }
.stat-icon-security.info { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }

.stat-value {
    font-size: 36px;
    font-weight: 800;
    color: #1f2937;
}

.stat-period {
    font-size: 12px;
    color: #9ca3af;
    margin-top: 5px;
}

.card-white {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}

.card-header-custom {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e5e7eb;
}

.card-header-custom h4 {
    margin: 0;
    color: #1f2937;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}

.table-security {
    width: 100%;
    border-collapse: collapse;
}

.table-security thead {
    background: #f9fafb;
}

.table-security thead th {
    padding: 12px;
    text-align: left;
    font-size: 12px;
    font-weight: 700;
    color: #4b5563;
    text-transform: uppercase;
    border-bottom: 2px solid #e5e7eb;
}

.table-security tbody tr {
    border-bottom: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.table-security tbody tr:hover {
    background: #f9fafb;
}

.table-security tbody td {
    padding: 12px;
}

.ip-badge {
    font-family: 'Courier New', monospace;
    background: #f3f4f6;
    padding: 5px 10px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
}

.alert-security {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border-left: 4px solid #f59e0b;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 25px;
}

.alert-security h4 {
    color: #92400e;
    font-weight: 700;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.alert-security p {
    color: #78350f;
    margin: 0;
}

.btn-modern {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-modern.btn-primary {
    background: var(--primary-gradient);
    color: white;
}

.btn-modern.btn-danger {
    background: var(--danger-gradient);
    color: white;
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.config-section {
    background: #f9fafb;
    border-radius: 10px;
    padding: 20px;
    margin: 20px 0;
}

.config-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #e5e7eb;
}

.config-item:last-child {
    border-bottom: none;
}

.config-label {
    font-weight: 600;
    color: #1f2937;
}

.config-value {
    color: #667eea;
    font-weight: 700;
    font-family: 'Courier New', monospace;
}
</style>

</head>
<?php include("../compartido/body.php"); ?>

<div class="page-wrapper">
    <?php include("../compartido/encabezado.php"); ?>
    <?php include("../compartido/panel-color.php"); ?>
    
    <div class="page-container">
        <?php include("../compartido/menu.php"); ?>
        
        <div class="page-content-wrapper">
            <div class="page-content security-dashboard">
                
                <!-- Header -->
                <div class="page-header-security">
                    <h2>
                        <i class="fas fa-shield-alt"></i>
                        Dashboard de Seguridad - Rate Limiting
                    </h2>
                    <p>Monitoreo de intentos de acceso y protección contra fuerza bruta</p>
                </div>
                
                <!-- Alert -->
                <div class="alert-security">
                    <h4>
                        <i class="fas fa-info-circle"></i>
                        Sistema de Protección Activo
                    </h4>
                    <p>
                        El sistema está monitoreando activamente todos los intentos de login y bloqueando automáticamente 
                        direcciones IP y usuarios que excedan los límites de seguridad configurados.
                    </p>
                </div>
                
                <!-- Stats 24h -->
                <h3 style="margin-bottom: 15px; color: #1f2937;">
                    <i class="fas fa-clock"></i> Últimas 24 Horas
                </h3>
                <div class="stats-grid">
                    <div class="stat-card-security danger">
                        <div class="stat-header">
                            <div class="stat-title">Intentos Fallidos</div>
                            <div class="stat-icon-security danger">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?= number_format($stats24h['total_intentos']); ?></div>
                        <div class="stat-period">en las últimas 24 horas</div>
                    </div>
                    
                    <div class="stat-card-security warning">
                        <div class="stat-header">
                            <div class="stat-title">IPs Únicas</div>
                            <div class="stat-icon-security warning">
                                <i class="fas fa-network-wired"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?= number_format($stats24h['ips_unicas']); ?></div>
                        <div class="stat-period">direcciones IP diferentes</div>
                    </div>
                </div>
                
                <!-- Stats 7d -->
                <h3 style="margin-bottom: 15px; margin-top: 30px; color: #1f2937;">
                    <i class="fas fa-calendar-week"></i> Últimos 7 Días
                </h3>
                <div class="stats-grid">
                    <div class="stat-card-security danger">
                        <div class="stat-header">
                            <div class="stat-title">Intentos Fallidos</div>
                            <div class="stat-icon-security danger">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?= number_format($stats7d['total_intentos']); ?></div>
                        <div class="stat-period">en los últimos 7 días</div>
                    </div>
                    
                    <div class="stat-card-security warning">
                        <div class="stat-header">
                            <div class="stat-title">IPs Únicas</div>
                            <div class="stat-icon-security warning">
                                <i class="fas fa-network-wired"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?= number_format($stats7d['ips_unicas']); ?></div>
                        <div class="stat-period">direcciones IP diferentes</div>
                    </div>
                </div>
                
                <!-- Configuración Actual -->
                <div class="card-white">
                    <div class="card-header-custom">
                        <h4>
                            <i class="fas fa-cog"></i>
                            Configuración de Rate Limiting
                        </h4>
                    </div>
                    
                    <div class="config-section">
                        <div class="config-item">
                            <div class="config-label">
                                <i class="fas fa-network-wired"></i>
                                Máximo de intentos por IP
                            </div>
                            <div class="config-value"><?= RateLimit::MAX_INTENTOS_IP; ?> intentos</div>
                        </div>
                        
                        <div class="config-item">
                            <div class="config-label">
                                <i class="fas fa-user"></i>
                                Máximo de intentos por usuario
                            </div>
                            <div class="config-value"><?= RateLimit::MAX_INTENTOS_USUARIO; ?> intentos</div>
                        </div>
                        
                        <div class="config-item">
                            <div class="config-label">
                                <i class="fas fa-clock"></i>
                                Tiempo de bloqueo por IP
                            </div>
                            <div class="config-value"><?= (RateLimit::TIEMPO_BLOQUEO_IP / 60); ?> minutos</div>
                        </div>
                        
                        <div class="config-item">
                            <div class="config-label">
                                <i class="fas fa-clock"></i>
                                Tiempo de bloqueo por usuario
                            </div>
                            <div class="config-value"><?= (RateLimit::TIEMPO_BLOQUEO_USUARIO / 60); ?> minutos</div>
                        </div>
                        
                        <div class="config-item">
                            <div class="config-label">
                                <i class="fas fa-hourglass-half"></i>
                                Ventana de tiempo para conteo
                            </div>
                            <div class="config-value"><?= (RateLimit::VENTANA_TIEMPO / 60); ?> minutos</div>
                        </div>
                    </div>
                </div>
                
                <!-- Top IPs Atacantes (24h) -->
                <?php if (!empty($stats24h['top_ips'])): ?>
                <div class="card-white">
                    <div class="card-header-custom">
                        <h4>
                            <i class="fas fa-skull-crossbones"></i>
                            Top 10 IPs con Intentos Fallidos (24h)
                        </h4>
                    </div>
                    
                    <div style="overflow-x: auto;">
                        <table class="table-security">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Dirección IP</th>
                                    <th>Intentos Fallidos</th>
                                    <th>Nivel de Amenaza</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $contador = 1;
                                foreach ($stats24h['top_ips'] as $ipData): 
                                    $nivelAmenaza = 'Bajo';
                                    $colorAmenaza = '#10b981';
                                    
                                    if ($ipData['intentos'] >= 20) {
                                        $nivelAmenaza = 'Crítico';
                                        $colorAmenaza = '#ef4444';
                                    } elseif ($ipData['intentos'] >= 10) {
                                        $nivelAmenaza = 'Alto';
                                        $colorAmenaza = '#f59e0b';
                                    } elseif ($ipData['intentos'] >= 5) {
                                        $nivelAmenaza = 'Medio';
                                        $colorAmenaza = '#3b82f6';
                                    }
                                ?>
                                <tr>
                                    <td><strong><?= $contador++; ?></strong></td>
                                    <td><span class="ip-badge"><?= htmlspecialchars($ipData['uif_ip']); ?></span></td>
                                    <td><strong style="color: #ef4444; font-size: 18px;"><?= $ipData['intentos']; ?></strong></td>
                                    <td>
                                        <span style="background: <?= $colorAmenaza; ?>; color: white; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">
                                            <?= $nivelAmenaza; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-success" 
                                                onclick="desbloquearIPRapido('<?= htmlspecialchars($ipData['uif_ip']); ?>')"
                                                title="Desbloquear esta IP">
                                            <i class="fas fa-unlock"></i> Desbloquear
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="card-white">
                    <div style="text-align: center; padding: 40px;">
                        <i class="fas fa-check-circle" style="font-size: 64px; color: #10b981; margin-bottom: 15px;"></i>
                        <h3 style="color: #1f2937; margin-bottom: 10px;">¡Todo Tranquilo!</h3>
                        <p style="color: #6b7280;">No se han detectado intentos fallidos en las últimas 24 horas.</p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Desbloqueo Manual -->
                <div class="card-white">
                    <div class="card-header-custom">
                        <h4>
                            <i class="fas fa-unlock"></i>
                            Desbloquear IP o Usuario
                        </h4>
                    </div>
                    
                    <div style="background: #fef3c7; padding: 15px; border-radius: 8px; border-left: 4px solid #f59e0b; margin-bottom: 20px;">
                        <p style="margin: 0; color: #78350f; font-size: 14px;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Advertencia:</strong> Usa esta función solo cuando sea necesario desbloquear manualmente una IP o usuario legítimo.
                        </p>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <!-- Desbloquear IP -->
                        <div style="background: #f9fafb; padding: 20px; border-radius: 10px; border: 2px solid #e5e7eb;">
                            <h5 style="margin: 0 0 15px 0; color: #1f2937; font-weight: 700;">
                                <i class="fas fa-network-wired"></i> Desbloquear IP
                            </h5>
                            <input type="text" 
                                   id="ipDesbloquear" 
                                   class="form-control" 
                                   placeholder="Ej: 192.168.1.100 o ::1"
                                   style="margin-bottom: 10px;">
                            <button class="btn-modern btn-primary" onclick="desbloquearIP()" style="width: 100%;">
                                <i class="fas fa-unlock"></i>
                                Desbloquear IP
                            </button>
                        </div>
                        
                        <!-- Desbloquear Usuario -->
                        <div style="background: #f9fafb; padding: 20px; border-radius: 10px; border: 2px solid #e5e7eb;">
                            <h5 style="margin: 0 0 15px 0; color: #1f2937; font-weight: 700;">
                                <i class="fas fa-user"></i> Desbloquear Usuario
                            </h5>
                            <input type="text" 
                                   id="usuarioDesbloquear" 
                                   class="form-control" 
                                   placeholder="Ej: admin, docente-123"
                                   style="margin-bottom: 10px;">
                            <button class="btn-modern btn-primary" onclick="desbloquearUsuario()" style="width: 100%;">
                                <i class="fas fa-unlock"></i>
                                Desbloquear Usuario
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Acciones -->
                <div class="card-white">
                    <div class="card-header-custom">
                        <h4>
                            <i class="fas fa-tools"></i>
                            Acciones de Mantenimiento
                        </h4>
                    </div>
                    
                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <button class="btn-modern btn-primary" onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i>
                            Actualizar Datos
                        </button>
                        
                        <button class="btn-modern btn-danger" onclick="ejecutarMantenimiento()">
                            <i class="fas fa-broom"></i>
                            Limpiar Registros Antiguos
                        </button>
                    </div>
                    
                    <div style="margin-top: 20px; padding: 15px; background: #eff6ff; border-radius: 8px; border-left: 4px solid #3b82f6;">
                        <p style="margin: 0; color: #1e40af; font-size: 14px;">
                            <i class="fas fa-info-circle"></i>
                            <strong>Nota:</strong> La limpieza elimina registros de intentos fallidos mayores a 30 días. 
                            Esto ayuda a mantener la base de datos optimizada.
                        </p>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <?php include("../compartido/footer.php"); ?>
</div>

<!-- Scripts -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>

<script>
function ejecutarMantenimiento() {
    if (!confirm('¿Deseas ejecutar el mantenimiento?\n\nEsto eliminará registros de intentos fallidos mayores a 30 días.')) {
        return;
    }
    
    $.toast({
        heading: 'Procesando...',
        text: 'Ejecutando mantenimiento, por favor espera',
        position: 'top-right',
        loaderBg: '#667eea',
        icon: 'info',
        hideAfter: false
    });
    
    $.ajax({
        url: 'ajax-rate-limit-mantenimiento.php',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            $('.jq-toast-wrap').remove();
            
            if (response.success) {
                $.toast({
                    heading: 'Éxito',
                    text: response.message,
                    position: 'top-right',
                    loaderBg: '#10b981',
                    icon: 'success',
                    hideAfter: 3500
                });
                
                setTimeout(function() {
                    location.reload();
                }, 2000);
            } else {
                $.toast({
                    heading: 'Error',
                    text: response.message,
                    position: 'top-right',
                    loaderBg: '#ef4444',
                    icon: 'error',
                    hideAfter: 4000
                });
            }
        },
        error: function(xhr, status, error) {
            $('.jq-toast-wrap').remove();
            
            $.toast({
                heading: 'Error',
                text: 'Error de conexión: ' + error,
                position: 'top-right',
                loaderBg: '#ef4444',
                icon: 'error',
                hideAfter: 4000
            });
        }
    });
}

function desbloquearIP() {
    const ip = $('#ipDesbloquear').val().trim();
    
    if (!ip) {
        $.toast({
            heading: 'Advertencia',
            text: 'Por favor ingresa una dirección IP',
            position: 'top-right',
            loaderBg: '#f59e0b',
            icon: 'warning',
            hideAfter: 3000
        });
        return;
    }
    
    if (!confirm('¿Deseas desbloquear la IP: ' + ip + '?\n\nEsto eliminará todos los intentos fallidos registrados para esta IP.')) {
        return;
    }
    
    $.toast({
        heading: 'Procesando...',
        text: 'Desbloqueando IP...',
        position: 'top-right',
        loaderBg: '#667eea',
        icon: 'info',
        hideAfter: false
    });
    
    $.ajax({
        url: 'ajax-rate-limit-desbloquear.php',
        type: 'POST',
        data: {
            tipo: 'ip',
            valor: ip
        },
        dataType: 'json',
        success: function(response) {
            $('.jq-toast-wrap').remove();
            
            if (response.success) {
                $.toast({
                    heading: 'Éxito',
                    text: response.message,
                    position: 'top-right',
                    loaderBg: '#10b981',
                    icon: 'success',
                    hideAfter: 3500
                });
                
                $('#ipDesbloquear').val('');
                
                setTimeout(function() {
                    location.reload();
                }, 2000);
            } else {
                $.toast({
                    heading: 'Error',
                    text: response.message,
                    position: 'top-right',
                    loaderBg: '#ef4444',
                    icon: 'error',
                    hideAfter: 4000
                });
            }
        },
        error: function(xhr, status, error) {
            $('.jq-toast-wrap').remove();
            
            $.toast({
                heading: 'Error',
                text: 'Error de conexión: ' + error,
                position: 'top-right',
                loaderBg: '#ef4444',
                icon: 'error',
                hideAfter: 4000
            });
        }
    });
}

function desbloquearUsuario() {
    const usuario = $('#usuarioDesbloquear').val().trim();
    
    if (!usuario) {
        $.toast({
            heading: 'Advertencia',
            text: 'Por favor ingresa un nombre de usuario',
            position: 'top-right',
            loaderBg: '#f59e0b',
            icon: 'warning',
            hideAfter: 3000
        });
        return;
    }
    
    if (!confirm('¿Deseas desbloquear el usuario: ' + usuario + '?\n\nEsto eliminará todos los intentos fallidos y reseteará el contador de intentos.')) {
        return;
    }
    
    $.toast({
        heading: 'Procesando...',
        text: 'Desbloqueando usuario...',
        position: 'top-right',
        loaderBg: '#667eea',
        icon: 'info',
        hideAfter: false
    });
    
    $.ajax({
        url: 'ajax-rate-limit-desbloquear.php',
        type: 'POST',
        data: {
            tipo: 'usuario',
            valor: usuario
        },
        dataType: 'json',
        success: function(response) {
            $('.jq-toast-wrap').remove();
            
            if (response.success) {
                $.toast({
                    heading: 'Éxito',
                    text: response.message,
                    position: 'top-right',
                    loaderBg: '#10b981',
                    icon: 'success',
                    hideAfter: 3500
                });
                
                $('#usuarioDesbloquear').val('');
                
                setTimeout(function() {
                    location.reload();
                }, 2000);
            } else {
                $.toast({
                    heading: 'Error',
                    text: response.message,
                    position: 'top-right',
                    loaderBg: '#ef4444',
                    icon: 'error',
                    hideAfter: 4000
                });
            }
        },
        error: function(xhr, status, error) {
            $('.jq-toast-wrap').remove();
            
            $.toast({
                heading: 'Error',
                text: 'Error de conexión: ' + error,
                position: 'top-right',
                loaderBg: '#ef4444',
                icon: 'error',
                hideAfter: 4000
            });
        }
    });
}

// Desbloqueo rápido desde la tabla
function desbloquearIPRapido(ip) {
    $('#ipDesbloquear').val(ip);
    desbloquearIP();
}
</script>

</body>
</html>

