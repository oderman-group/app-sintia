<?php
$nombreEmisor = UsuariosPadre::nombreCompletoDelUsuario($datosUsuarioActual);
$destinatarioPredeterminado = '';
$asuntoPredeterminado = '';

if(!empty($_GET['para'])){
    $destinatarioPredeterminado = base64_decode($_GET['para']);
}

if(!empty($_GET['asunto'])){
    $asuntoPredeterminado = base64_decode($_GET['asunto']);
}
?>

<style>
/* Formulario de redactar tipo Gmail */
.gmail-compose-container {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.gmail-compose-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px 24px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.gmail-compose-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.gmail-compose-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    color: white;
    cursor: pointer;
    transition: all 0.2s ease;
}

.gmail-compose-close:hover {
    background: rgba(255, 255, 255, 0.3);
}

.gmail-compose-body {
    padding: 24px;
}

.gmail-form-group {
    margin-bottom: 20px;
    position: relative;
}

.gmail-form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #5f6368;
    margin-bottom: 8px;
}

.gmail-form-input,
.gmail-form-select {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #dadce0;
    border-radius: 8px;
    font-size: 14px;
    color: #202124;
    transition: all 0.2s ease;
    font-family: inherit;
}

.gmail-form-input:focus,
.gmail-form-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.gmail-form-textarea {
    width: 100%;
    min-height: 300px;
    padding: 16px;
    border: 1px solid #dadce0;
    border-radius: 8px;
    font-size: 14px;
    color: #202124;
    font-family: inherit;
    line-height: 1.6;
    resize: vertical;
}

.gmail-form-textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.gmail-compose-footer {
    padding: 0 24px 24px 24px;
    display: flex;
    gap: 12px;
    align-items: center;
}

.gmail-send-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 32px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.gmail-send-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.gmail-send-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.gmail-cancel-btn {
    padding: 12px 24px;
    background: transparent;
    color: #5f6368;
    border: 1px solid #dadce0;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.gmail-cancel-btn:hover {
    background: #f1f3f4;
}

.gmail-sending-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.gmail-sending-box {
    background: white;
    padding: 40px;
    border-radius: 16px;
    text-align: center;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
}

.gmail-sending-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.select2-container--bootstrap .select2-selection {
    border: 1px solid #dadce0 !important;
    border-radius: 8px !important;
    padding: 8px !important;
}

.select2-container--bootstrap.select2-container--focus .select2-selection {
    border-color: #667eea !important;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
}

/* Dark mode */
body.dark-mode .gmail-compose-container {
    background: #16213e;
}

body.dark-mode .gmail-compose-body {
    background: #16213e;
}

body.dark-mode .gmail-form-label {
    color: #eaeaea;
}

body.dark-mode .gmail-form-input,
body.dark-mode .gmail-form-select,
body.dark-mode .gmail-form-textarea {
    background: #0f3460;
    border-color: #533483;
    color: #eaeaea;
}

body.dark-mode .gmail-form-input:focus,
body.dark-mode .gmail-form-select:focus,
body.dark-mode .gmail-form-textarea:focus {
    border-color: #667eea;
}

body.dark-mode .gmail-cancel-btn {
    background: #0f3460;
    border-color: #533483;
    color: #eaeaea;
}

body.dark-mode .gmail-cancel-btn:hover {
    background: #533483;
}

body.dark-mode .gmail-sending-box {
    background: #16213e;
    color: #eaeaea;
}
</style>

<div class="page-bar">
	<div class="page-title-breadcrumb">
		<div class="pull-left">
			<div class="page-title">Redactar Mensaje</div>
		</div>
		<ol class="breadcrumb page-breadcrumb pull-right">
			<li><a class="parent-item" href="mensajes.php">Mensajes</a>&nbsp;<i class="fa fa-angle-right"></i></li>
			<li class="active">Redactar</li>
		</ol>
	</div>
</div>

