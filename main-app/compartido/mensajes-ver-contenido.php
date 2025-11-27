<?php
$filtro = "AND ema_para='".$_SESSION["id"]."'";
$esEnviados = false;
if(isset($_GET["opt"]) AND base64_decode($_GET["opt"])==2){
	$filtro = '';
	$esEnviados = true;
}

$idR = "";
if(!empty($_GET["idR"])){ $idR = base64_decode($_GET["idR"]);}

// Consulta con PDO
$sql = "SELECT *, uss.uss_nombre, uss.uss_email, uss.uss_foto 
        FROM ".$baseDatosServicios.".social_emails
        INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=ema_de AND uss.institucion=? AND uss.year=?
        WHERE ema_id=? ".$filtro;
$stmt = $conexionPDO->prepare($sql);
$stmt->execute([$config['conf_id_institucion'], $_SESSION["bd"], $idR]);
$datosConsulta = $stmt->fetch(PDO::FETCH_ASSOC);

// Marcar como visto si es recibido y no se ha visto
if(!empty($datosConsulta) && $datosConsulta['ema_para']==$_SESSION["id"] && $datosConsulta['ema_visto']=='0'){
	$sqlUpdate = "UPDATE ".$baseDatosServicios.".social_emails SET ema_visto=1, ema_fecha_visto=now() WHERE ema_id=?";
	$stmtUpdate = $conexionPDO->prepare($sqlUpdate);
	$stmtUpdate->execute([$idR]);
}

$eliminarTipo = $datosConsulta['ema_para'] == $_SESSION["id"] ? 2 : 1;
?>

<style>
/* Vista de mensaje individual tipo Gmail */
.gmail-message-view {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.gmail-message-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e9ecef;
}

.gmail-message-header-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.gmail-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: transparent;
    border: 1px solid #dadce0;
    border-radius: 8px;
    color: #5f6368;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.gmail-back-btn:hover {
    background: #f1f3f4;
    color: #202124;
    text-decoration: none;
}

.gmail-message-actions-top {
    display: flex;
    gap: 8px;
}

.gmail-action-btn-top {
    padding: 8px 16px;
    background: transparent;
    border: 1px solid #dadce0;
    border-radius: 8px;
    color: #5f6368;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
}

.gmail-action-btn-top:hover {
    background: #f1f3f4;
    color: #202124;
}

.gmail-action-btn-top.danger:hover {
    background: #fce8e6;
    color: #d93025;
    border-color: #d93025;
}

.gmail-subject-line {
    font-size: 22px;
    font-weight: 400;
    color: #202124;
    margin: 0 0 20px 0;
    line-height: 1.4;
}

.gmail-sender-info {
    display: flex;
    align-items: flex-start;
    gap: 16px;
}

.gmail-sender-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}

.gmail-sender-details {
    flex: 1;
}

.gmail-sender-name {
    font-size: 15px;
    font-weight: 500;
    color: #202124;
    margin: 0 0 4px 0;
}

.gmail-sender-email {
    font-size: 13px;
    color: #5f6368;
    margin: 0 0 8px 0;
}

.gmail-message-meta {
    display: flex;
    align-items: center;
    gap: 16px;
    font-size: 13px;
    color: #5f6368;
}

.gmail-message-date {
    display: flex;
    align-items: center;
    gap: 4px;
}

.gmail-message-content {
    padding: 32px 24px;
    font-size: 15px;
    line-height: 1.7;
    color: #202124;
}

.gmail-message-content p {
    margin: 0 0 16px 0;
}

.gmail-message-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
}

.gmail-message-footer {
    padding: 20px 24px;
    border-top: 1px solid #e9ecef;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.gmail-reply-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.gmail-reply-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    color: white;
    text-decoration: none;
}

.gmail-secondary-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: white;
    color: #5f6368;
    border: 1px solid #dadce0;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.gmail-secondary-btn:hover {
    background: #f1f3f4;
    color: #202124;
    text-decoration: none;
}

/* Dark mode */
body.dark-mode .gmail-message-view {
    background: #16213e;
}

body.dark-mode .gmail-message-header,
body.dark-mode .gmail-message-footer {
    border-color: #533483;
}

body.dark-mode .gmail-subject-line,
body.dark-mode .gmail-sender-name,
body.dark-mode .gmail-message-content {
    color: #eaeaea;
}

body.dark-mode .gmail-sender-email,
body.dark-mode .gmail-message-meta,
body.dark-mode .gmail-message-date {
    color: #b0b0b0;
}

