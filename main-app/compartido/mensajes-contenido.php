<?php
// Determinar qué bandeja mostrar
$esEnviados = isset($_GET["opt"]) && base64_decode($_GET["opt"]) == 2;
$esBorrador = isset($_GET["opt"]) && base64_decode($_GET["opt"]) == 3;

// Consultas con PDO
if ($esEnviados) {
    $sql = "SELECT * FROM ".$baseDatosServicios.".social_emails
            INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=ema_para AND uss.institucion=? AND uss.year=?
            WHERE ema_de=? AND ema_eliminado_de='0' AND ema_institucion=? AND ema_year=?
            ORDER BY ema_id DESC";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->execute([$config['conf_id_institucion'], $_SESSION["bd"], $_SESSION["id"], $_SESSION["idInstitucion"], $_SESSION["bd"]]);
} else {
    $sql = "SELECT * FROM ".$baseDatosServicios.".social_emails
            INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=ema_de AND uss.institucion=? AND uss.year=?
            WHERE ema_para=? AND ema_eliminado_para='0' AND ema_institucion=? AND ema_year=?
            ORDER BY ema_id DESC";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->execute([$config['conf_id_institucion'], $_SESSION["bd"], $_SESSION["id"], $_SESSION["idInstitucion"], $_SESSION["bd"]]);
}

// Contar mensajes no leídos
$sqlNoLeidos = "SELECT COUNT(*) as total FROM ".$baseDatosServicios.".social_emails 
                WHERE ema_para=? AND ema_eliminado_para!=1 AND ema_visto=0 AND ema_institucion=? AND ema_year=?";
$stmtNoLeidos = $conexionPDO->prepare($sqlNoLeidos);
$stmtNoLeidos->execute([$_SESSION["id"], $_SESSION["idInstitucion"], $_SESSION["bd"]]);
$numR = $stmtNoLeidos->fetchColumn();

$sqlEnviados = "SELECT COUNT(*) as total FROM ".$baseDatosServicios.".social_emails 
                WHERE ema_de=? AND ema_eliminado_de!=1 AND ema_institucion=? AND ema_year=?";
$stmtEnviados = $conexionPDO->prepare($sqlEnviados);
$stmtEnviados->execute([$_SESSION["id"], $_SESSION["idInstitucion"], $_SESSION["bd"]]);
$numRenviados = $stmtEnviados->fetchColumn();
?>

<!-- CSS Moderno tipo Gmail -->
<style>
.gmail-container {
    display: flex;
    gap: 0;
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    min-height: 600px;
}

/* Sidebar izquierdo */
.gmail-sidebar {
    width: 240px;
    background: #f8f9fa;
    border-right: 1px solid #e9ecef;
    padding: 20px 12px;
    flex-shrink: 0;
}

.compose-btn-gmail {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 24px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    text-decoration: none;
    width: 100%;
    justify-content: center;
    margin-bottom: 20px;
}

.compose-btn-gmail:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    color: white;
    text-decoration: none;
}

.compose-btn-gmail i {
    font-size: 18px;
}

.gmail-nav {
    list-style: none;
    padding: 0;
    margin: 0;
}

.gmail-nav-item {
    margin-bottom: 4px;
}

.gmail-nav-link {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 10px 16px;
    color: #5f6368;
    text-decoration: none;
    border-radius: 0 24px 24px 0;
    transition: all 0.2s ease;
    font-size: 14px;
    font-weight: 500;
    position: relative;
}

.gmail-nav-link:hover {
    background: #f1f3f4;
    color: #202124;
    text-decoration: none;
}

.gmail-nav-link.active {
    background: #fce8e6;
    color: #d93025;
    font-weight: 700;
}

.gmail-nav-link.active i {
    color: #d93025;
}

.gmail-nav-icon {
    width: 20px;
    text-align: center;
    font-size: 18px;
}

.gmail-badge {
    margin-left: auto;
    background: #d93025;
    color: white;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 700;
    min-width: 24px;
    text-align: center;
}

