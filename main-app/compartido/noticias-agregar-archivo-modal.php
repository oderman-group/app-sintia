<?php
if (empty($_SESSION["id"])) {
    include_once("session-compartida.php");
}
?>

<!-- Área de subida -->
<div class="upload-area" id="uploadAreaFile" style="border-color: #0061ff;">
    <div class="upload-icon">📎</div>
    <div class="upload-title">Selecciona tu archivo</div>
    <div class="upload-subtitle">Arrastra y suelta aquí, o haz clic para elegir</div>
    <button type="button" class="btn-choose" id="btnChooseFile" style="background: linear-gradient(87deg, #0061ff 0, #0047b3 100%);">
        Elegir Archivo
    </button>
    <div class="upload-info">PDF, DOC, XLS, PPT, ZIP • Máximo 10MB</div>
</div>

<input type="file" id="fileInput" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar" style="display: none;">

<!-- Preview del archivo -->
<div class="preview-container" id="previewContainerFile" style="display: none;">
    <div style="display: flex; align-items: center; gap: 20px; padding: 20px; background: #f5f5f5; border-radius: 12px;">
        <div id="fileIconPreview" style="font-size: 48px; flex-shrink: 0;">📄</div>
        <div style="flex: 1;">
            <div id="fileNamePreview" style="font-size: 16px; font-weight: 600; color: #333; margin-bottom: 8px; word-break: break-word;"></div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <div id="fileSizePreview" style="font-size: 13px; color: #666;"></div>
                <div id="fileTypePreview" style="background: #0061ff; color: white; padding: 3px 10px; border-radius: 15px; font-size: 11px; font-weight: 600; text-transform: uppercase;"></div>
            </div>
        </div>
        <button type="button" class="btn-remove-preview" id="btnRemoveFile">
            ✕
        </button>
    </div>
</div>

<!-- Campo de texto opcional -->
<div class="photo-text-container" id="fileTextContainer" style="display: none;">
    <label class="photo-text-label">Escribe algo sobre tu archivo (opcional)</label>
    <textarea id="fileDescription" 
              class="photo-text-area" 
              placeholder="¿Qué quieres compartir?"
              rows="4"
              maxlength="500"></textarea>
    <div class="photo-text-counter">
        <span id="fileCharCount">0</span>/500 caracteres
    </div>
</div>

<!-- Botones de acción -->
<div class="action-buttons">
    <button type="button" class="btn-action btn-cancel" onclick="cerrarModal_nuevoArchivo()">
        Cancelar
    </button>
    <button type="button" class="btn-action btn-publish" id="btnPublishFile" disabled style="background: linear-gradient(87deg, #0061ff 0, #0047b3 100%);">
        Publicar Archivo
    </button>
</div>

<!-- Loading overlay -->
<div class="loading-overlay" id="loadingOverlayFile">
    <div class="loading-content">
        <div class="loading-spinner" style="border-top-color: #0061ff;"></div>
        <div class="loading-text">Subiendo archivo...</div>
    </div>
</div>