<div class="gmail-compose-container">
    <!-- Header -->
    <div class="gmail-compose-header">
        <h2 class="gmail-compose-title">
            <i class="fa fa-pencil-alt"></i>
            Nuevo Mensaje
        </h2>
        <a href="mensajes.php" class="gmail-compose-close" title="Cerrar">
            <i class="fa fa-times"></i>
        </a>
    </div>
    
    <!-- Formulario -->
    <form id="formEnviarMensaje" method="post">
        <div class="gmail-compose-body">
            <!-- Para -->
            <div class="gmail-form-group">
                <label class="gmail-form-label">Para: <span style="color: #d93025;">*</span></label>
                <select id="select_usuario" class="gmail-form-select select2-multiple" multiple name="para[]" required>
					<?php
					if(!empty($destinatarioPredeterminado)){
						$filtro = " AND uss_id='".$destinatarioPredeterminado."'";
						$lista = UsuariosPadre::obtenerTodosLosDatosDeUsuarios($filtro);
						while($dato = mysqli_fetch_array($lista, MYSQLI_BOTH)){
							$nombre = UsuariosPadre::nombreCompletoDelUsuario($dato)." - ".$dato["pes_nombre"];
					?>
						<option value="<?= $dato["uss_id"]; ?>" selected><?= $nombre; ?></option>
					<?php
						}
					}
					?>
				</select>
            </div>
            
            <!-- Asunto -->
            <div class="gmail-form-group">
                <label class="gmail-form-label">Asunto: <span style="color: #d93025;">*</span></label>
                <input type="text" class="gmail-form-input" id="asunto" name="asunto" value="<?= htmlspecialchars($asuntoPredeterminado); ?>" required placeholder="Escribe el asunto del mensaje">
            </div>
            
            <!-- Contenido -->
            <div class="gmail-form-group">
                <label class="gmail-form-label">Mensaje: <span style="color: #d93025;">*</span></label>
                <textarea id="editor1" name="contenido" class="gmail-form-textarea" required>

--- --- ---
<p>Cordialmente,</p>
<small><b><?= htmlspecialchars($nombreEmisor); ?></b></small>
</textarea>
            </div>
        </div>
        
        <!-- Footer con botones -->
        <div class="gmail-compose-footer">
            <button type="submit" class="gmail-send-btn" id="btnEnviar">
                <i class="fa fa-paper-plane"></i>
                <span>Enviar Mensaje</span>
            </button>
            
            <a href="mensajes.php" class="gmail-cancel-btn">
                <i class="fa fa-times"></i>
                Cancelar
            </a>
            
            <div id="mensajeEstado" style="margin-left: auto; font-size: 14px;"></div>
        </div>
    </form>
</div>

<!-- Overlay de envío -->
<div class="gmail-sending-overlay" id="sendingOverlay">
    <div class="gmail-sending-box">
        <div class="gmail-sending-spinner"></div>
        <p style="font-size: 16px; color: #5f6368; margin: 0;">Enviando mensaje...</p>
    </div>
</div>

<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<script src="../ckeditor/ckeditor.js"></script>

<script>
// Función personalizada para base64 encode
function base64Encode(str) {
    return window.btoa(unescape(encodeURIComponent(str)));
}