/* Área principal de mensajes */
.gmail-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: white;
    overflow: hidden;
}

.gmail-toolbar {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-bottom: 1px solid #e9ecef;
    flex-wrap: wrap;
}

.gmail-checkbox-all {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.gmail-toolbar-btn {
    background: transparent;
    border: none;
    padding: 8px 12px;
    color: #5f6368;
    cursor: pointer;
    border-radius: 4px;
    transition: all 0.2s ease;
    font-size: 14px;
}

.gmail-toolbar-btn:hover {
    background: #f1f3f4;
    color: #202124;
}

.gmail-toolbar-btn i {
    font-size: 16px;
}

.gmail-refresh-btn {
    margin-left: auto;
}

/* Lista de mensajes */
.gmail-messages {
    flex: 1;
    overflow-y: auto;
}

.gmail-message-row {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 12px 20px;
    border-bottom: 1px solid #f1f3f4;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    color: inherit;
}

.gmail-message-row:hover {
    box-shadow: inset 1px 0 0 #dadce0, inset -1px 0 0 #dadce0, 0 1px 2px 0 rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15);
    z-index: 2;
}

.gmail-message-row.unread {
    background: #f5f5f5;
    font-weight: 600;
}

.gmail-message-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
    flex-shrink: 0;
}

.gmail-message-star {
    font-size: 18px;
    color: #dadce0;
    cursor: pointer;
    transition: color 0.2s ease;
    flex-shrink: 0;
}

.gmail-message-star:hover,
.gmail-message-star.starred {
    color: #f9ab00;
}

.gmail-message-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}

.gmail-message-sender {
    min-width: 180px;
    font-size: 14px;
    flex-shrink: 0;
}

.gmail-message-row.unread .gmail-message-sender {
    font-weight: 700;
    color: #202124;
}

.gmail-message-subject {
    flex: 1;
    font-size: 14px;
    color: #202124;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.gmail-message-preview {
    color: #5f6368;
    font-weight: 400;
    margin-left: 8px;
}

.gmail-message-date {
    font-size: 13px;
    color: #5f6368;
    min-width: 100px;
    text-align: right;
    flex-shrink: 0;
}

.gmail-message-actions {
    display: flex;
    gap: 4px;
    opacity: 0;
    transition: opacity 0.2s ease;
    flex-shrink: 0;
}

.gmail-message-row:hover .gmail-message-actions {
    opacity: 1;
}

.gmail-action-btn {
    background: transparent;
    border: none;
    padding: 6px 10px;
    color: #5f6368;
    cursor: pointer;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.gmail-action-btn:hover {
    background: #f1f3f4;
    color: #202124;
}

.gmail-seen-badge {
    font-size: 11px;
    color: #1a73e8;
    display: flex;
    align-items: center;
    gap: 4px;
}

.gmail-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    text-align: center;
}

.gmail-empty-state i {
    font-size: 72px;
    color: #dadce0;
    margin-bottom: 20px;
}

.gmail-empty-state h4 {
    color: #5f6368;
    font-size: 16px;
    font-weight: 500;
    margin: 0;
}

/* Responsive */
@media (max-width: 992px) {
    .gmail-sidebar {
        width: 200px;
    }
    
    .gmail-message-sender {
        min-width: 120px;
    }
}

@media (max-width: 768px) {
    .gmail-container {
        flex-direction: column;
    }
    
    .gmail-sidebar {
        width: 100%;
        border-right: none;
        border-bottom: 1px solid #e9ecef;
        padding: 15px;
    }
    
    .gmail-nav-link {
        border-radius: 8px;
    }
    
    .gmail-message-sender {
        display: none;
    }
    
    .gmail-message-date {
        min-width: 60px;
    }
}

/* Dark mode support */
body.dark-mode .gmail-container {
    background: #16213e;
}

body.dark-mode .gmail-sidebar {
    background: #0f3460;
    border-right-color: #533483;
}

body.dark-mode .gmail-main {
    background: #16213e;
}

body.dark-mode .gmail-toolbar {
    border-bottom-color: #533483;
}