<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" style="display:none" onload="
(function(){
    console.log('📎 Inicializando modal de archivo INLINE...');
    
    var selectedFile = null;
    var fileInput = document.getElementById('fileInput');
    var uploadAreaFile = document.getElementById('uploadAreaFile');
    var previewContainerFile = document.getElementById('previewContainerFile');
    var btnPublishFile = document.getElementById('btnPublishFile');
    var fileTextContainer = document.getElementById('fileTextContainer');
    var fileDescription = document.getElementById('fileDescription');
    var fileCharCount = document.getElementById('fileCharCount');
    var loadingOverlayFile = document.getElementById('loadingOverlayFile');
    var btnChooseFile = document.getElementById('btnChooseFile');
    var btnRemoveFile = document.getElementById('btnRemoveFile');
    var fileIconPreview = document.getElementById('fileIconPreview');
    var fileNamePreview = document.getElementById('fileNamePreview');
    var fileSizePreview = document.getElementById('fileSizePreview');
    var fileTypePreview = document.getElementById('fileTypePreview');
    
    if (!fileInput) {
        console.error('❌ Elementos no encontrados');
        return;
    }
    
    console.log('✅ Elementos encontrados');
    
    // Click en botón elegir
    btnChooseFile.onclick = function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('👆 Click en botón');
        fileInput.click();
    };
    
    // Click en área
    uploadAreaFile.onclick = function(e) {
        if (e.target.id !== 'btnChooseFile') {
            console.log('👆 Click en área');
            fileInput.click();
        }
    };
    
    // Cambio de archivo
    fileInput.onchange = function() {
        console.log('📁 Archivo seleccionado');
        if (this.files && this.files[0]) {
            handleFile(this.files[0]);
        }
    };
    
    // Drag & Drop
    uploadAreaFile.ondragover = function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.add('active');
    };
    
    uploadAreaFile.ondragleave = function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('active');
    };
    
    uploadAreaFile.ondrop = function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('active');
        console.log('📥 Drop detectado');
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            handleFile(e.dataTransfer.files[0]);
        }
    };
    
    // Contador
    if (fileDescription) {
        fileDescription.oninput = function() {
            if (fileCharCount) fileCharCount.textContent = this.value.length;
        };
    }
    
    // Remover archivo
    btnRemoveFile.onclick = function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('🗑️ Removiendo archivo');
        clearFile();
    };
    
    function clearFile() {
        selectedFile = null;
        fileInput.value = '';
        previewContainerFile.style.display = 'none';
        uploadAreaFile.style.display = 'block';
        fileTextContainer.style.display = 'none';
        fileDescription.value = '';
        fileCharCount.textContent = '0';
        btnPublishFile.disabled = true;
    }
    
    function formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        var k = 1024;
        var sizes = ['Bytes', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    function handleFile(file) {
        console.log('📎 Procesando:', file.name, file.size, file.type);
        
        if (file.size > 10 * 1024 * 1024) {
            alert('❌ Archivo muy grande. Máximo 10MB.');
            return;
        }
        
        var validExt = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar'];
        var ext = file.name.split('.').pop().toLowerCase();
        
        if (validExt.indexOf(ext) === -1) {
            alert('❌ Tipo de archivo no permitido. Solo PDF, DOC, XLS, PPT, ZIP.');
            return;
        }
        
        selectedFile = file;
        console.log('✅ Archivo válido');
        
        // Mostrar preview
        fileNamePreview.textContent = file.name;
        fileSizePreview.textContent = formatBytes(file.size);
        fileTypePreview.textContent = ext.toUpperCase();
        
        // Iconos según tipo
        var iconMap = {
            'pdf': '📄',
            'doc': '📘', 'docx': '📘',
            'xls': '📊', 'xlsx': '📊',
            'ppt': '📽️', 'pptx': '📽️',
            'zip': '📦', 'rar': '📦'
        };
        
        fileIconPreview.textContent = iconMap[ext] || '📎';
        
        console.log('🖼️ Mostrando preview...');
        previewContainerFile.style.display = 'block';
        uploadAreaFile.style.display = 'none';
        fileTextContainer.style.display = 'block';
        btnPublishFile.disabled = false;
        console.log('✅ Preview mostrado, botón habilitado');
    }
    
    // Publicar
    btnPublishFile.onclick = function() {
        console.log('🚀 Publicando archivo...');
        
        if (!selectedFile) {
            alert('❌ Selecciona un archivo primero');
            return;
        }
        
        console.log('📤 Preparando envío...');
        
        loadingOverlayFile.classList.add('active');
        btnPublishFile.disabled = true;
        
        var formData = new FormData();
        formData.append('archivo', selectedFile);
        
        var description = fileDescription.value.trim();
        if (description) {
            formData.append('titulo', description.substring(0, 100));
            formData.append('contenido', description);
        } else {
            formData.append('titulo', 'Publicación con archivo');
            formData.append('contenido', 'Publicación con archivo');
        }
        
        formData.append('categoriaGeneral', '15');
        
        console.log('📡 Enviando...');
        
        fetch('../compartido/noticias-guardar.php', {
            method: 'POST',
            body: formData
        })
        .then(function(response) {
            console.log('✅ Respuesta recibida');
            return response.text();
        })
        .then(function(data) {
            console.log('✅ Éxito:', data);
            loadingOverlayFile.classList.remove('active');
            cerrarModal_nuevoArchivo();
            setTimeout(function() {
                window.location.reload();
            }, 300);
        })
        .catch(function(error) {
            console.error('❌ Error:', error);
            loadingOverlayFile.classList.remove('active');
            btnPublishFile.disabled = false;
            alert('❌ Error al subir. Intenta de nuevo.');
        });
    };
    
    console.log('✅ Modal de archivo inicializado correctamente');
})();
">