body.dark-mode .gmail-back-btn,
body.dark-mode .gmail-action-btn-top,
body.dark-mode .gmail-secondary-btn {
    background: #0f3460;
    border-color: #533483;
    color: #eaeaea;
}

body.dark-mode .gmail-back-btn:hover,
body.dark-mode .gmail-action-btn-top:hover,
body.dark-mode .gmail-secondary-btn:hover {
    background: #533483;
}
</style>

<div class="page-bar">
	<div class="page-title-breadcrumb">
		<div class="pull-left">
			<div class="page-title"><?= htmlspecialchars($datosConsulta['ema_asunto'] ?? 'Mensaje'); ?></div>
		</div>
		<ol class="breadcrumb page-breadcrumb pull-right">
			<li><a class="parent-item" href="mensajes.php">Mensajes</a>&nbsp;<i class="fa fa-angle-right"></i></li>
			<li class="active"><?= htmlspecialchars($datosConsulta['ema_asunto'] ?? 'Ver mensaje'); ?></li>
		</ol>
	</div>
</div>

<div class="gmail-message-view">
    <!-- Header del mensaje -->
    <div class="gmail-message-header">
        <div class="gmail-message-header-top">
            <a href="mensajes.php<?= $esEnviados ? '?opt='.base64_encode(2) : ''; ?>" class="gmail-back-btn">
                <i class="fa fa-arrow-left"></i>
                <span>Volver</span>
            </a>
            
            <div class="gmail-message-actions-top">
                <button class="gmail-action-btn-top" onclick="imprimirMensaje()" title="Imprimir">
                    <i class="fa fa-print"></i>
                </button>
                <button class="gmail-action-btn-top danger" onclick="eliminarEsteMensaje()" title="Eliminar">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
        
        <h1 class="gmail-subject-line"><?= htmlspecialchars($datosConsulta['ema_asunto']); ?></h1>
        
        <div class="gmail-sender-info">
            <img src="../files/fotos/<?= $datosConsulta['uss_foto']; ?>" class="gmail-sender-avatar" alt="<?= htmlspecialchars($datosConsulta['uss_nombre']); ?>">
            
            <div class="gmail-sender-details">
                <h3 class="gmail-sender-name"><?= htmlspecialchars($datosConsulta['uss_nombre']); ?></h3>
                <p class="gmail-sender-email">De: <?= htmlspecialchars($datosConsulta['uss_email']); ?></p>
                
                <div class="gmail-message-meta">
                    <div class="gmail-message-date">
                        <i class="fa fa-clock"></i>
                        <span><?= date('d \d\e F \d\e Y, H:i', strtotime($datosConsulta['ema_fecha'])); ?></span>
                    </div>
                    <?php if($datosConsulta['ema_visto'] == 1 && !empty($datosConsulta['ema_fecha_visto'])): ?>
                        <div class="gmail-message-date">
                            <i class="fa fa-check-double" style="color: #1a73e8;"></i>
                            <span>Visto el <?= date('d/m/Y H:i', strtotime($datosConsulta['ema_fecha_visto'])); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contenido del mensaje -->
    <div class="gmail-message-content">
        <?= $datosConsulta['ema_contenido']; ?>
    </div>
    
    <!-- Footer con botones de acción -->
    <div class="gmail-message-footer">
        <a href="mensajes-redactar.php?para=<?= base64_encode($datosConsulta['ema_de']); ?>&asunto=<?= base64_encode('RE: '.$datosConsulta['ema_asunto']); ?>" class="gmail-reply-btn">
            <i class="fa fa-reply"></i>
            <span>Responder</span>
        </a>
        
        <button class="gmail-secondary-btn" onclick="reenviarMensaje()">
            <i class="fa fa-share"></i>
            <span>Reenviar</span>
        </button>
        
        <button class="gmail-secondary-btn" onclick="imprimirMensaje()">
            <i class="fa fa-print"></i>
            <span>Imprimir</span>
        </button>
        
        <button class="gmail-secondary-btn danger" onclick="eliminarEsteMensaje()" style="margin-left: auto;">
            <i class="fa fa-trash"></i>
            <span>Eliminar</span>
        </button>
    </div>
</div>

<script>
function eliminarEsteMensaje() {
    if (confirm('¿Estás seguro de que deseas eliminar este mensaje?')) {
        window.location.href = '../compartido/mensajes-eliminar.php?idR=<?= base64_encode($idR); ?>&elm=<?= base64_encode($eliminarTipo); ?>';
    }
}

function imprimirMensaje() {
    window.print();
}

function reenviarMensaje() {
    // TODO: Implementar reenvío
    alert('Función de reenvío en desarrollo');
}
</script>