body.dark-mode .gmail-nav-link {
    color: #d0d0d0;
}

body.dark-mode .gmail-nav-link:hover {
    background: #533483;
    color: #eaeaea;
}

body.dark-mode .gmail-nav-link.active {
    background: #764ba2;
    color: #eaeaea;
}

body.dark-mode .gmail-message-row {
    border-bottom-color: #533483;
}

body.dark-mode .gmail-message-row:hover {
    background: #0f3460;
}

body.dark-mode .gmail-message-row.unread {
    background: #0f3460;
}

body.dark-mode .gmail-message-sender,
body.dark-mode .gmail-message-subject {
    color: #eaeaea;
}

body.dark-mode .gmail-message-preview,
body.dark-mode .gmail-message-date {
    color: #b0b0b0;
}

body.dark-mode .gmail-toolbar-btn {
    color: #d0d0d0;
}

body.dark-mode .gmail-toolbar-btn:hover {
    background: #533483;
    color: #eaeaea;
}
</style>

<div class="gmail-container">
    <!-- Sidebar -->
    <div class="gmail-sidebar">
        <a href="mensajes-redactar.php" class="compose-btn-gmail">
            <i class="fa fa-pencil-alt"></i>
            <span>Redactar</span>
        </a>
        
        <ul class="gmail-nav">
            <li class="gmail-nav-item">
                <a href="mensajes.php" class="gmail-nav-link <?php if(!isset($_GET["opt"])) echo 'active'; ?>">
                    <i class="fa fa-inbox gmail-nav-icon"></i>
                    <span>Recibidos</span>
                    <?php if($numR > 0): ?>
                        <span class="gmail-badge"><?= $numR; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="gmail-nav-item">
                <a href="mensajes.php?opt=<?= base64_encode(2); ?>" class="gmail-nav-link <?php if($esEnviados) echo 'active'; ?>">
                    <i class="fa fa-paper-plane gmail-nav-icon"></i>
                    <span>Enviados</span>
                    <span class="gmail-badge" style="background: #5f6368;"><?= $numRenviados; ?></span>
                </a>
            </li>
        </ul>
    </div>
    
    <!-- Área principal -->
    <div class="gmail-main">
        <!-- Toolbar -->
        <div class="gmail-toolbar">
            <input type="checkbox" class="gmail-checkbox-all" id="selectAll" title="Seleccionar todos">
            
            <button class="gmail-toolbar-btn" title="Actualizar" onclick="location.reload();">
                <i class="fa fa-sync-alt"></i>
            </button>
            
            <div style="width: 1px; height: 24px; background: #dadce0; margin: 0 8px;"></div>
            
            <button class="gmail-toolbar-btn" title="Eliminar seleccionados" onclick="eliminarSeleccionados()">
                <i class="fa fa-trash"></i>
            </button>
            
            <button class="gmail-toolbar-btn" title="Marcar como leído" onclick="marcarComoLeido()">
                <i class="fa fa-envelope-open"></i>
            </button>
            
            <button class="gmail-toolbar-btn gmail-refresh-btn" title="Buscar" onclick="toggleBusqueda()">
                <i class="fa fa-search"></i>
            </button>
        </div>
        
        <!-- Lista de mensajes -->
        <div class="gmail-messages" id="messagesList">
            <?php
            $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($mensajes) == 0):
            ?>
                <div class="gmail-empty-state">
                    <i class="fa fa-inbox"></i>
                    <h4><?= $esEnviados ? 'No hay mensajes enviados' : 'Bandeja vacía'; ?></h4>
                    <p style="color: #5f6368; font-size: 14px;">No tienes mensajes en este momento.</p>
                </div>
            <?php
            else:
                foreach ($mensajes as $resultado):
                    $eliminar = $resultado['ema_para'] == $_SESSION["id"] ? 2 : 1;
                    $esNoLeido = $resultado['ema_visto'] == '0';
                    
                    // Extraer preview del contenido (primeros 100 caracteres sin HTML)
                    $mensajeContenido = $resultado['ema_contenido'] ?? '';
                    $preview = strip_tags($mensajeContenido);
                    $preview = mb_substr($preview, 0, 100);
                    if (mb_strlen($preview) == 100) $preview .= '...';
            ?>
                <div class="gmail-message-row <?= $esNoLeido ? 'unread' : ''; ?>" onclick="verMensaje('<?= $resultado['ema_id']; ?>')">
                    <input type="checkbox" class="gmail-message-checkbox" data-id="<?= $resultado['ema_id']; ?>" onclick="event.stopPropagation();">
                    
                    <i class="fa fa-star gmail-message-star" onclick="toggleStar(event, '<?= $resultado['ema_id']; ?>')"></i>
                    
                    <img src="../files/fotos/<?= $resultado['uss_foto']; ?>" class="gmail-message-avatar" alt="">
                    
                    <div class="gmail-message-sender">
                        <?= htmlspecialchars($resultado['uss_nombre']); ?>
                    </div>
                    
                    <div class="gmail-message-subject">
                        <strong><?= htmlspecialchars($resultado['ema_asunto']); ?></strong>
                        <span class="gmail-message-preview"> - <?= htmlspecialchars($preview); ?></span>
                    </div>
                    
                    <?php if($resultado['ema_de'] == $_SESSION["id"] && $resultado['ema_visto'] == 1): ?>
                        <div class="gmail-seen-badge" onclick="event.stopPropagation();" title="Visto el <?= $resultado['ema_fecha_visto']; ?>">
                            <i class="fa fa-check-double"></i>
                            <span>Visto</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="gmail-message-date">
                        <?= date('d M', strtotime($resultado['ema_fecha'])); ?>
                    </div>
                    
                    <div class="gmail-message-actions">
                        <button class="gmail-action-btn" title="Eliminar" onclick="eliminarMensaje(event, '<?= $resultado['ema_id']; ?>', '<?= $eliminar; ?>')">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            <?php
                endforeach;
            endif;
            ?>
        </div>
    </div>