// Inicializar CKEditor
CKEDITOR.replace('editor1', {
    height: 300,
    toolbar: [
        { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
        { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Blockquote'] },
        { name: 'links', items: ['Link', 'Unlink'] },
        { name: 'insert', items: ['Image', 'Table'] },
        { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
        { name: 'colors', items: ['TextColor', 'BGColor'] },
        { name: 'tools', items: ['Maximize'] }
    ]
});

// Inicializar Select2
$(document).ready(function() {
	$('#select_usuario').select2({
		placeholder: 'Seleccione destinatario(s)...',
		theme: "bootstrap",
		multiple: true,
		allowClear: true,
		ajax: {
			type: 'GET',
			url: '../compartido/ajax-listar-usuarios.php',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					term: params.term || '',
					todos: '1' // Solicitar todos los usuarios para enviar mensajes
				};
			},
			processResults: function(data) {
				// El endpoint ya devuelve JSON parseado
				if (!data || !Array.isArray(data)) {
					return { results: [] };
				}
				return {
					results: $.map(data, function(item) {
						return {
							id: item.value,
							text: item.label
						}
					})
				};
			},
			cache: true
		},
		minimumInputLength: 0
	});
});

// Envío asíncrono del formulario
document.getElementById('formEnviarMensaje').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validar formulario
    const para = $('#select_usuario').val();
    const asunto = document.getElementById('asunto').value.trim();
    const contenido = CKEDITOR.instances.editor1.getData().trim();
    
    if (!para || para.length === 0) {
        alert('Por favor selecciona al menos un destinatario.');
        return;
    }
    
    if (!asunto) {
        alert('Por favor escribe un asunto.');
        return;
    }
    
    if (!contenido || contenido === '<p><br></p>' || contenido === '') {
        alert('Por favor escribe un mensaje.');
        return;
    }
    
    // Deshabilitar botón y mostrar loading
    const btnEnviar = document.getElementById('btnEnviar');
    const mensajeEstado = document.getElementById('mensajeEstado');
    const sendingOverlay = document.getElementById('sendingOverlay');
    
    btnEnviar.disabled = true;
    btnEnviar.innerHTML = '<i class="fa fa-spinner fa-spin"></i> <span>Enviando...</span>';
    sendingOverlay.style.display = 'flex';
    
    // Preparar datos
    const formData = new FormData();
    formData.append('para', JSON.stringify(para));
    formData.append('asunto', asunto);
    formData.append('contenido', contenido);
    
    // Enviar por AJAX
    fetch('../compartido/ajax-enviar-mensaje.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.text();
    })
    .then(text => {
        console.log('Response text:', text);
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('Error parsing JSON:', e);
            console.error('Response text:', text);
            throw new Error('Respuesta del servidor no es JSON válido');
        }
    })
    .then(data => {
        console.log('Data received:', data);
        sendingOverlay.style.display = 'none';
        
        if (data.success) {
            // Mostrar mensaje de éxito
            mensajeEstado.innerHTML = '<span style="color: #27ae60;"><i class="fa fa-check-circle"></i> Mensaje enviado correctamente</span>';
            
            // Mostrar notificación
            if (typeof $.toast !== 'undefined') {
                $.toast({
                    heading: '¡Mensaje Enviado!',  
                    text: 'Tu mensaje ha sido enviado correctamente a ' + para.length + ' destinatario(s).',
                    position: 'top-right',
                    showHideTransition: 'slide',
                    loaderBg: '#27ae60',
                    icon: 'success',
                    hideAfter: 3000
                });
            }
            
            // Redirigir después de 2 segundos
            setTimeout(function() {
                window.location.href = 'mensajes.php?opt=' + base64Encode('2'); // Ir a enviados
            }, 2000);
        } else {
            // Mostrar error
            mensajeEstado.innerHTML = '<span style="color: #d93025;"><i class="fa fa-exclamation-circle"></i> ' + (data.message || 'Error al enviar') + '</span>';
            btnEnviar.disabled = false;
            btnEnviar.innerHTML = '<i class="fa fa-paper-plane"></i> <span>Enviar Mensaje</span>';
            
            if (typeof $.toast !== 'undefined') {
                $.toast({
                    heading: 'Error',  
                    text: data.message || 'No se pudo enviar el mensaje',
                    position: 'top-right',
                    showHideTransition: 'slide',
                    loaderBg: '#d93025',
                    icon: 'error',
                    hideAfter: 5000
                });
            }
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        sendingOverlay.style.display = 'none';
        mensajeEstado.innerHTML = '<span style="color: #d93025;"><i class="fa fa-exclamation-circle"></i> ' + error.message + '</span>';
        btnEnviar.disabled = false;
        btnEnviar.innerHTML = '<i class="fa fa-paper-plane"></i> <span>Enviar Mensaje</span>';
        
        if (typeof $.toast !== 'undefined') {
            $.toast({
                heading: 'Error',  
                text: error.message || 'No se pudo enviar el mensaje',
                position: 'top-right',
                showHideTransition: 'slide',
                loaderBg: '#d93025',
                icon: 'error',
                hideAfter: 5000
            });
        }
    });
});
</script>