</div>

<script>
// Función para ver mensaje
function verMensaje(idMensaje) {
    const opt = '<?= isset($_GET["opt"]) ? $_GET["opt"] : ""; ?>';
    let url = 'mensajes-ver.php?idR=' + base64Encode(idMensaje);
    if (opt) {
        url += '&opt=' + opt;
    }
    window.location.href = url;
}

// Función para eliminar mensaje individual
function eliminarMensaje(event, idMensaje, tipo) {
    event.stopPropagation();
    
    if (confirm('¿Estás seguro de que deseas eliminar este mensaje?')) {
        window.location.href = '../compartido/mensajes-eliminar.php?idR=' + base64Encode(idMensaje) + '&elm=' + base64Encode(tipo);
    }
}

// Función para seleccionar todos
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.gmail-message-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    }
});

// Función para eliminar seleccionados
function eliminarSeleccionados() {
    const checkboxes = document.querySelectorAll('.gmail-message-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Por favor selecciona al menos un mensaje para eliminar.');
        return;
    }
    
    if (confirm('¿Estás seguro de que deseas eliminar ' + checkboxes.length + ' mensaje(s)?')) {
        // TODO: Implementar eliminación múltiple
        alert('Función de eliminación múltiple en desarrollo');
    }
}

// Función para marcar como leído
function marcarComoLeido() {
    const checkboxes = document.querySelectorAll('.gmail-message-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Por favor selecciona al menos un mensaje.');
        return;
    }
    
    // TODO: Implementar marcar como leído
    alert('Función de marcar como leído en desarrollo');
}

// Función para toggle búsqueda
function toggleBusqueda() {
    // TODO: Implementar búsqueda
    alert('Función de búsqueda en desarrollo');
}

// Función para toggle star
function toggleStar(event, idMensaje) {
    event.stopPropagation();
    const star = event.target;
    star.classList.toggle('starred');
    
    // TODO: Guardar estado en BD
}

// Función personalizada para base64 encode
function base64Encode(str) {
    return window.btoa(unescape(encodeURIComponent(str)));
}
</script>
